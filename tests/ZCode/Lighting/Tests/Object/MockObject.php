<?php

/*
 * This file is part of the ZCode Lighting Web Framework.
 *
 * (c) Álvaro Somoza <asomoza@zcode.cl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZCode\Lighting\Tests\Object;

use ZCode\Lighting\Object\BaseObject;

class MockObject extends BaseObject
{
    private $test;

    protected function init()
    {
        $this->test = true;
        parent::init();
    }

    public function getTest()
    {
        return $this->test;
    }
}