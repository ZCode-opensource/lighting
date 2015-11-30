<?php

/*
 * This file is part of the ZCode Lighting Web Framework.
 *
 * (c) Álvaro Somoza <asomoza@zcode.cl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZCode\Lighting\Tests\Controller;

use ZCode\Lighting\Factory\ProjectFactory;
use ZCode\Lighting\Http\ServerInfo;
use ZCode\Lighting\Model\BaseModel;
use ZCode\Lighting\Template\Template;
use ZCode\Lighting\View\BaseView;
use ZCode\Lighting\View\Widget;

/**
 * @coversDefaultClass \ZCode\Lighting\Controller\BaseController
 * @uses \ZCode\Lighting\Object\BaseObject
 * @uses \ZCode\Lighting\Model\BaseModel
 */
class BaseControllerTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    private $mockInfo;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    private $mockProjectFactory;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    private $mockWidgetFactory;

    public function setUp()
    {
        $infoMap = [
            [ServerInfo::DOC_ROOT, '/home/test'],
            [ServerInfo::PROJECT_NAMESPACE, 'ZCode\Lighting\Tests']
        ];

        $factoryMap = [
            [ProjectFactory::VIEW, 'TestView', new BaseView(null)],
            [ProjectFactory::MODEL, 'TestModel', new BaseModel(null)],
            [
                ProjectFactory::CONTROLLER,
                'TestController',
                $this->getMockForAbstractClass('\ZCode\Lighting\Controller\BaseController', [null])
            ]
        ];

        $customFactoryMap = [
            ['ZCode\Lighting\Tests\Modules\Models\TestPath\Test', 'TestModel', new BaseModel(null)],
            [
                'ZCode\Lighting\Tests\Modules\Controllers\TestPath\Test',
                'TestController',
                $this->getMockForAbstractClass('\ZCode\Lighting\Controller\BaseController', [null])
            ]
        ];

        $this->mockInfo = $this->getMockBuilder('\ZCode\Lighting\Http\ServerInfo')->disableOriginalConstructor()
            ->setMethods(['getData'])->getMock();
        $this->mockInfo->method('getData')->will($this->returnValueMap($infoMap));

        $this->mockProjectFactory = $this->getMockBuilder('\ZCode\Lighting\Factory\ProjectFactory')
            ->disableOriginalConstructor()->setMethods(['create', 'customCreate'])->getMock();
        $this->mockProjectFactory->method('create')->will($this->returnValueMap($factoryMap));
        $this->mockProjectFactory->method('customCreate')->will($this->returnValueMap($customFactoryMap));

        $this->mockWidgetFactory = $this->getMockBuilder('\ZCode\Lighting\Factory\WidgetFactory')
            ->disableOriginalConstructor()->setMethods(['create'])->getMock();
        $this->mockWidgetFactory->method('create')->with('TestWidget')
            ->willReturn(new Widget(null));
    }

    /**
     * @covers ::getTemplate
     * @covers ::init
     * @uses \ZCode\Lighting\Template\Template
     */
    public function testGetTemplate()
    {
        $mockController = $this->getMockForAbstractClass('\ZCode\Lighting\Controller\BaseController', [null]);
        $mockController->serverInfo = $this->mockInfo;

        $tmpl = new Template(null);
        $this->assertEquals($tmpl, $mockController->getTemplate('test.html', '/'));
    }

    /**
     * @covers ::init
     * @covers ::createView
     * @covers ::getObject
     */
    public function testCreateView()
    {
        $method     = self::getMethod('createView');
        $controller = $this->getMockForAbstractClass('\ZCode\Lighting\Controller\BaseController', [null]);

        $controller->serverInfo     = $this->mockInfo;
        $controller->projectFactory = $this->mockProjectFactory;

        $testView = $method->invokeArgs($controller, ['TestView']);
        $badView  = $method->invokeArgs($controller, ['FailView']);

        $this->assertEquals('ZCode\Lighting\View\BaseView', get_class($testView));
        $this->assertNull($badView);
    }

    /**
     * @covers ::init
     * @covers ::createModel
     * @covers ::seedModel
     * @covers ::getObject
     */
    public function testCreateModel()
    {
        $method     = self::getMethod('createModel');
        $controller = $this->getMockForAbstractClass('\ZCode\Lighting\Controller\BaseController', [null]);

        $controller->serverInfo     = $this->mockInfo;
        $controller->projectFactory = $this->mockProjectFactory;

        $testModel = $method->invokeArgs($controller, ['TestModel']);
        $badModel  = $method->invokeArgs($controller, ['FailModel']);

        $this->assertEquals('ZCode\Lighting\Model\BaseModel', get_class($testModel));
        $this->assertNull($badModel);
    }

    /**
     * @covers ::init
     * @covers ::createController
     * @covers ::seedController
     * @covers ::getObject
     */
    public function testCreateController()
    {
        $method     = self::getMethod('createController');
        $controller = $this->getMockForAbstractClass('\ZCode\Lighting\Controller\BaseController', [null]);

        $controller->serverInfo     = $this->mockInfo;
        $controller->projectFactory = $this->mockProjectFactory;

        $testController = $method->invokeArgs($controller, ['TestController']);
        $badController  = $method->invokeArgs($controller, ['FailController']);

        $this->assertEquals('ZCode\Lighting\Controller\BaseController', get_parent_class($testController));
        $this->assertNull($badController);
    }

    /**
     * @covers ::init
     * @covers ::createWidget
     */
    public function testCreateWidget()
    {
        $method     = self::getMethod('createWidget');
        $controller = $this->getMockForAbstractClass('\ZCode\Lighting\Controller\BaseController', [null]);

        $controller->serverInfo    = $this->mockInfo;
        $controller->widgetFactory = $this->mockWidgetFactory;

        $testWidget = $method->invokeArgs($controller, ['TestWidget']);

        $this->assertEquals('ZCode\Lighting\View\Widget', get_class($testWidget));
    }

    /**
     * @covers ::init
     * @covers ::createCustomModel
     * @covers ::seedModel
     * @covers ::getCustomObject
     */
    public function testCreateCustomModel()
    {
        $method     = self::getMethod('createCustomModel');
        $controller = $this->getMockForAbstractClass('\ZCode\Lighting\Controller\BaseController', [null]);

        $controller->serverInfo     = $this->mockInfo;
        $controller->projectFactory = $this->mockProjectFactory;

        $testModel = $method->invokeArgs($controller, ['TestModel', 'TestPath/Test']);
        $badModel  = $method->invokeArgs($controller, ['FailModel', 'TestPath/Test']);

        $this->assertEquals('ZCode\Lighting\Model\BaseModel', get_class($testModel));
        $this->assertNull($badModel);
    }

    /**
     * @covers ::init
     * @covers ::createCustomController
     * @covers ::seedController
     * @covers ::getCustomObject
     */
    public function testCreateCustomController()
    {
        $method     = self::getMethod('createCustomController');
        $controller = $this->getMockForAbstractClass('\ZCode\Lighting\Controller\BaseController', [null]);

        $controller->serverInfo     = $this->mockInfo;
        $controller->projectFactory = $this->mockProjectFactory;

        $testController = $method->invokeArgs($controller, ['TestController', 'TestPath/Test']);
        $badController  = $method->invokeArgs($controller, ['FailController', 'TestPath/Test']);

        $this->assertEquals('ZCode\Lighting\Controller\BaseController', get_parent_class($testController));
        $this->assertNull($badController);
    }

    protected static function getMethod($name)
    {
        $class = new \ReflectionClass('\ZCode\Lighting\Controller\BaseController');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

    /**
     * @covers ::init
     * @covers ::addCss
     */
    public function testAddCss()
    {
        $controller = $this->getMockForAbstractClass('\ZCode\Lighting\Controller\BaseController', [null]);
        $controller->addCss('test');

        $this->assertEquals('test.css', $controller->cssList[0]);
    }

    /**
     * @covers ::init
     * @covers ::addPriorityCss
     */
    public function testAddPriorityCss()
    {
        $controller = $this->getMockForAbstractClass('\ZCode\Lighting\Controller\BaseController', [null]);
        $controller->addPriorityCss('test');

        $this->assertEquals('test.css', $controller->priorityCssList[0]);
    }

    /**
     * @covers ::init
     * @covers ::addJs
     */
    public function testAddJs()
    {
        $controller = $this->getMockForAbstractClass('\ZCode\Lighting\Controller\BaseController', [null]);
        $controller->addJs('test');

        $this->assertEquals('test.js', $controller->jsList[0]);
    }

    /**
     * @covers ::init
     * @covers ::addPriorityJs
     */
    public function testAddPriorityJs()
    {
        $controller = $this->getMockForAbstractClass('\ZCode\Lighting\Controller\BaseController', [null]);
        $controller->addPriorityJs('test');

        $this->assertEquals('test.js', $controller->priorityJsList[0]);
    }

    /**
     * @covers ::init
     * @covers ::generateJsonResponse
     */
    public function testGenerateJsonResponse()
    {
        $method     = self::getMethod('generateJsonResponse');
        $controller = $this->getMockForAbstractClass('\ZCode\Lighting\Controller\BaseController', [null]);

        $json = '{"success":true,"message":"Test message"}';
        $method->invokeArgs($controller, [true, 'Test message', null]);
        $response = $controller->response;
        $this->assertEquals($json, $response);

        $json = '{"success":false,"message":""}';
        $method->invokeArgs($controller, [false, '', null]);
        $response = $controller->response;
        $this->assertEquals($json, $response);

        $json = '{"success":true,"message":"Test message","test":false}';
        $method->invokeArgs($controller, [true, 'Test message', ['test' => false]]);
        $response = $controller->response;
        $this->assertEquals($json, $response);

        $json = '{"success":true,"message":"Test message","testArray":{"test1":true,"test2":false}}';
        $method->invokeArgs($controller, [true, 'Test message', ['testArray' => ['test1' => true, 'test2' => false]]]);
        $response = $controller->response;
        $this->assertEquals($json, $response);
    }
}
