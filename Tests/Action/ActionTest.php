<?php

namespace Pablodip\ModuleBundle\Tests\Action;

use Pablodip\ModuleBundle\Action\Action as BaseAction;
use Pablodip\ModuleBundle\Action\ActionView;

class Action extends BaseAction
{
    static public $name;
    static public $routeNameSuffix;
    static public $routePatternSuffix;

    protected function configure()
    {
        $this
            ->setName(static::$name)
            ->setRouteNameSuffix(static::$routeNameSuffix)
            ->setRoutePatternSuffix(static::$routePatternSuffix)
        ;
    }

    public function executeController()
    {
    }
}

class ActionTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        Action::$name = 'action.test';
        Action::$routeNameSuffix = 'foo';
        Action::$routePatternSuffix = '/bar';
    }

    public function testConfigure()
    {
        $action = new Action();
        $this->assertSame('action.test', $action->getName());
        $this->assertSame('foo', $action->getRouteNameSuffix());
        $this->assertSame('/bar', $action->getRoutePatternSuffix());
    }

    public function testRouteNameSuffix()
    {
        $action = new Action();

        $this->assertSame($action, $action->setRouteNameSuffix('list'));
        $this->assertSame('list', $action->getRouteNameSuffix());
    }

    public function testRoutePatternSuffix()
    {
        $action = new Action();

        // empty string to /
        $this->assertSame($action, $action->setRoutePatternSuffix(''));
        $this->assertSame('', $action->getRoutePatternSuffix());

        // normal
        $this->assertSame($action, $action->setRoutePatternSuffix('/list'));
        $this->assertSame('/list', $action->getRoutePatternSuffix());
    }

    public function testRouteDefaults()
    {
        $action = new Action();

        $routeDefaults = array('_controller' => 'foobar');
        $this->assertSame($action, $action->setRouteDefaults($routeDefaults));
        $this->assertSame($routeDefaults, $action->getRouteDefaults());
    }

    public function testRouteRequirements()
    {
        $action = new Action();

        $routeRequirements = array('_method' => 'GET');
        $this->assertSame($action, $action->setRouteRequirements($routeRequirements));
        $this->assertSame($routeRequirements, $action->getRouteRequirements());
    }

    public function testSetRoute()
    {
        $action = new Action();

        // normal
        $this->assertSame($action, $action->setRoute('edit', '/edit'));
        $this->assertSame('edit', $action->getRouteNameSuffix());
        $this->assertSame('/edit', $action->getRoutePatternSuffix());
        $this->assertSame(array(), $action->getRouteDefaults());
        $this->assertSame(array(), $action->getRouteRequirements());

        // with defaults
        $this->assertSame($action, $action->setRoute('create', '/creando', $routeDefaults = array('foo' => 'bar')));
        $this->assertSame('create', $action->getRouteNameSuffix());
        $this->assertSame('/creando', $action->getRoutePatternSuffix());
        $this->assertSame($routeDefaults, $action->getRouteDefaults());
        $this->assertSame(array(), $action->getRouteRequirements());

        // with defaults and requirements
        $routeDefaults = array('ups' => 'man');
        $routeRequirements = array('_foo' => 'barfoo');
        $this->assertSame($action, $action->setRoute('update', '/upgrade', $routeDefaults, $routeRequirements));
        $this->assertSame('update', $action->getRouteNameSuffix());
        $this->assertSame('/upgrade', $action->getRoutePatternSuffix());
        $this->assertSame($routeDefaults, $action->getRouteDefaults());
        $this->assertSame($routeRequirements, $action->getRouteRequirements());
    }

    public function testOptions()
    {
        $action = new Action();

        $this->assertSame($action, $action->addOption('foo', 'bar'));
        $this->assertSame($action, $action->addOption('bar', 'foo'));
        $this->assertSame($action, $action->addOptions(array(
            'man' => 'dango',
            'mon' => 'dator',
        )));

        try {
            $action->addOption('foo', 'bu');
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('LogicException', $e);
        }
        try {
            $action->addOptions(array(
                'bar' => 'ba',
            ));
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('LogicException', $e);
        }

        $this->assertSame('bar', $action->getOption('foo'));
        $this->assertSame('foo', $action->getOption('bar'));
        $this->assertSame('dango', $action->getOption('man'));
        $this->assertSame('dator', $action->getOption('mon'));

        try {
            $action->getOption('no');
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
        }

        $this->assertSame(array(
            'foo' => 'bar',
            'bar' => 'foo',
            'man' => 'dango',
            'mon' => 'dator',
        ), $action->getOptions());

        $this->assertSame($action, $action->setOption('foo', 'ups'));
        $this->assertSame('ups', $action->getOption('foo'));
        $this->assertSame('foo', $action->getOption('bar'));
        $this->assertSame('dango', $action->getOption('man'));
        $this->assertSame('dator', $action->getOption('mon'));

        $this->assertSame($action, $action->setOption('bar', 'min'));
        $this->assertSame('ups', $action->getOption('foo'));
        $this->assertSame('min', $action->getOption('bar'));
        $this->assertSame('dango', $action->getOption('man'));
        $this->assertSame('dator', $action->getOption('mon'));

        try {
            $action->setOption('no', 'bar');
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
        }
    }

    public function testHas()
    {
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');

        $container
            ->expects($this->once())
            ->method('has')
            ->with('foo')
            ->will($this->returnValue(true))
        ;

        $action = new Action();
        $action->setContainer($container);

        $this->assertTrue($action->has('foo'));
    }

    public function testGet()
    {
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');

        $service = new \DateTime();

        $container
            ->expects($this->once())
            ->method('get')
            ->with('bar')
            ->will($this->returnValue($service))
        ;

        $action = new Action();
        $action->setContainer($container);

        $this->assertSame($service, $action->get('bar'));
    }

    public function testCreateView()
    {
        $action = new Action();

        $view = $action->createView();
        $this->assertEquals(new ActionView($action), $view);
        $this->assertNotSame($view, $action->createView());
    }
}
