<?php

/*
 * This file is part of the ZCode Lighting Web Framework.
 *
 * (c) Álvaro Somoza <asomoza@zcode.cl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZCode\Lighting\Tests;

use ZCode\Lighting\Application;
use ZCode\Lighting\Factory\MainFactory;

/**
 * @SuppressWarnings(PHPMD.Superglobals)
 * @coversDefaultClass \ZCode\Lighting\Application
 * @uses \ZCode\Lighting\Object\BaseObject
 * @uses \ZCode\Lighting\Factory\BaseFactory
 * @uses \ZCode\Lighting\Factory\MainFactory
 * @uses \ZCode\Lighting\Factory\ModuleFactory
 * @uses \ZCode\Lighting\Factory\TemplateFactory
 * @uses \ZCode\Lighting\Factory\DatabaseFactory
 * @uses \ZCode\Lighting\Database\DatabaseProvider
 * @uses \ZCode\Lighting\Database\Mysql\BaseMysqlProvider
 * @uses \ZCode\Lighting\Configuration\Configuration
 */
class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    /** @var  Application */
    private $app;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    private $logger;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    private $mockFactory;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    private $mockModFactory;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    private $mockTmplFactory;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    private $mockModule;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    private $mockInfo;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    private $mockReq;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    private $mockRes;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    private $mockSes;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    private $mockTmpl;

    private $map;

    public function setUp()
    {
        $this->mockFactory = $this->getMockBuilder('ZCode\Lighting\Factory\MainFactory')->setConstructorArgs([null])
            ->getMock();
        $this->mockModFactory = $this->getMockBuilder('ZCode\Lighting\Factory\ModuleFactory')
            ->setConstructorArgs([null])->getMock();
        $this->mockTmplFactory = $this->getMockBuilder('ZCode\Lighting\Factory\TemplateFactory')
            ->setConstructorArgs([null])->getMock();
        $this->mockModule = $this->getMockBuilder('ZCode\Lighting\Module\BaseModule')->disableOriginalConstructor()
            ->getMock();
        $this->mockInfo = $this->getMockBuilder('ZCode\Lighting\Http\ServerInfo')->disableOriginalConstructor()
            ->getMock();
        $this->mockReq  = $this->getMockBuilder('ZCode\Lighting\Http\Request')->disableOriginalConstructor()
            ->getMock();
        $this->mockRes  = $this->getMockBuilder('ZCode\Lighting\Http\Response')->disableOriginalConstructor()
            ->getMock();
        $this->mockSes  = $this->getMockBuilder('ZCode\Lighting\Session\Session')->disableOriginalConstructor()
            ->getMock();
        $this->mockTmpl = $this->getMockBuilder('ZCode\Lighting\Template\Template')->disableOriginalConstructor()
            ->getMock();

        $this->logger = $this->getMockBuilder('Monolog\Logger')->disableOriginalConstructor()->getMock();

        $this->mockTmplFactory->method('create')->willReturn($this->mockTmpl);
        $this->mockModFactory->method('create')->willReturn($this->mockModule);
    }

    private function populateFactory()
    {
        $this->map = [
            [MainFactory::SERVER_INFO, $this->mockInfo],
            [MainFactory::REQUEST, $this->mockReq],
            [MainFactory::RESPONSE, $this->mockRes],
            [MainFactory::SESSION, $this->mockSes],
            [MainFactory::MODULE_FACTORY, $this->mockModFactory],
            [MainFactory::TEMPLATE_FACTORY, $this->mockTmplFactory]
        ];

        $this->mockFactory->method('getLogger')->willReturn($this->logger);
        $this->mockFactory->method('create')->will($this->returnValueMap($this->map));
    }

    /**
     * @covers ::__construct
     * @uses \ZCode\Lighting\Application::getLogLevel
     */
    public function testConfigurationError()
    {
        $this->logger->expects($this->exactly(1))->method('addError')->with("Couldn't load configuration file");
        $this->populateFactory();
        $this->app = new Application('error.conf', $this->mockFactory);
    }

    /**
     * @covers ::__construct
     * @covers ::render
     * @covers ::generateModuleResponse
     * @covers ::renderResponse
     * @covers ::processModuleCss
     * @covers ::processModuleJs
     * @uses \ZCode\Lighting\Application::validateAuth
     * @uses \ZCode\Lighting\Application::getLogLevel
     * @uses \ZCode\Lighting\Application::getDisplayErrors
     */
    public function testRender()
    {
        $this->mockModule->moduleCssList = [];
        $this->mockModule->moduleJsList = [];

        $this->mockReq->method('getPostVar')->willReturn('Test');
        $this->mockTmpl->method('getHtml')->willReturn('Test template');
        $this->mockRes->expects($this->exactly(1))->method('html')->with('Test template');

        $this->populateFactory();

        $this->app = new Application('framework-dist.conf', $this->mockFactory);
        $this->app->render();
    }

    public function tearDown()
    {

    }
}
