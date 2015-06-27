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

use ZCode\Lighting\Http\ServerInfo;
use ZCode\Lighting\Object\BaseObject;
use ZCode\Lighting\Template\Template;

abstract class BaseController extends BaseObject
{
    public $request;
    public $response;
    public $serverInfo;
    public $session;
    public $projectNamespace;
    public $resourcePath;
    public $moduleName;
    public $databases;
    public $cssList;
    public $globalCssList;
    public $jsList;
    public $globalJsList;

    abstract public function run();
    abstract public function runAjax();

    public function init()
    {
        $this->response = '';
    }

    public function getTemplate($filename)
    {
        $tmpl = new Template($this->logger);
        $tmpl->loadTemplate($filename, $this->resourcePath.'html/');

        return $tmpl;
    }

    public function getGlobalTemplate($filename, $path)
    {
        $basePath = $this->serverInfo->getData(ServerInfo::DOC_ROOT);
        $tmpl = new Template($this->logger);
        $tmpl->loadTemplate($filename, $basePath.'/'.$path);

        return $tmpl;
    }

    protected function createView($viewName)
    {
        $view = null;

        $class  = $this->projectNamespace.'\Modules\\'.$this->moduleName.'\Views\\'.$viewName;
        $rClass = new \ReflectionClass($class);
        $view   = $rClass->newInstance($this->logger);

        $view->serverInfo = $this->serverInfo;

        $view->setTemplateFunction(array($this, 'getTemplate'));
        $view->setGlobalTemplateFunction(array($this, 'getGlobalTemplate'));
        $view->setAddCssFunction(array($this, 'addCss'));
        $view->setAddGlobalCssFunction(array($this, 'addGlobalCss'));
        $view->setAddJsFunction(array($this, 'addJs'));
        $view->setAddGlobalJsFunction(array($this, 'addGlobalJs'));

        return $view;
    }

    protected function createModel($modelName)
    {
        $model = null;

        $class  = $this->projectNamespace.'\Modules\\'.$this->moduleName.'\Models\\'.$modelName;
        $rClass = new \ReflectionClass($class);
        $model  = $rClass->newInstance($this->logger);

        $model->setDatabases($this->databases);

        return $model;
    }

    protected function createController($controllerName)
    {
        $controller = null;

        $class       = $this->projectNamespace.'\Modules\\'.$this->moduleName.'\Controllers\\'.$controllerName;
        $rClass      = new \ReflectionClass($class);
        $controller  = $rClass->newInstance($this->logger);

        $controller->databases        = $this->databases;
        $controller->request          = $this->request;
        $controller->serverInfo       = $this->serverInfo;
        $controller->session          = $this->session;
        $controller->projectNamespace = $this->projectNamespace;
        $controller->resourcePath     = $this->resourcePath;
        $controller->moduleName       = $this->moduleName;

        return $controller;
    }

    public function addCss($file)
    {
        if (strlen($file) > 0) {
            $this->cssList[] = $file;
        }
    }

    public function addGlobalCss($file)
    {
        if (strlen($file) > 0) {
            $this->globalCssList[] = $file;
         }
    }

    public function addJs($file)
    {
        if (strlen($file) > 0) {
            $this->jsList[] = $file;
        }
    }

    public function addGlobalJs($file)
    {
        if (strlen($file) > 0) {
            $this->globalJsList[] = $file;
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
