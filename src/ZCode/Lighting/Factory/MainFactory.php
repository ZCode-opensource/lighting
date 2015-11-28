<?php

/*
 * This file is part of the ZCode Lighting Web Framework.
 *
 * (c) Álvaro Somoza <asomoza@zcode.cl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZCode\Lighting\Factory;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use ZCode\Lighting\Configuration\Configuration;
use ZCode\Lighting\Http\Request;
use ZCode\Lighting\Http\Response;
use ZCode\Lighting\Http\ServerInfo;
use ZCode\Lighting\Session\Session;

class MainFactory extends BaseFactory
{
    const REQUEST  = 0;
    const RESPONSE = 1;
    const SERVER_INFO = 2;
    const SESSION = 3;
    const MODULE_FACTORY = 4;
    const TEMPLATE_FACTORY = 5;

    private $config;

    protected function init()
    {
        $this->classArray = [
            'Http\Request',
            'Http\Response',
            'Http\ServerInfo',
            'Session\Session',
            'Factory\ModuleFactory',
            'Factory\TemplateFactory'
        ];
    }

    public function setConfigurationFile($configFile)
    {
        $this->config = new Configuration($configFile);

        if ($this->logger !== null) {
            $logDir   = $this->config->getConfig('log', 'log_dir');
            $logFile  = $this->config->getConfig('log', 'log_file');
            $logLevel = $this->getLogLevel();

            if ($logDir !== null && $logFile !== null) {
                // check if the file is created
                if (!file_exists('app.log')) {
                    fopen($logDir.$logFile, "w");
                }

                if (is_writable($logDir.$logFile)) {
                    $this->logger->pushHandler(new StreamHandler('app.log', $logLevel));
                    $this->logger->addDebug('Logging system ready, MainFactory created.');
                } else {
                    if ($logLevel === Logger::DEBUG) {
                        echo "Log file is not writable.";
                    }
                }

            } else {
                if ($logLevel === Logger::DEBUG) {
                    echo "Log file and log directory configuration not found.";
                }
            }
        }
    }

    public function getConfiguration()
    {
        return $this->config;
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     *
     * @param Request|Response|ServerInfo|Session|ModuleFactory|TemplateFactory $object
     *
     * @return Request|Response|ServerInfo|Session|ModuleFactory|TemplateFactory
     */
    protected function additionalSetup($object)
    {
        if (get_class($object) === 'ZCode\Lighting\Http\Request') {
            $object->initializeRequest($_POST, $_GET, $_SERVER['REQUEST_URI']);
        }

        return $object;
    }

    public function getLogger()
    {
        return $this->logger;
    }

    private function getLogLevel()
    {
        $logLevel      = $this->config->getConfig('log', 'log_level');
        $logLevelValue = Logger::ERROR;

        switch ($logLevel) {
            case 'debug':
                $logLevelValue = Logger::DEBUG;
                break;
            case 'info':
                $logLevelValue = Logger::INFO;
                break;
            case 'notice':
                $logLevelValue = Logger::NOTICE;
                break;
            case 'warning':
                $logLevelValue = Logger::WARNING;
                break;
            case 'error':
                $logLevelValue = Logger::ERROR;
                break;
            case 'critical':
                $logLevelValue = Logger::CRITICAL;
                break;
            case 'emergency':
                $logLevelValue = Logger::EMERGENCY;
                break;
        }

        return $logLevelValue;
    }
}
