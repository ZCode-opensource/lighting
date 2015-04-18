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

        // process the css
        $numCss = sizeof($this->controller->cssList);
        if ($numCss > 0) {
            $this->processCss($this->controller->cssList, $numCss);
        }

        // process the js
        $numJs = sizeof($this->controller->jsList);
        if ($numJs > 0) {
            $this->processJs($this->controller->jsList, $numJs);
        }

        return $this->controller->response;
    }

    private function processCss($controllerCssList, $numCss)
    {
        for ($i = 0; $i < $numCss; $i++) {
            $this->addCss($controllerCssList[$i]);
        }
    }

    private function addCss($filename)
    {
        $css = $this->resourcePath.'css/'.$filename.'.css';
        $this->moduleCssList[] = $css;
    }

    private function processJs($controllerJsList, $numJs)
    {
        for ($i = 0; $i < $numJs; $i++) {
            $this->addJs($controllerJsList[$i]);
        }
    }

    private function addJs($filename)
    {
        $jsc = $this->resourcePath.'js/'.$filename.'.js';
        $this->moduleJsList[] = $jsc;
    }
}
