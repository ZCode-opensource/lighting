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
use Monolog\Handler\StreamHandler;
use ZCode\Lighting\Factory\ModuleFactory;
use ZCode\Lighting\Http\Request;
use ZCode\Lighting\Http\ServerInfo;
use ZCode\Lighting\Template\Template;

class Application
{
    private $error;
    private $config;
    private $mainFactory;
    private $request;
    private $response;
    private $serverInfo;
    private $logger;
    private $session;
    private $module;

    public function __construct()
    {
        $this->error      = true;
        $this->config     = new Configuration('framework.conf');
        
        $this->logger   = new Logger('Main');
        $logLevel       = $this->getLogLevel();
        
        $this->logger->pushHandler(new StreamHandler('app.log', $logLevel));

        if ($this->config->error) {
            // TODO: Make an error showing system
            $this->logger->addError("Couldn't load configuration file");
            return;
        }

        $displayErrors = $this->getDisplayErrors();
        ini_set('display_errors', $displayErrors);

        $this->mainFactory = new MainFactory($this->logger);

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

        // convert first letter to uppercase
        $module = ucwords($module);

        $this->session->setModule($module);
        $ajax            = $this->request->getVar('ajax', Request::BOOLEAN);
        $moduleResponse  = $this->generateModuleResponse($module, $ajax);

        $this->renderResponse($moduleResponse, $ajax);
    }

    private function getLogLevel()
    {
        $logLevelValue = Logger::DEBUG;

        return $logLevelValue;
    }

    private function getDisplayErrors()
    {
        $displayErrors = $this->config->getConfig(
            'application',
            'show_errors',
            true
        );

        $errorVar = '1';

        if ($displayErrors) {
            $errorVar = '1';
        }

        return $errorVar;
    }

    private function generateModuleResponse($module, $ajax)
    {
        // create the databases if needed
        $useDatabase = $this->config->getConfig('database', 'use_database', true);
        $databases   = array();

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
                    $connectionName            = $this->config->getConfig($dbSection, 'name', false);
                    $databaseFactory->server   = $this->config->getConfig($dbSection, 'server', false);
                    $databaseFactory->user     = $this->config->getConfig($dbSection, 'user', false);
                    $databaseFactory->password = $this->config->getConfig($dbSection, 'password', false);
                    $databaseFactory->database = $this->config->getConfig($dbSection, 'database', false);

                    $databases[$connectionName] = $databaseFactory->create($dbType);
                }
            }
        }

        $moduleFactory             = new ModuleFactory($this->logger);
        $moduleFactory->databases  = $databases;
        $moduleFactory->request    = $this->request;
        $moduleFactory->serverInfo = $this->serverInfo;
        $moduleFactory->session    = $this->session;
        $moduleFactory->ajax       = $ajax;

        $moduleFactory->projectNamespace = $this->config->getConfig('application', 'project_namespace', false);
        $this->module = $moduleFactory->create(ModuleFactory::MODULE);

        $this->module->setModuleName($module);
        $response = $this->module->getResponse();

        return $response;
    }

    private function renderResponse($response, $ajax)
    {
        if ($ajax) {
            $this->response->html($response);
            return;
        }

        $baseUrl = $this->serverInfo->getData(ServerInfo::BASE_URL);

        $tmpl = new Template($this->logger);
        $tmpl->loadTemplate('main', 'resources/html');

        $tmpl->addSearchReplace('{#MODULE#}', $response);
        $tmpl->addSearchReplace('{#BASE_URL#}', $baseUrl);

        $cssString = $this->processModuleCss($this->module->moduleCssList);
        $tmpl->addSearchReplace('{#CSS#}', $cssString);

        $jsString = $this->processModuleJs($this->module->moduleJsList);
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
