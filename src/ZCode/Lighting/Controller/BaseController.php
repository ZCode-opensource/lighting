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
    public $jsList;

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

    protected function createView($viewName)
    {
        $view = null;

        $class  = $this->projectNamespace.'\Modules\\'.$this->moduleName.'\Views\\'.$viewName;
        $rClass = new \ReflectionClass($class);
        $view   = $rClass->newInstance($this->logger);

        $view->serverInfo = $this->serverInfo;

        $view->setTemplateFunction(array($this, 'getTemplate'));
        $view->setAddCssFunction(array($this, 'addCss'));
        $view->setAddJsFunction(array($this, 'addJs'));

        return $view;
    }

    public function addCss($file)
    {
        if (strlen($file) > 0) {
            $this->cssList[] = $file;
        }
    }

    public function addJs($file)
    {
        if (strlen($file) > 0) {
            $this->jsList[] = $file;
        }
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
