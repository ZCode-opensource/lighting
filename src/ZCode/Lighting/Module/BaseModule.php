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

use ZCode\Lighting\Controller\BaseController;
use ZCode\Lighting\Factory\ProjectFactory;
use ZCode\Lighting\Http\Request;
use ZCode\Lighting\Http\ServerInfo;
use ZCode\Lighting\Object\BaseObject;
use ZCode\Lighting\Session\Session;

class BaseModule extends BaseObject
{
    /** @var  string */
    public $moduleName;

    /** @var  array */
    public $databases;

    /** @var  Request */
    public $request;

    /** @var  Session */
    public $session;

    /** @var  ServerInfo */
    public $serverInfo;

    /** @var  ProjectFactory Factory for the internal objects of the controller */
    public $projectFactory;

    /** @var  boolean */
    public $ajax;

    /** @var  array */
    public $moduleCssList;

    /** @var  array */
    public $moduleJsList;

    /** @var  BaseController */
    private $controller;

    /** @var  string */
    private $resourcePath;

    public function setModuleName($name)
    {
        $this->moduleName = $name;
        $this->moduleCssList = [];
        $this->moduleJsList = [];
    }

    private function moduleInit($errorModule)
    {
        $projectNameSpace = $this->serverInfo->getData(ServerInfo::PROJECT_NAMESPACE);

        // Load module
        $moduleLoaded = $this->moduleName;
        $rClass       = $this->getModuleClass($this->moduleName, $projectNameSpace);

        // If null, load error module (that could also be the default module)
        if ($rClass === null) {
            $this->logger->addDebug('Trying to load error module: '.$errorModule);
            $rClass       = $this->getModuleClass($errorModule, $projectNameSpace);
            $moduleLoaded = $errorModule;
        }

        // If still null, return false
        if ($rClass === null) {
            return false;
        }

        $this->logger->addDebug('Module loaded: '.$moduleLoaded);

        $this->resourcePath  = 'src/'.str_replace('\\', '/', $projectNameSpace);
        $this->resourcePath .= '/Modules/'.$moduleLoaded.'/resources/';

        $this->controller                   = $rClass->newInstance($this->logger);
        $this->controller->databases        = $this->databases;
        $this->controller->request          = $this->request;
        $this->controller->serverInfo       = $this->serverInfo;
        $this->controller->session          = $this->session;
        $this->controller->resourcePath     = $this->resourcePath;
        $this->controller->projectFactory   = $this->projectFactory;
        $this->controller->moduleName       = $moduleLoaded;

        return true;
    }

    private function getModuleClass($module, $projectNameSpace)
    {
        $this->logger->debug('Loading module: '.$module);
        $class = $projectNameSpace.'\Modules\\'.$module.'\\'.$module.'Controller';

        try {
            $rClass = new \ReflectionClass($class);
        } catch (\ReflectionException $ex) {
            $this->logger->addError('Failed to load module: '.$module);
            $rClass = null;
        }

        return $rClass;
    }

    public function getResponse($errorModule)
    {
        if (!$this->moduleInit($errorModule)) {
            $this->logger->addError('Could not load any module.');
            return '';
        }

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
