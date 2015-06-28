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
        $projectNameSpace = $this->serverInfo->getData(ServerInfo::PROJECT_NAMESPACE);
        $class            = $projectNameSpace.'\Modules\\'.$this->moduleName.'\Controller';

        try {
            $rClass = new \ReflectionClass($class);
        } catch (\ReflectionException $ex) {
            // TODO: Generate error
            $rClass = null;
        }

        $this->resourcePath  = 'src/'.str_replace('\\', '/', $projectNameSpace);
        $this->resourcePath .= '/Modules/'.$this->moduleName.'/resources/';

        $this->controller                   = $rClass->newInstance($this->logger);
        $this->controller->databases        = $this->databases;
        $this->controller->request          = $this->request;
        $this->controller->serverInfo       = $this->serverInfo;
        $this->controller->session          = $this->session;
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

        // process the css files
        $this->moduleCssList = array_merge($this->controller->priorityCssList, $this->controller->cssList);

        // process the js files
        $this->moduleJsList = array_merge($this->controller->priorityJsList, $this->controller->jsList);

        return $this->controller->response;
    }
}
