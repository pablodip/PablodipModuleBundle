<?php

namespace Pablodip\ModuleBundle\Tests\Action;

use Pablodip\ModuleBundle\Action\BaseRouteAction as BaseBaseRouteAction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BaseRouteAction extends BaseBaseRouteAction
{
    static public $name;
    static public $routeName;
    static public $routePattern;
    static public $controller;

    protected function defineConfiguration()
    {
        if (null !== self::$name) {
            $this->setName(self::$name);
        }
        if (null !== self::$routeName) {
            $this->setRouteName(self::$routeName);
        }
        if (null !== self::$routePattern) {
            $this->setRoutePattern(self::$routePattern);
        }
        if (null !== self::$controller) {
            $this->setController(self::$controller);
        }
    }
}

class BaseRouteActionTest extends \PHPUnit_Framework_TestCase
{
    private $controller;
    private $action;
    private $module;

    protected function setUp()
    {
        $this->controller = function () {};

        BaseRouteAction::$name = 'list_name';
        BaseRouteAction::$routeName = 'list';
        BaseRouteAction::$routePattern = '/list';
        BaseRouteAction::$controller = $this->controller;

        $this->action = new BaseRouteAction();
        $this->module = $this->getMock('Pablodip\ModuleBundle\Module\ModuleInterface');
    }

    public function testDefineConfiguration()
    {
        $action = new BaseRouteAction();
        $action->setModule($this->module);

        $this->assertSame('list_name', $action->getName());
    }

    public function testDefineConfigurationDefaultName()
    {
        BaseRouteAction::$name = null;

        $action = new BaseRouteAction();
        $action->setModule($this->module);

        $this->assertSame('list', $action->getName());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testConstructorConfigureNoRouteName()
    {
        BaseRouteAction::$routeName = null;

        $action = new BaseRouteAction();
        $action->setModule($this->module);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testConstructorConfigureNoRoutePattern()
    {
        BaseRouteAction::$routePattern = null;

        $action = new BaseRouteAction();
        $action->setModule($this->module);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testConstructorConfigureNoController()
    {
        BaseRouteAction::$controller = null;

        $action = new BaseRouteAction();
        $action->setModule($this->module);
    }

    public function testSetGetRouteName()
    {
        $this->assertSame($this->action, $this->action->setRouteName('list'));
        $this->assertSame('list', $this->action->getRouteName());
    }

    public function testSetGetRoutePattern()
    {
        // /
        $this->assertSame($this->action, $this->action->setRoutePattern('/'));
        $this->assertSame('/', $this->action->getRoutePattern());

        // normal
        $this->assertSame($this->action, $this->action->setRoutePattern('/list'));
        $this->assertSame('/list', $this->action->getRoutePattern());
    }

    public function testSetGetRouteDefaults()
    {
        $routeDefaults = array('_controller' => 'foobar');
        $this->assertSame($this->action, $this->action->setRouteDefaults($routeDefaults));
        $this->assertSame($routeDefaults, $this->action->getRouteDefaults());
    }

    public function testSetRouteDefault()
    {
        $this->assertSame($this->action, $this->action->setRouteDefault('foo', 'bar'));
        $this->assertSame(array('foo' => 'bar'), $this->action->getRouteDefaults());
        $this->assertSame($this->action, $this->action->setRouteDefault('ups', 'sf'));
        $this->assertSame(array('foo' => 'bar', 'ups' => 'sf'), $this->action->getRouteDefaults());
    }

    public function testSetGetRouteRequirements()
    {
        $routeRequirements = array('_method' => 'GET');
        $this->assertSame($this->action, $this->action->setRouteRequirements($routeRequirements));
        $this->assertSame($routeRequirements, $this->action->getRouteRequirements());
    }

    public function testSetRouteRequirement()
    {
        $this->assertSame($this->action, $this->action->setRouteRequirement('foo', 'bar'));
        $this->assertSame(array('foo' => 'bar'), $this->action->getRouteRequirements());
        $this->assertSame($this->action, $this->action->setRouteRequirement('_method', 'GET'));
        $this->assertSame(array('foo' => 'bar', '_method' => 'GET'), $this->action->getRouteRequirements());
    }

    public function testSetGetRouteOptions()
    {
        $routeOptions = array('expose' => true);
        $this->assertSame($this->action, $this->action->setRouteOptions($routeOptions));
        $this->assertSame($routeOptions, $this->action->getRouteOptions());
    }

    public function testSetRouteOption()
    {
        $this->assertSame($this->action, $this->action->setRouteOption('foo', 'bar'));
        $this->assertSame(array('foo' => 'bar'), $this->action->getRouteOptions());
        $this->assertSame($this->action, $this->action->setRouteOption('expose', true));
        $this->assertSame(array('foo' => 'bar', 'expose' => true), $this->action->getRouteOptions());
    }

    public function testSetRouteBasic()
    {
        $this->assertSame($this->action, $this->action->setRoute('list', '/list', 'ANY'));
        $this->assertSame('list', $this->action->getRouteName());
        $this->assertSame('/list', $this->action->getRoutePattern());
        $this->assertSame(array(), $this->action->getRouteDefaults());
        $this->assertSame(array(), $this->action->getRouteRequirements());
    }

    public function testSetRouteMethod()
    {
        $this->assertSame($this->action, $this->action->setRoute('list', '/list', 'GET'));
        $this->assertSame(array('_method' => 'GET'), $this->action->getRouteRequirements());
    }

    /**
     * @dataProvider executeControllerArgumentsProvider
     */
    public function testExecuteControllerArguments($attributes, $controller, $expectedArguments)
    {
        $request = new Request();
        $request->attributes->replace($attributes);

        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $container
            ->expects($this->any())
            ->method('get')
            ->with('request')
            ->will($this->returnValue($request))
        ;

        $module = $this->getMock('Pablodip\ModuleBundle\Module\ModuleInterface');
        $module
            ->expects($this->any())
            ->method('getContainer')
            ->will($this->returnValue($container))
        ;

        foreach ($expectedArguments as &$argument) {
            if ('@request' === $argument) {
                $argument = $request;
            } elseif ('@action' === $argument) {
                $argument = $this->action;
            }
        }

        $this->action->setModule($module);
        $this->action->setController($controller);
        $this->assertSame($expectedArguments, $this->action->executeController());
    }

    public function executeControllerArgumentsProvider()
    {
        $provider = array();

        // normal arguments
        $provider[] = array(
            array('id' => 1, 'order' => 'title'),
            function ($id, $order) {
                return func_get_args();
            },
            array(1, 'title'),
        );

        // order does not matter
        $provider[] = array(
            array('id' => 2, 'order' => 'date'),
            function ($order, $id) {
                return func_get_args();
            },
            array('date', 2),
        );

        // request
        $provider[] = array(
            array('id' => 3),
            function (Request $request, $id) {
                return func_get_args();
            },
            array('@request', 3),
        );

        // action
        $provider[] = array(
            array('action' => 'list', 'page' => '3'),
            function ($action, $page) {
                return func_get_args();
            },
            array('list', '3'),
        );
        $provider[] = array(
            array('hash' => 'bump'),
            function (BaseRouteAction $action, $hash) {
                return func_get_args();
            },
            array('@action', 'bump'),
        );

        return $provider;
    }
}
