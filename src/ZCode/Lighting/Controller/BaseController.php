<?php

/*
 * This file is part of the ZCode Lighting Web Framework.
 *
 * (c) Álvaro Somoza <asomoza@zcode.cl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZCode\Lighting\Controller;

use ZCode\Lighting\Factory\ProjectFactory;
use ZCode\Lighting\Factory\WidgetFactory;
use ZCode\Lighting\Http\ServerInfo;
use ZCode\Lighting\Object\BaseObject;
use ZCode\Lighting\Template\Template;

abstract class BaseController extends BaseObject
{
    public $request;
    public $response;
    public $serverInfo;
    public $session;
    public $resourcePath;
    public $moduleName;
    public $databases;
    public $priorityCssList;
    public $cssList;
    public $jsList;
    public $priorityJsList;

    abstract public function run();
    abstract public function runAjax();

    public function init()
    {
        $this->priorityCssList = [];
        $this->cssList         = [];
        $this->priorityJsList  = [];
        $this->jsList          = [];

        $this->response = '';
    }

    public function getTemplate($filename, $path)
    {
        $docRoot = $this->serverInfo->getData(ServerInfo::DOC_ROOT);

        $tmpl = new Template($this->logger);
        $tmpl->loadTemplate($filename, $docRoot.'/'.$path);

        return $tmpl;
    }

    protected function createView($name)
    {
        $view = $this->getObject(ProjectFactory::VIEW, $name);

        if ($view) {
            $view->serverInfo           = $this->serverInfo;
            $view->resourcePath         = $this->resourcePath;
            $view->templateFunction     = array($this, 'getTemplate');
            $view->addCssFunction       = array($this, 'addCss');
            $view->addJsFunction        = array($this, 'addJs');
            $view->createWidgetFunction = array($this, 'createWidget');
        }

        return $view;
    }

    public function createWidget($name)
    {
        $projectNameSpace = $this->serverInfo->getData(ServerInfo::PROJECT_NAMESPACE);
        $factory = new WidgetFactory($this->logger);

        $factory->basePath = $projectNameSpace.'\Widgets\\'.$name;
        $widget            = $factory->create($name);

        $widget->widgetName       = $name;
        $widget->resourcePath     = 'src/'.str_replace('\\', '/', $factory->basePath).'/resources/';
        $widget->serverInfo       = $this->serverInfo;
        $widget->templateFunction = array($this, 'getTemplate');
        $widget->addCssFunction   = array($this, 'addPriorityCss');
        $widget->addJsFunction    = array($this, 'addPriorityJs');

        return $widget;
    }

    protected function createModel($name)
    {
        $model = $this->getObject(ProjectFactory::MODEL, $name);

        if ($model) {
            $model->setDatabases($this->databases);
        }

        return $model;
    }

    protected function createController($name)
    {
        $controller = $this->getObject(ProjectFactory::CONTROLLER, $name);

        if ($controller) {
            $controller->databases        = $this->databases;
            $controller->request          = $this->request;
            $controller->serverInfo       = $this->serverInfo;
            $controller->session          = $this->session;
            $controller->resourcePath     = $this->resourcePath;
            $controller->moduleName       = $this->moduleName;
        }

        return $controller;
    }

    private function getObject($type, $name)
    {
        $projectNameSpace = $this->serverInfo->getData(ServerInfo::PROJECT_NAMESPACE);

        $factory = new ProjectFactory($this->logger);
        $factory->basePath = $projectNameSpace.'\Modules\\'.$this->moduleName;
        $object  = $factory->create($type, $name);

        return $object;
    }

    public function addCss($file)
    {
        if (strlen($file) > 0) {
            $this->cssList[] = $file.'.css';
        }
    }

    public function addPriorityCss($file)
    {
        if (strlen($file) > 0) {
            $this->priorityCssList[] = $file.'.css';
        }
    }

    public function addJs($file)
    {
        if (strlen($file) > 0) {
            $this->jsList[] = $file.'.js';
        }
    }

    public function addPriorityJs($file)
    {
        if (strlen($file) > 0) {
            $this->priorityJsList[] = $file.'.js';
        }
    }

    protected function generateJsonResponse($success, $message, $data)
    {
        $jsonArray = array('success' => $success, 'message' => $message);

        if ($data && is_array($data)) {
            foreach ($data as $key => $value) {
                $jsonArray[$key] = $value;
            }
        }

        $json = json_encode($jsonArray, JSON_UNESCAPED_UNICODE);
        $this->response = $json;
    }
}
