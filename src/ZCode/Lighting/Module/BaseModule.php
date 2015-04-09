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
    public $request;
    public $session;
    public $serverInfo;
    public $ajax;
    public $projectNamespace;

    private $controller;

    public function setModuleName($name)
    {
        $this->moduleName = $name;
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

        $this->controller                   = $rClass->newInstance($this->logger);
        $this->controller->request          = $this->request;
        $this->controller->serverInfo       = $this->serverInfo;
        $this->controller->session          = $this->session;
        $this->controller->projectNamespace = $this->projectNamespace;
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

        return $this->controller->response;
    }
}