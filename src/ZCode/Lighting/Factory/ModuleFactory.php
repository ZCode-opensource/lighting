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
    public $projectNamespace;
    public $databases;

    protected function init()
    {
        $this->classArray = array(
            'Module\BaseModule'
        );
    }

    protected function additionalSetup($object)
    {
        $object->request          = $this->request;
        $object->session          = $this->session;
        $object->serverInfo       = $this->serverInfo;
        $object->ajax             = $this->ajax;
        $object->projectNamespace = $this->projectNamespace;
        $object->databases        = $this->databases;

        return $object;
    }
}
