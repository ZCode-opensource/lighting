<?php

/*
 * This file is part of the ZCode Lighting Web Framework.
 *
 * (c) Álvaro Somoza <asomoza@zcode.cl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZCode\Lighting\Factory;

class ModuleFactory extends BaseFactory
{
    const MODULE      = 0;

    public $request;
    public $session;
    public $serverInfo;
    public $ajax;

    protected function init()
    {
        $this->classArray = array(
            'Module\BaseModule'
        );
    }

    protected function createObject($type)
    {
        $class  = $this->getClass($type);
        $classR = new \ReflectionClass($class);
        $obj    = $classR->newInstance($this->logger);

        $obj->request    = $this->request;
        $obj->session    = $this->session;
        $obj->serverInfo = $this->serverInfo;
        $obj->ajax       = $this->ajax;

        return $obj;
    }
}