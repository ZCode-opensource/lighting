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

use ZCode\Lighting\Controller\BaseController;
use ZCode\Lighting\Model\BaseModel;
use ZCode\Lighting\View\BaseView;

class ProjectFactory extends BaseFactory
{
    const MODEL      = 0;
    const VIEW       = 1;
    const CONTROLLER = 2;

    protected function init()
    {
        $this->classArray = ['\Models',  '\Views', '\Controllers'];
    }

    public function create($type, $name = null)
    {
        $obj = $this->createObject($this->getClass($type), $name);
        return $obj;
    }

    public function customCreate($path, $name)
    {
        $obj = $this->createObject($path, $name);
        return $obj;
    }

    protected function getClass($type)
    {
        return $this->basePath.$this->classArray[$type];
    }

    /**
     * Instantiate the class and return the object created.
     *
     * This method instantiates the class that could be a model, view or controller for using inside
     * the module. The class used to instantiate the object depends on the path and the name of the class.
     * This method should only be used to instantiate a class of the type BaseModel, BaseView or BaseController.
     *
     * @param string $path Path inside the module that contains the class
     * @param string $name Name of the class to instantiate
     *
     * @return BaseModel|BaseView|BaseController Depending on the type, returns the model, view or controller
     * @throws \ReflectionException
     */
    protected function createObject($path, $name = null)
    {
        $class  = $path.'\\'.$name;
        $classR = new \ReflectionClass($class);
        $object = $classR->newInstance($this->logger);
        $object = $this->additionalSetup($object);

        return $object;
    }
}
