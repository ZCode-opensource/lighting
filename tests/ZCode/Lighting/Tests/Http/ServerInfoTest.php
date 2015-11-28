<?php

/*
 * This file is part of the ZCode Lighting Web Framework.
 *
 * (c) Álvaro Somoza <asomoza@zcode.cl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZCode\Lighting\Tests\Http;

use ZCode\Lighting\Http\ServerInfo;

/**
 * @coversDefaultClass \ZCode\Lighting\Http\ServerInfo
 * @uses \ZCode\Lighting\Object\BaseObject
 */
class ServerInfoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::init
     * @covers ::setData
     * @covers ::getData
     */
    public function testSetGetData()
    {
        $info = new ServerInfo(null);
        $info->setData('test', true);
        $this->assertTrue($info->getData('test'));
    }

    /**
     * @covers ::setRelativePath
     * @covers ::init
     * @covers ::setData
     * @covers ::getData
     * @covers ::getBaseUrl
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)*
     */
    public function testSetRelativePath()
    {
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['PHP_SELF']  = '/test/';

        $info = new ServerInfo(null);
        $info->setRelativePath('/test');

        $this->assertEquals('http://localhost/test/', $info->getData(ServerInfo::BASE_URL));
        $this->assertEquals('/test', $info->getData(ServerInfo::DOC_ROOT));
        $this->assertEquals('/test', $info->getData(ServerInfo::RELATIVE_PATH));
    }

    /**
     * @covers ::getBaseUrl
     * @covers ::init
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)*
     */
    public function testGetBaseUrl()
    {
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['PHP_SELF']  = '/test/';

        $info = new ServerInfo(null);
        $this->assertEquals('http://localhost/test/', $info->getBaseUrl());
    }
}
