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
        $this->reponse    = $this->mainFactory->create(MainFactory::RESPONSE);
        $this->serverInfo = $this->mainFactory->create(MainFactory::SERVER_INFO);

        $relativePath = $this->config->getConfig('site', 'relative_path', false);
        $this->serverInfo->setRelativePath($relativePath);
    }

    public function render()
    {
        $this->session = $this->mainFactory->create(MainFactory::SESSION);
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
}
