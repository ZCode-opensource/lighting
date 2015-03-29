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
use ZCode\Lighting\Http\Request;
use ZCode\Lighting\Http\Response;
use ZCode\Lighting\Http\ServerInfo;
use ZCode\Lighting\Session\Session;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Application
{
    private $error;
    private $config;    
    private $request;
    private $response;
    private $serverInfo;
    private $logger;

    public function __construct()
    {           
        $this->error      = true;
        $this->config     = new Configuration('framework.conf');
        
        $logger   = new Logger('Main');
        $logLevel = Logger::DEBUG;
        
        $logger->pushHandler(new StreamHandler('app.log', $logLevel));
                
        $this->request    = new Request();
        $this->reponse    = new Response();
        $this->serverInfo = new ServerInfo(
            $this->config->getConfig('site', 'relative_path', false)
        );

        if ($this->config->error) {
            // TODO: Make an error showing system
            return;
        }

        $displayErrors = $this->getDisplayErrors();
        ini_set('display_errors', $displayErrors);
    }

    public function render()
    {
        $this->sesion = new Session();
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
