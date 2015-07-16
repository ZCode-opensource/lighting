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

use ZCode\Lighting\Object\BaseObject;

abstract class BaseFactory extends BaseObject
{
    public $basePath;
    protected $classArray;

    public function create($type)
    {
        $obj = $this->createObject($type);

        return $obj;
    }

    protected function getClass($type)
    {
        $frameworkDir = '\ZCode\Lighting\\';
        return $frameworkDir.$this->classArray[$type];
    }

    protected function createObject($type)
    {
        $class  = $this->getClass($type);
        $classR = new \ReflectionClass($class);
        $object = $classR->newInstance($this->logger);
        $object = $this->additionalSetup($object);

        return $object;
    }

    protected function additionalSetup($object)
    {
        return $object;
    }
}
