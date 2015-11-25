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

/**
 * @coversDefaultClass \ZCode\Lighting\Object\BaseObject
 */
class BaseObjectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::init
     */
    public function testInit()
    {
        $mockObject = new MockObject(null);
        $this->assertTrue($mockObject->getTest());
    }
}
