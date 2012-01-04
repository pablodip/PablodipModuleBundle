<?php

namespace Pablodip\ModuleBundle\Tests\Module;

use Pablodip\ModuleBundle\Module\Module as BaseModule;
use Symfony\Component\DependencyInjection\Container;

class Module extends BaseModule
{
    protected function configure()
    {
    }
}

class ModuleTest extends \PHPUnit_Framework_TestCase
{
    private $container;
    private $module;

    protected function setUp()
    {
        $this->container = new Container();
        $this->module = new Module($this->container);
    }

    public function testDefaultValues()
    {
        $this->assertTrue(is_string($this->module->getRouteNamePrefix()));
        $this->assertTrue(is_string($this->module->getRoutePatternPrefix()));
    }

    public function testGetContainer()
    {
        $this->assertSame($this->container, $this->module->getContainer());
    }

    public function testRouteNamePrefix()
    {
        $this->assertSame($this->module, $this->module->setRouteNamePrefix('foo_bar'));
        $this->assertSame('foo_bar', $this->module->getRouteNamePrefix());
    }

    public function testRoutePatternPrefix()
    {
        $this->assertSame($this->module, $this->module->setRoutePatternPrefix('/foo/bar'));
        $this->assertSame('/foo/bar', $this->module->getRoutePatternPrefix());
    }

    public function testParametersToPropagate()
    {
        $this->assertSame(array(), $this->module->getParametersToPropagate());
        $this->assertSame($this->module, $this->module->addParameterToPropagate('foo'));
        $this->assertSame(array('foo'), $this->module->getParametersToPropagate());
        $this->assertSame($this->module, $this->module->addParameterToPropagate('bar'));
        $this->assertSame(array('foo', 'bar'), $this->module->getParametersToPropagate());

        $this->assertSame($this->module, $this->module->addParametersToPropagate(array('ups', 'man')));
        $this->assertSame(array('foo', 'bar', 'ups', 'man'), $this->module->getParametersToPropagate());

        $this->assertSame($this->module, $this->module->setParametersToPropagate(array('a', 'b')));
        $this->assertSame(array('a', 'b'), $this->module->getParametersToPropagate());
    }

    public function testOptions()
    {
        $this->assertSame($this->module, $this->module->addOption('foo', 'bar'));
        $this->assertSame($this->module, $this->module->addOption('bar', 'foo'));
        $this->assertSame($this->module, $this->module->addOptions(array(
            'man' => 'dango',
            'mon' => 'dator',
        )));

        try {
            $this->module->addOption('foo', 'bu');
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('LogicException', $e);
        }
        try {
            $this->module->addOptions(array(
                'bar' => 'ba',
            ));
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('LogicException', $e);
        }

        $this->assertSame('bar', $this->module->getOption('foo'));
        $this->assertSame('foo', $this->module->getOption('bar'));
        $this->assertSame('dango', $this->module->getOption('man'));
        $this->assertSame('dator', $this->module->getOption('mon'));

        try {
            $this->module->getOption('no');
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
        }

        $this->assertSame(array(
            'foo' => 'bar',
            'bar' => 'foo',
            'man' => 'dango',
            'mon' => 'dator',
        ), $this->module->getOptions());

        $this->assertSame($this->module, $this->module->setOption('foo', 'ups'));
        $this->assertSame('ups', $this->module->getOption('foo'));
        $this->assertSame('foo', $this->module->getOption('bar'));
        $this->assertSame('dango', $this->module->getOption('man'));
        $this->assertSame('dator', $this->module->getOption('mon'));

        $this->assertSame($this->module, $this->module->setOption('bar', 'min'));
        $this->assertSame('ups', $this->module->getOption('foo'));
        $this->assertSame('min', $this->module->getOption('bar'));
        $this->assertSame('dango', $this->module->getOption('man'));
        $this->assertSame('dator', $this->module->getOption('mon'));

        try {
            $this->module->setOption('no', 'bar');
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
        }
    }

