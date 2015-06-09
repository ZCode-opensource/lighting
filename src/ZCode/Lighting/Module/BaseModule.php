<?php

/*
 * This file is part of the ZCode Lighting Web Framework.
 *
 * (c) Álvaro Somoza <asomoza@zcode.cl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZCode\Lighting\Module;

use ZCode\Lighting\Http\ServerInfo;
use ZCode\Lighting\Object\BaseObject;

class BaseModule extends BaseObject
{
    public $moduleName;
    public $databases;
    public $request;
    public $session;
    public $serverInfo;
    public $ajax;
    public $projectNamespace;
    public $moduleCssList;
    public $moduleJsList;

    private $controller;
    private $resourcePath;

    public function setModuleName($name)
    {
        $this->moduleName = $name;
        $this->moduleCssList = array();
        $this->moduleJsList = array();
    }

    private function moduleInit()
    {
        $class = $this->projectNamespace.'\Modules\\'.$this->moduleName.'\Controller';

        try {
            $rClass = new \ReflectionClass($class);
        } catch (\ReflectionException $ex) {
            // TODO: Generate error
            $rClass = null;
        }

        $this->resourcePath  = 'src/'.str_replace('\\', '/', $this->projectNamespace);
        $this->resourcePath .= '/Modules/'.$this->moduleName.'/resources/';

        $this->controller                   = $rClass->newInstance($this->logger);
        $this->controller->databases        = $this->databases;
        $this->controller->request          = $this->request;
        $this->controller->serverInfo       = $this->serverInfo;
        $this->controller->session          = $this->session;
        $this->controller->projectNamespace = $this->projectNamespace;
        $this->controller->resourcePath     = $this->resourcePath;
        $this->controller->moduleName       = $this->moduleName;
    }

    public function getResponse()
    {
        $this->moduleInit();

        if ($this->ajax) {
            $this->controller->runAjax();
            return $this->controller->response;
        }

        $this->controller->run();

        // process the global css files
        $numCss = sizeof($this->controller->globalCssList);
        if ($numCss > 0) {
            for ($i = 0; $i < $numCss; $i++) {
                $this->addCss($this->controller->globalCssList[$i], true);
            }
        }

        // process the module css files
        $numCss = sizeof($this->controller->cssList);
        if ($numCss > 0) {
            for ($i = 0; $i < $numCss; $i++) {
                $this->addCss($this->controller->cssList[$i], false);
            }
        }

        // process the global js files
        $numJs = sizeof($this->controller->globalJsList);
        if ($numJs > 0) {
            for ($i = 0; $i < $numJs; $i++) {
                $this->addJs($this->controller->globalJsList[$i], true);
            }
        }

        // process the module js files
        $numJs = sizeof($this->controller->jsList);
        if ($numJs > 0) {
            for ($i = 0; $i < $numJs; $i++) {
                $this->addJs($this->controller->jsList[$i], false);
            }
        }

        return $this->controller->response;
    }

    private function addCss($filename, $global)
    {
        $base = $this->serverInfo->getData(ServerInfo::BASE_URL).$this->resourcePath.'css/';

        if ($global) {
            $base = $this->serverInfo->getData(ServerInfo::BASE_URL).'resources/css/';
        }

        $css = $base.$filename.'.css';
        $this->moduleCssList[] = $css;
    }

    private function addJs($filename, $global)
    {
        $base = $this->serverInfo->getData(ServerInfo::BASE_URL).$this->resourcePath.'js/';

        if ($global) {
            $base = $this->serverInfo->getData(ServerInfo::BASE_URL).'resources/js/';
        }

        $jsc = $base.$filename.'.js';
        $this->moduleJsList[] = $jsc;
    }
}
