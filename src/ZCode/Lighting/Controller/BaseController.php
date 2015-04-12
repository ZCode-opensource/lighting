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
    public $moduleName;
    public $databases;

    abstract public function run();
    abstract public function runAjax();

    public function init()
    {
        $this->response = '';
    }

    public function getTemplate($filename)
    {
        $path = 'src/'.str_replace('\\', '/', $this->projectNamespace).'/Modules/'.$this->moduleName.'/resources/html/';
        $tmpl = new Template($this->logger);
        $tmpl->loadTemplate($filename, $path);

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
}