    public function testRequiredOptions()
    {
        $this->assertSame(array(), $this->module->getRequiredOptions());
        $this->assertSame($this->module, $this->module->addRequiredOption('foo'));
        $this->assertSame(array('foo'), $this->module->getRequiredOptions());
        $this->module->addRequiredOption('bar');
        $this->assertSame(array('foo', 'bar'), $this->module->getRequiredOptions());
    }

    public function testAddAction()
    {
        $actions = array();
        for ($i = 1; $i <= 2; $i++) {
            $actions[$i] = $action = $this->getMock('Pablodip\ModuleBundle\Action\ActionInterface');
            $action
                ->expects($this->once())
                ->method('setModule')
                ->with($this->anything())
            ;
            $action
                ->expects($this->any())
                ->method('getName')
                ->will($this->returnValue('action'.$i))
            ;
        }

        $this->assertSame($this->module, $this->module->addAction($actions[1]));
        $this->assertSame($this->module, $this->module->addAction($actions[2]));
        $this->assertSame(2, count($this->module->getActions()));
    }

    public function testAddActions()
    {
        $actions = array();
        for ($i = 1; $i <= 2; $i++) {
            $actions[$i] = $action = $this->getMock('Pablodip\ModuleBundle\Action\ActionInterface');
            $action
                ->expects($this->once())
                ->method('setModule')
                ->with($this->anything())
            ;
            $action
                ->expects($this->any())
                ->method('getName')
                ->will($this->returnValue('action'.$i))
            ;
        }

        $this->assertSame($this->module, $this->module->addActions($actions));
        $this->assertSame(2, count($this->module->getActions()));
    }

    public function testHasAction()
    {
        $action = $this->getMock('Pablodip\ModuleBundle\Action\ActionInterface');
        $action
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('foo'))
        ;

        $this->module->addAction($action);

        $this->assertTrue($this->module->hasAction('foo'));
        $this->assertFalse($this->module->hasAction('bar'));
    }

    public function testGetAction()
    {
        $actions = array();
        for ($i = 1; $i <= 2; $i++) {
            $actions[$i] = $action = $this->getMock('Pablodip\ModuleBundle\Action\ActionInterface');
            $action
                ->expects($this->any())
                ->method('getName')
                ->will($this->returnValue('action'.$i))
            ;
        }

        $this->module->addActions($actions);

        $this->assertSame($actions[1], $this->module->getAction('action1'));
        $this->assertSame($actions[2], $this->module->getAction('action2'));
    }

    public function testControllerPreExecutes()
    {
        $preExecute1 = function () {};
        $preExecute2 = function () {};

        $this->assertSame($this->module, $this->module->addControllerPreExecute($preExecute1));
        $this->assertSame(array($preExecute1), $this->module->getControllerPreExecutes());
        $this->assertSame($this->module, $this->module->addControllerPreExecute($preExecute2));
        $this->assertSame(array($preExecute1, $preExecute2), $this->module->getControllerPreExecutes());
    }

    public function testGenerateActionUrl()
    {
        $routeNamePrefix = 'my_prefix';
        $actionRouteName = 'list';
        $parameters = array('foo' => 'bar', 'bar' => 'foo');
        $absolute = false;
        $url = '/ups/bump';

        $router = $this->getMock('Symfony\Component\Routing\RouterInterface');
        $router
            ->expects($this->once())
            ->method('generate')
            ->with($routeNamePrefix.'_'.$actionRouteName, $parameters, $absolute)
            ->will($this->returnValue($url))
        ;

        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $container
            ->expects($this->any())
            ->method('get')
            ->with('router')
            ->will($this->returnValue($router))
        ;

        $module = new Module($container);
        $module->setRouteNamePrefix($routeNamePrefix);

        $this->assertSame($url, $module->generateActionUrl($actionRouteName, $parameters, $absolute));
    }

    public function testCreateView()
    {
        $view = $this->module->createView();
        $this->assertInstanceOf('Pablodip\ModuleBundle\Module\ModuleView', $view);
    }
}
