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

class WidgetFactory extends BaseFactory
{
    protected function getClass($type)
    {
        return $this->basePath.'\\'.$type;
    }

    protected function createObject($type)
    {
        $class  = $this->getClass($type);
        $classR = new \ReflectionClass($class);
        $object = $classR->newInstance($this->logger);
        $object = $this->additionalSetup($object);

        return $object;
    }
}