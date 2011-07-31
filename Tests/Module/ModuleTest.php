<?php

namespace Pablodip\ModuleBundle\Tests\Module;

use Pablodip\ModuleBundle\Module\Module as BaseModule;
use Symfony\Component\DependencyInjection\Container;

class Module extends BaseModule
{
    protected function configure()
    {
        $this->setDataClass('foobar');
    }
}

class ModuleTest extends \PHPUnit_Framework_TestCase
{
    private $module;

    protected function setUp()
    {
        $this->module = new Module(new Container());
    }

    public function testConfigure()
    {
        $this->assertTrue(is_string($this->module->getRouteNamePrefix()));
        $this->assertTrue(is_string($this->module->getRoutePatternPrefix()));
    }

    public function testDataClass()
    {
        $this->assertSame($this->module, $this->module->setDataClass('ups'));
        $this->assertSame('ups', $this->module->getDataClass());
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

    public function testControllerPreExecutes()
    {
        $preExecute1 = function () {};
        $preExecute2 = function () {};

        $this->assertSame($this->module, $this->module->addControllerPreExecute($preExecute1));
        $this->assertSame(array($preExecute1), $this->module->getControllerPreExecutes());
        $this->assertSame($this->module, $this->module->addControllerPreExecute($preExecute2));
        $this->assertSame(array($preExecute1, $preExecute2), $this->module->getControllerPreExecutes());
    }

    public function testCreateView()
    {
        $view = $this->module->createView();
        $this->assertInstanceOf('Pablodip\ModuleBundle\Module\ModuleView', $view);
    }
}
