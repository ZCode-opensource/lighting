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

use ZCode\Lighting\Database\DatabaseProvider;
use ZCode\Lighting\Http\Request;
use ZCode\Lighting\Http\ServerInfo;
use ZCode\Lighting\Session\Session;

class ModuleFactory extends BaseFactory
{
    const MODULE = 0;

    /** @var  Request Request object*/
    public $request;

    /** @var  Session Session object */
    public $session;

    /** @var  ServerInfo ServerInfo object*/
    public $serverInfo;

    /** @var  boolean */
    public $ajax;

    /** @var  DatabaseProvider[] Array of databases created from the configuration file. */
    public $databases;

    /** @var  ProjectFactory Factory for the internal objects of the controller */
    public $projectFactory;

    /** @var  WidgetFactory Factory for creating widgets */
    public $widgetFactory;

    protected function init()
    {
        $this->classArray = ['Module\BaseModule'];
    }

    protected function additionalSetup($object)
    {
        $object->request        = $this->request;
        $object->session        = $this->session;
        $object->serverInfo     = $this->serverInfo;
        $object->ajax           = $this->ajax;
        $object->databases      = $this->databases;
        $object->projectFactory = $this->projectFactory;
        $object->widgetFactory  = $this->widgetFactory;

        return $object;
    }
}
