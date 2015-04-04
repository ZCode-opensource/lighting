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
    abstract protected function createObject($type);

    public function create($type)
    {
        $obj = $this->createObject($type);
        // $obj->setLogger($this->logger);

        return $obj;
    }
}
