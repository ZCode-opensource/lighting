<?php

/*
 * This file is part of the ZCode Lighting Web Framework.
 *
 * (c) Álvaro Somoza <asomoza@zcode.cl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZCode\Lighting;

use ZCode\Lighting\Configuration\Configuration;
use ZCode\Lighting\Factory\DatabaseFactory;
use ZCode\Lighting\Factory\MainFactory;

use Monolog\Logger;
use ZCode\Lighting\Factory\ModuleFactory;
use ZCode\Lighting\Factory\TemplateFactory;
use ZCode\Lighting\Http\Request;
use ZCode\Lighting\Http\Response;
use ZCode\Lighting\Http\ServerInfo;
use ZCode\Lighting\Module\BaseModule;
use ZCode\Lighting\Session\Session;

class Application
{
    private $error;

    /** @var  Configuration */
    private $config;

    /** @var MainFactory */
    private $mainFactory;

    /** @var Request */
    private $request;

    /** @var Response */
    private $response;

    /** @var ServerInfo */
    private $serverInfo;

    /** @var Logger */
    private $logger;

    /** @var  Session */
    private $session;

    /** @var  BaseModule */
    private $module;

    /** @var  BaseModule */
    private $menuModule;

    /** @var  BaseModule */
    private $footerModule;

    public function __construct($mainFactory)
    {
        $this->error  = true;

        $this->mainFactory = $mainFactory;
        $this->config      = $this->mainFactory->getConfiguration();
        $this->logger      = $this->mainFactory->getLogger();

        $this->logger->addInfo('Logger initialized.');
        $this->logger->addInfo('Initializing application.');

        if ($this->config->error) {
            // TODO: Make an error showing system
            $this->logger->addError("Couldn't load configuration file");
            return;
        }

        $displayErrors = $this->getDisplayErrors();
        $this->logger->addDebug('Setting PHP diplay errors to: '.$displayErrors);
        ini_set('display_errors', $displayErrors);

        $this->request    = $this->mainFactory->create(MainFactory::REQUEST);
        $this->response   = $this->mainFactory->create(MainFactory::RESPONSE);
        $this->serverInfo = $this->mainFactory->create(MainFactory::SERVER_INFO);

        $relativePath = $this->config->getConfig('site', 'relative_path', false);
        $this->serverInfo->setRelativePath($relativePath);
    }

    public function render()
    {
        $this->session = $this->mainFactory->create(MainFactory::SESSION);
        $module        = $this->request->getPostVar('module', Request::STRING);

        if (!$module) {
            $internalPath = $this->config->getConfig('site', 'internal_path', false);
            $module       = $this->request->getModule(
                $internalPath,
                $this->serverInfo->getData(ServerInfo::RELATIVE_PATH)
            );
        }

        if ($module == 'logout') {
            $this->session->cleanSession();
            $baseUrl = $this->serverInfo->getData(ServerInfo::BASE_URL);
            header('Location: '.$baseUrl);
            return;
        }

        if (!$module) {
            $module = $this->config->getConfig('application', 'default_module', false);
        }

        // Auth section
        $loginModule = $this->config->getConfig('auth', 'login_module', false);
        if ($this->validateAuth($module)) {
            $module = $loginModule;
        }

        // convert first letter to uppercase
        $module = ucwords($module);

        // xdebug configuration
        $xdebug = 2;

        $this->serverInfo->setData(ServerInfo::MODULE, $module);
        $ajax = $this->request->getVar('ajax', Request::BOOLEAN);
        $this->request->unsetVar('ajax');

        if ($ajax) {
            $xdebug = 0;
        }

        ini_set('xdebug.overload_var_dump', $xdebug);

        $moduleResponse = $this->generateModuleResponse($module, $ajax);
        $this->renderResponse($moduleResponse, $ajax);
    }

    private function validateAuth($module)
    {
        $requireAuth = $this->config->getConfig('application', 'auth', true);

        if (!$requireAuth) {
            return false;
        }

        $userAuth    = $this->session->getVar('auth', true);
        $authModules = $this->config->getConfig('auth', 'modules', false);

        if ($authModules === '*') {
            if (!$userAuth) {
                return true;
            }
        }

        $modules = explode(',', $authModules);
        $numModules = sizeof($modules);

        if ($numModules > 0) {
            for ($i = 0; $i < $numModules; $i++) {
                if (strtolower($modules[$i]) === strtolower($module)) {
                    if (!$userAuth) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    private function getDisplayErrors()
    {
        $displayErrors = $this->config->getConfig(
            'application',
            'show_errors',
            true
        );

        $errorVar = '0';

        if ($displayErrors) {
            $errorVar = '1';
        }

        return $errorVar;
    }

    private function generateModuleResponse($module, $ajax)
    {
        // create the databases if needed
        $useDatabase = $this->config->getConfig('database', 'use_database', true);
        $databases   = [];

        if ($useDatabase) {
            $databaseFactory = new DatabaseFactory($this->logger);
            $databaseCount   = intval($this->config->getConfig('database', 'number_databases', false));

            for ($i = 0; $i < $databaseCount; $i++) {
                $dbSection  = 'database_'.($i + 1);
                $dbConfType = $this->config->getConfig($dbSection, 'database_type', false);

                switch ($dbConfType) {
                    case 'mysql':
                        $dbType = DatabaseFactory::MYSQL;
                        break;
                    default:
                        $dbType = null;
                        break;
                }

                if ($dbType !== null) {
                    $connectionName                = $this->config->getConfig($dbSection, 'name');
                    $databaseFactory->server       = $this->config->getConfig($dbSection, 'server');
                    $databaseFactory->user         = $this->config->getConfig($dbSection, 'user');
                    $databaseFactory->password     = $this->config->getConfig($dbSection, 'password');
                    $databaseFactory->database     = $this->config->getConfig($dbSection, 'database');
                    $databaseFactory->forceCharset = $this->config->getConfig($dbSection, 'force_charset', true);
                    $databaseFactory->charset      = $this->config->getConfig($dbSection, 'charset');

                    $databases[$connectionName] = $databaseFactory->create($dbType);
                }
            }
        }

        $moduleFactory = $this->mainFactory->create(MainFactory::MODULE_FACTORY);
        $moduleFactory->databases  = $databases;
        $moduleFactory->request    = $this->request;
        $moduleFactory->serverInfo = $this->serverInfo;
        $moduleFactory->session    = $this->session;
        $moduleFactory->ajax       = $ajax;

        $this->serverInfo->setData(
            ServerInfo::PROJECT_NAMESPACE,
            $this->config->getConfig('application', 'project_namespace', false)
        );

        $this->module = $moduleFactory->create(ModuleFactory::MODULE);
        $this->module->setModuleName($module);
        $response = $this->module->getResponse();

        // Generate menu module if needed
        $generateMenu = $this->config->getConfig('menu', 'generate_menu', true);

        if ($generateMenu) {
            $menuModule = $this->config->getConfig('menu', 'menu_module', false);
            $this->menuModule = $moduleFactory->create(ModuleFactory::MODULE);
            $this->menuModule->setModuleName($menuModule);
        }

        // Generate footer module if needed
        $genrateFooter = $this->config->getConfig('footer', 'generate_footer', true);

        if ($genrateFooter) {
            $footerModule = $this->config->getConfig('footer', 'footer_module', false);
            $this->footerModule = $moduleFactory->create(ModuleFactory::MODULE);
            $this->footerModule->setModuleName($footerModule);
        }

        return $response;
    }

    private function renderResponse($response, $ajax)
    {
        if ($ajax) {
            $this->response->json($response);
            return;
        }

        $baseUrl = $this->serverInfo->getData(ServerInfo::BASE_URL);

        /** @var TemplateFactory $templateFactory */
        $templateFactory = $this->mainFactory->create(MainFactory::TEMPLATE_FACTORY);
        $tmpl = $templateFactory->create(TemplateFactory::TEMPLATE);
        $tmpl->loadTemplate('main', 'resources/html');

        $pageTitle = $this->config->getConfig('site', 'page_title', false);
        $tmpl->addSearchReplace('{#PAGE_TITLE#}', $pageTitle);

        $cssString = '';
        $jsString  = '';

        // Generate Menu
        $menu = '';

        if ($this->menuModule) {
            $menu      = $this->menuModule->getResponse();
            $cssString = $this->processModuleCss($this->menuModule->moduleCssList);
            $jsString  = $this->processModuleJs($this->menuModule->moduleJsList);
        }

        $tmpl->addSearchReplace('{#MENU#}', $menu);

        // Generate Footer
        $footer = '';

        if ($this->footerModule) {
            $footer     = $this->footerModule->getResponse();
            $cssString .= $this->processModuleCss($this->footerModule->moduleCssList);
            $jsString  .= $this->processModuleJs($this->footerModule->moduleJsList);
        }

        $tmpl->addSearchReplace('{#FOOTER#}', $footer);

        $tmpl->addSearchReplace('{#MODULE#}', $response);
        $tmpl->addSearchReplace('{#BASE_URL#}', $baseUrl);

        $cssString .= $this->processModuleCss($this->module->moduleCssList);
        $tmpl->addSearchReplace('{#CSS#}', $cssString);

        $jsString .= $this->processModuleJs($this->module->moduleJsList);
        $tmpl->addSearchReplace('{#JS#}', $jsString);

        $this->response->html($tmpl->getHtml());
    }

    private function processModuleCss(array $moduleCssList)
    {
        $cssString = '';
        $numCss    = sizeof($moduleCssList);

        if ($numCss > 0) {
            for ($i = 0; $i < $numCss; $i++) {
                $cssString .= '<link rel="stylesheet" href="'.$moduleCssList[$i].'" />';
            }
        }

        return $cssString;
    }

    private function processModuleJs(array $moduleJsList)
    {
        $jsString = '';
        $numJs    = sizeof($moduleJsList);

        if ($numJs > 0) {
            for ($i = 0; $i < $numJs; $i++) {
                $jsString .= '<script src="'.$moduleJsList[$i].'"></script>';
            }
        }

        return $jsString;
    }
}
