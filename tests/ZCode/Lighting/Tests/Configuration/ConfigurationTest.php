<?php

/*
 * This file is part of the ZCode Lighting Web Framework.
 *
 * (c) Álvaro Somoza <asomoza@zcode.cl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZCode\Lighting\Tests\Configuration;

use ZCode\Lighting\Configuration\Configuration;

/**
 * @coversDefaultClass \ZCode\Lighting\Configuration\Configuration
 * @uses \ZCode\Lighting\Object\BaseObject
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     */
    public function testError()
    {
        $config = new Configuration('no-file.conf');
        $this->assertTrue($config->error);
    }

    /**
     * @covers ::getConfig
     * @covers ::__construct
     * @covers ::getText
     */
    public function testGetConfig()
    {
        $config = new Configuration('framework-dist.conf');

        $this->assertEquals('/lighting', $config->getConfig('site', 'relative_path'));
        $this->assertNull($config->getConfig('no-section', 'no-data'));
    }

    /**
     * @covers ::getConfig
     * @covers ::__construct
     * @covers ::getBoolean
     */
    public function testGetBooleanConfig()
    {
        $config = new Configuration('framework-dist.conf');

        $this->assertTrue($config->getConfig('application', 'show_errors', true));
        $this->assertFalse($config->getConfig('application', 'auth', true));
        $this->assertNull($config->getConfig('no-section', 'no-data'), true);
    }
}
