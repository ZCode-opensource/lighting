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

class MainFactory extends BaseFactory
{
    const REQUEST  = 0;
    const RESPONSE = 1;
    const SERVER_INFO = 2;
    const SESSION = 3;

    private $classArray;

    protected function init()
    {
        $this->logger->addDebug('Initializing MainFactory.');

        $this->classArray = array(
            'Http\Request',
            'Http\Response',
            'Http\ServerInfo',
            'Session\Session'
        );

        $this->logger->addDebug('MainFactory initialized.');
    }

    private function getClass($type)
    {
        $frameworkDir = '\ZCode\Lighting\\';

        return $frameworkDir.$this->classArray[$type];
    }

    protected function createObject($type)
    {
        $this->logger->addDebug('Creating object of type: '.$type);
        $class  = $this->getClass($type);
        $classR = new \ReflectionClass($class);
        $obj    = $classR->newInstance($this->logger);

        $this->logger->addDebug('Object loaded.');

        return $obj;
    }
}