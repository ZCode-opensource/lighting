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

use ZCode\Lighting\Database\DatabaseProvider;
use ZCode\Lighting\Factory\ProjectFactory;
use ZCode\Lighting\Factory\WidgetFactory;
use ZCode\Lighting\Http\Request;
use ZCode\Lighting\Http\Response;
use ZCode\Lighting\Http\ServerInfo;
use ZCode\Lighting\Model\BaseModel;
use ZCode\Lighting\Object\BaseObject;
use ZCode\Lighting\Session\Session;
use ZCode\Lighting\Template\Template;

abstract class BaseController extends BaseObject
{
    /** @var  Request Request object*/
    public $request;

    /** @var  Response Response object*/
    public $response;

    /** @var  ServerInfo ServerInfo object*/
    public $serverInfo;

    /** @var  Session Session object */
    public $session;

    /** @var  ProjectFactory Factory for the internal objects of the controller */
    public $projectFactory;

    /** @var  WidgetFactory Factory for creating widgets */
    public $widgetFactory;

    /** @var  DatabaseProvider[] Array of databases created from the configuration file. */
    public $databases;

    /** @var  string path to the resource directory */
    public $resourcePath;

    /** @var  string Name of the actual module in use */
    public $moduleName;

    public $priorityCssList;
    public $priorityJsList;

    public $cssList;
    public $jsList;

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
            $view->templateFunction     = [$this, 'getTemplate'];
            $view->addCssFunction       = [$this, 'addCss'];
            $view->addJsFunction        = [$this, 'addJs'];
            $view->createWidgetFunction = [$this, 'createWidget'];
        }

        return $view;
    }

    protected function createModel($name)
    {
        $model = $this->getObject(ProjectFactory::MODEL, $name);
        $model = $this->seedModel($model);

        return $model;
    }

    protected function createController($name)
    {
        $controller = $this->getObject(ProjectFactory::CONTROLLER, $name);
        $controller = $this->seedController($controller);

        return $controller;
    }

    protected function createWidget($name)
    {
        $projectNameSpace = $this->serverInfo->getData(ServerInfo::PROJECT_NAMESPACE);

        $this->widgetFactory->basePath = $projectNameSpace.'\Widgets\\'.$name;
        $widget                        = $this->widgetFactory->create($name);

        $widget->widgetName       = $name;
        $widget->resourcePath     = 'src/'.str_replace('\\', '/', $this->widgetFactory->basePath).'/resources/';
        $widget->serverInfo       = $this->serverInfo;
        $widget->templateFunction = [$this, 'getTemplate'];
        $widget->addCssFunction   = [$this, 'addPriorityCss'];
        $widget->addJsFunction    = [$this, 'addPriorityJs'];

        return $widget;
    }

    protected function createCustomModel($name, $path)
    {
        $model = $this->getCustomObject($name, '\\Models\\'.$path);
        $model = $this->seedModel($model);

        return $model;
    }

    protected function createCustomController($name, $path)
    {
        $controller = $this->getCustomObject($name, '\\Controllers\\'.$path);
        $controller = $this->seedController($controller);

        return $controller;
    }

    /**
     * @param $model BaseModel
     * @return BaseModel
     */
    private function seedModel($model)
    {
        if ($model) {
            $model->setDatabases($this->databases);
        }

        return $model;
    }

    /**
     * @param $controller BaseController
     * @return BaseController
     */
    private function seedController($controller)
    {
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
        $projectNameSpace               = $this->serverInfo->getData(ServerInfo::PROJECT_NAMESPACE);
        $this->projectFactory->basePath = $projectNameSpace.'\Modules\\'.$this->moduleName;

        $object  = $this->projectFactory->create($type, $name);

        return $object;
    }

    private function getCustomObject($name, $path)
    {
        $object = null;

        if ($path !== null && strlen($path) > 0) {
            $path = str_replace('/', '\\', $path);

            $projectNameSpace               = $this->serverInfo->getData(ServerInfo::PROJECT_NAMESPACE);
            $this->projectFactory->basePath = $projectNameSpace.'\Modules\\'.$this->moduleName.$path;

            $object = $this->projectFactory->customCreate($this->projectFactory->basePath, $name);
        }

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

    protected function generateJsonResponse($success, $message, $data = null)
    {
        $jsonArray = ['success' => $success, 'message' => $message];

        if ($data && is_array($data)) {
            foreach ($data as $key => $value) {
                $jsonArray[$key] = $value;
            }
        }

        $json = json_encode($jsonArray, JSON_UNESCAPED_UNICODE);
        $this->response = $json;
    }
}
