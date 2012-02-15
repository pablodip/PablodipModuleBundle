<?php

namespace Pablodip\ModuleBundle\Tests\Module;

use Pablodip\ModuleBundle\Module\Module as BaseModule;
use Pablodip\ModuleBundle\Extension\BaseExtension;
use Symfony\Component\DependencyInjection\Container;

abstract class BaseModuleExtension extends BaseExtension
{
    public function defineConfiguration()
    {
    }

    public function configure()
    {
    }

    public function parseConfiguration()
    {
    }
}

class ModuleExtension1 extends BaseModuleExtension
{
    public function getName()
    {
        return 'extension1';
    }
}

class ModuleExtension2 extends BaseModuleExtension
{
    public function getName()
    {
        return 'extension2';
    }
}

class Module extends BaseModule
{
    public static $registerExtensions;

    protected function registerExtensions()
    {
        return self::$registerExtensions;
    }

    protected function defineConfiguration()
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

        Module::$registerExtensions = array();
        $this->module = new Module($this->container);
    }

    public function testGetContainer()
    {
        $this->assertSame($this->container, $this->module->getContainer());
    }

    public function testRegisterExtensionsGetExtensions()
    {
        Module::$registerExtensions = array(
            $extension1 = new ModuleExtension1(),
            $extension2 = new ModuleExtension2(),
        );
        $module = new Module($this->container);

        $this->assertSame(array(
            $extension1->getName() => $extension1,
            $extension2->getName() => $extension2,
        ), $module->getExtensions());

        $this->assertSame($module, $extension1->getModule());
        $this->assertSame($module, $extension2->getModule());
    }

    /**
     * @expectedException \LogicException
     */
    public function testRegisterExtensionsNotExtensionInterface()
    {
        Module::$registerExtensions = array(new \DateTime());
        new Module($this->container);
    }

    /**
     * @expectedException \LogicException
     */
    public function testRegisterExtensionsTwiceSameExtension()
    {
        Module::$registerExtensions = array(
            new ModuleExtension1(),
            new ModuleExtension1(),
        );
        new Module($this->container);
    }

    public function testGetExtension()
    {
        Module::$registerExtensions = array(
            $extension1 = new ModuleExtension1(),
            $extension2 = new ModuleExtension2(),
        );
        $module = new Module($this->container);

        $this->assertSame($extension1, $module->getExtension('extension1'));
        $this->assertSame($extension2, $module->getExtension('extension2'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetExtensionNotExists($value='')
    {
        Module::$registerExtensions = array(
            $extension1 = new ModuleExtension1(),
        );
        $module = new Module($this->container);

        $module->getExtension('extension2');
    }

    public function testEmptyRoutePrefixes()
    {
        $this->assertSame('', $this->module->getRouteNamePrefix());
        $this->assertSame('', $this->module->getRoutePatternPrefix());
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

    public function testSetRoutePrefixes()
    {
        $this->assertSame($this->module, $this->module->setRoutePrefixes('foo_bar_', '/foo/bar'));
        $this->assertSame('foo_bar_', $this->module->getRouteNamePrefix());
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

    public function testSetActionOption()
    {
        $actionName = 'list';
        $optionName = 'template';
        $optionValue = 'foo';

        $action = $this->getMock('Pablodip\ModuleBundle\Action\ActionInterface');
        $action
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($actionName))
        ;
        $action
            ->expects($this->once())
            ->method('setOption')
            ->with($optionName, $optionValue)
        ;

        $this->module->addAction($action);

        $this->assertSame($this->module, $this->module->setActionOption($actionName, $optionName, $optionValue));
    }

    public function testGetRouteActions()
    {
        $actions = array();
        for ($i = 1; $i <= 4; $i++) {
            if ($i % 2) {
                $class = 'Pablodip\ModuleBundle\Action\ActionInterface';
            } else {
                $class = 'Pablodip\ModuleBundle\Action\RouteActionInterface';
            }

            $actions[$i] = $action = $this->getMock($class);
            $action
                ->expects($this->any())
                ->method('getName')
                ->will($this->returnValue('action'.$i))
            ;
        }

        $this->module->addActions($actions);
        $this->assertSame(array(
            'action2' => $actions[2],
            'action4' => $actions[4],
        ), $this->module->getRouteActions());
    }

    public function testControllerPreExecutes()
    {
        $preExecute1 = function () {};

        $this->assertSame($this->module, $this->module->addControllerPreExecute($preExecute1));
        $this->assertSame(array($preExecute1), $this->module->getControllerPreExecutes());
        $this->assertSame($this->module, $this->module->addControllerPreExecute('intval'));
        $this->assertSame(array($preExecute1, 'intval'), $this->module->getControllerPreExecutes());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAddControllerPreExecuteNotCallable()
    {
        $this->module->addControllerPreExecute('foo');
    }

    public function testGenerateModuleUrl()
    {
        $routeNamePrefix = 'my_prefix_';
        $actionName = 'list';
        $actionRouteName = 'my_list';
        $parameters = array('foo' => 'bar', 'bar' => 'foo');
        $absolute = false;
        $url = '/ups/bump';

        $router = $this->getMock('Symfony\Component\Routing\RouterInterface');
        $router
            ->expects($this->once())
            ->method('generate')
            ->with($routeNamePrefix.$actionRouteName, $parameters, $absolute)
            ->will($this->returnValue($url))
        ;

        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $container
            ->expects($this->any())
            ->method('get')
            ->with('router')
            ->will($this->returnValue($router))
        ;

        $action = $this->getMock('Pablodip\ModuleBundle\Action\RouteActionInterface');
        $action
            ->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($actionName))
        ;
        $action
            ->expects($this->once())
            ->method('getRouteName')
            ->will($this->returnValue($actionRouteName))
        ;

        $module = new Module($container);
        $module->addAction($action);
        $module->setRouteNamePrefix($routeNamePrefix);

        $this->assertSame($url, $module->generateModuleUrl($actionName, $parameters, $absolute));
    }

    public function testForward()
    {
        $actionName = 'list';
        $attributes = array('sort' => 'name');
        $retval = new \ArrayObject();

        $httpKernel = $this->getMockBuilder('Symfony\Bundle\FrameworkBundle\HttpKernel')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $httpKernel
            ->expects($this->once())
            ->method('forward')
            ->with('PablodipModuleBundle:Module:execute', array_merge($attributes, array(
                '_module.module' => 'Pablodip\ModuleBundle\Tests\Module\Module',
                '_module.action' => $actionName,
            )))
            ->will($this->returnValue($retval))
        ;

        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $container
            ->expects($this->any())
            ->method('get')
            ->with('http_kernel')
            ->will($this->returnValue($httpKernel))
        ;

        $module = new Module($container);
        $this->assertSame($retval, $module->forward($actionName, $attributes));
    }

    public function testCreateView()
    {
        $view = $this->module->createView();
        $this->assertInstanceOf('Pablodip\ModuleBundle\Module\ModuleView', $view);
    }
}
