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

use ZCode\Lighting\Http\Request;
use ZCode\Lighting\Http\Response;
use ZCode\Lighting\Http\ServerInfo;
use ZCode\Lighting\Session\Session;

class MainFactory extends BaseFactory
{
    const REQUEST  = 0;
    const RESPONSE = 1;
    const SERVER_INFO = 2;
    const SESSION = 3;
    const MODULE_FACTORY = 4;
    const TEMPLATE_FACTORY = 5;

    protected function init()
    {
        $this->classArray = [
            'Http\Request',
            'Http\Response',
            'Http\ServerInfo',
            'Session\Session',
            'Factory\ModuleFactory',
            'Factory\TemplateFactory'
        ];
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     *
     * @param Request|Response|ServerInfo|Session|ModuleFactory|TemplateFactory $object
     *
     * @return Request|Response|ServerInfo|Session|ModuleFactory|TemplateFactory
     */
    protected function additionalSetup($object)
    {
        if (get_class($object) === 'ZCode\Lighting\Http\Request') {
            $object->initializeRequest($_POST, $_GET, $_SERVER['REQUEST_URI']);
        }

        return $object;
    }

    public function getLogger()
    {
        return $this->logger;
    }
}
