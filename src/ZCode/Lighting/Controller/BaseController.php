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

abstract class BaseController extends BaseObject
{
    public $request;
    public $response;
    public $serverInfo;
    public $session;
    public $projectNamespace;

    public function getTemplate($filename)
    {

    }

    protected function createView($viewName)
    {
        $view = null;

        $class  = $this->projectNamespace.'\Modules\\'.$this->moduleName.'\Views'.$viewName;
        $rClass = new \ReflectionClass($class);
        $view   = $rClass->newInstance($this->logger);

        return $view;
    }

}