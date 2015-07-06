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

class ProjectFactory extends BaseFactory
{
    const MODEL      = 0;
    const VIEW       = 1;
    const CONTROLLER = 2;

    public $basePath;

    protected function init()
    {
        $this->classArray = array(
            '\Models',
            '\Views',
            '\Controllers'
        );
    }

    public function create($type, $name)
    {
        $obj = $this->createObject($type, $name);
        return $obj;
    }

    protected function getClass($type)
    {
        return $this->basePath.$this->classArray[$type];
    }

    protected function createObject($type, $name)
    {
        $class  = $this->getClass($type).'\\'.$name;
        $classR = new \ReflectionClass($class);
        $object = $classR->newInstance($this->logger);
        $object = $this->additionalSetup($object);

        return $object;
    }
}