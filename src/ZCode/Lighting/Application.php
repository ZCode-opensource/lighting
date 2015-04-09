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
        $this->response    = $this->mainFactory->create(MainFactory::RESPONSE);
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
        $response = '';

        $moduleFactory             = new ModuleFactory($this->logger);
        $moduleFactory->request    = $this->request;
        $moduleFactory->serverInfo = $this->serverInfo;
        $moduleFactory->session    = $this->session;
        $moduleFactory->ajax       = $ajax;

        $moduleFactory->projectNamespace = $this->config->getConfig('application', 'project_namespace', false);
        $mainModule = $moduleFactory->create(ModuleFactory::MODULE);

        $mainModule->setModuleName($module);
        $response = $mainModule->getResponse();

        return $response;
    }

    private function renderResponse($response, $ajax)
    {
        $baseUrl = $this->serverInfo->getData(ServerInfo::BASE_URL);

        $tmpl = new Template($this->logger);
        $tmpl->loadTemplate('main', 'resources/html');

        $tmpl->addSearchReplace('{#BASE_URL#}', $baseUrl);

        $this->response->html($tmpl->getHtml());
    }
}
