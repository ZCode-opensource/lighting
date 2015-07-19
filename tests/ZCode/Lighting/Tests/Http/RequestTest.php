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

use ZCode\Lighting\Http\Request;

/**
 * @coversDefaultClass \ZCode\Lighting\Http\Request
 * @covers \ZCode\Lighting\Http\Request::initializeRequest
 * @covers \ZCode\Lighting\Validation\Sanitizer
 * @uses \ZCode\Lighting\Object\BaseObject
 */
class RequestTest extends \PHPUnit_Framework_TestCase
{
    /** @var Request */
    protected $request;

    public function setUp()
    {
        $get  = [
            'getTestTrue' => true,
            'getTestFalse' => false,
            'getString' => 'string',
            'getInteger' => 123,
            'getArray' => [1,2,3,4],
            'getNumeric' => '123',
            'getFloat' => 12.34
        ];

        $post = [
            'postTestTrue' => true,
            'postTestFalse' => false,
            'postExclusive' => 1
        ];

        $this->request = new Request(null);
        $this->request->initializeRequest($post, $get, '/lighting/home/1');
    }

    /**
     * @covers ::getModule
     * @covers ::strReplaceFirst
     *
     */
    public function testGetModule()
    {
        // Must return home module
        $this->assertEquals('home', $this->request->getModule(true, '/lighting'));

        // No module in URL, must return false
        $this->assertFalse($this->request->getModule(true, '/lighting/home/1'));
    }

    /**
     * @covers ::GetUrlVar
     * @covers ::getModule
     * @covers ::strReplaceFirst
     *
     */
    public function testGetUrlVar()
    {
        // Necessary for setting the urlVars array property
        $this->request->getModule(true, '/lighting');

        // The url is: /lighting/home/1, must return 1
        $this->assertEquals(1, $this->request->getUrlVar(1));

        // The url is: /lighting/home/1 so no value for urlVar[2], must return false
        $this->assertFalse($this->request->getUrlVar(2));
    }

    /**
     * @covers ::getGetVar
     * @covers ::sanitizeVar
     */
    public function testGetGetVar()
    {
        // $_GET['null'] doesn't exit, must return null
        $this->assertNull($this->request->getGetVar('null', Request::BOOLEAN));

        // $_GET['getTestTrue] equals true, must return true
        $this->assertTrue($this->request->getGetVar('getTestTrue', Request::BOOLEAN));

        // $_GET['getTestFalse] equals false, must return false
        $this->assertFalse($this->request->getGetVar('getTestFalse', Request::BOOLEAN));

        // $_GET['getString] equals "string", must return the word string
        $this->assertEquals('string', $this->request->getGetVar('getString', Request::STRING));

        // $_GET['getInteger] equals 123, must return null
        $this->assertNull($this->request->getGetVar('getInteger', Request::STRING));

        // $_GET['getInteger] equals 123, must return 123
        $this->assertEquals(123, $this->request->getGetVar('getInteger', Request::INTEGER));

        // $_GET['getString] equals "string", must return null
        $this->assertNull($this->request->getGetVar('getString', Request::INTEGER));

        // $_GET['getString] equals "string", must return null
        $this->assertNull($this->request->getGetVar('getString', Request::ARRAY_VAR));

        // $_GET['getArray] equals an array with [1,2,3,4], must return the same array
        $this->assertEquals([1,2,3,4], $this->request->getGetVar('getArray', Request::ARRAY_VAR));

        // $_GET['getNumeric] equals "123", which is a string with numeric chars, must return "123"
        $this->assertEquals('123', $this->request->getGetVar('getNumeric', Request::NUMERIC));

        // $_GET['getString] equals "string", which is a string with no numeric chars, must return null
        $this->assertNull($this->request->getGetVar('getString', Request::NUMERIC));

        // $_GET['getFloat] equals 12.34, must return 12.34
        $this->assertEquals(12.34, $this->request->getGetVar('getFloat', Request::FLOAT));

        // $_GET['getInteger] equals 123, must return null
        $this->assertNull($this->request->getGetVar('getInteger', Request::FLOAT));
    }

    /**
     * @covers ::getPostVar
     * @covers ::sanitizeVar
     */
    public function testGetPostVar()
    {
        // $_POST'null'] doesn't exit, must return null
        $this->assertNull($this->request->getPostVar('null', Request::BOOLEAN));

        // $_POST['postTestTrue] equals true, must return true
        $this->assertTrue($this->request->getPostVar('postTestTrue', Request::BOOLEAN));

        // $_POST['postTestFalse] equals false, must return false
        $this->assertFalse($this->request->getPostVar('postTestFalse', Request::BOOLEAN));
    }

    /**
     * @covers ::getVar
     * @covers ::getGetVar
     * @covers ::getPostVar
     * @covers ::sanitizeVar
     */
    public function testGetVar()
    {
        // Neither post or get has a index with the name 'noVar', must return null
        $this->assertNull($this->request->getVar('noVar', Request::BOOLEAN));

        // $_GET['getTestTrue'] equals true, must return true
        $this->assertEquals(true, $this->request->getVar('getTestTrue', Request::BOOLEAN));

        // $_POST['postExclusive'] equals 1, must return 1
        $this->assertEquals(true, $this->request->getVar('postExclusive', Request::INTEGER));
    }
}
