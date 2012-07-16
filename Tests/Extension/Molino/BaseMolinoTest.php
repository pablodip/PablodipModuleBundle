<?php

namespace Pablodip\ModuleBundle\Tests\Extension\Molino;

use Pablodip\ModuleBundle\Extension\Molino\BaseMolinoExtension as OriginalBaseMolinoExtension;
use Pablodip\ModuleBundle\Module\Module;
use Symfony\Component\EventDispatcher\EventDispatcher;

class BaseMolinoExtension extends OriginalBaseMolinoExtension
{
    static public $registerMolino;

    protected function registerMolino()
    {
        return static::$registerMolino;
    }
}

class BaseMolinoExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testWithoutEventDispatcher()
    {
        $extension = new BaseMolinoExtension();
        $this->assertFalse($extension->isEvented());
        $this->assertNull($extension->getEventDispatcher());
    }

    public function testWithEventDispatcher()
    {
        $eventDispatcher = new EventDispatcher();
        $extension = new BaseMolinoExtension($eventDispatcher);
        $this->assertTrue($extension->isEvented());
        $this->assertSame($eventDispatcher, $extension->getEventDispatcher());
    }

    public function testGetName()
    {
        $extension = new BaseMolinoExtension();
        $this->assertSame('molino', $extension->getName());
    }

    public function testDefineConfigurationRegisterMolino()
    {
        $extension = new BaseMolinoExtension();
        BaseMolinoExtension::$registerMolino = $molino = $this->getMock('Molino\MolinoInterface');
        $extension->defineConfiguration();
        $this->assertSame($molino, $extension->getMolino());
        BaseMolinoExtension::$registerMolino = $this->getMock('Molino\MolinoInterface');
        $this->assertSame($molino, $extension->getMolino());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testDefineConfigurationRegisterMolinoNotMolinoInterfaceInstance()
    {
        BaseMolinoExtension::$registerMolino = new \ArrayObject();
        $extension = new BaseMolinoExtension();
        $extension->defineConfiguration();
    }

    public function testDefineConfigurationRegisterMolinoIsEvent()
    {
        BaseMolinoExtension::$registerMolino = $molino = $this->getMock('Molino\MolinoInterface');
        $eventDispatcher = new EventDispatcher();
        $extension = new BaseMolinoExtension($eventDispatcher);
        $extension->defineConfiguration();
        $eventMolino = $extension->getMolino();
        $this->assertInstanceOf('Molino\EventMolino', $eventMolino);
        $this->assertSame($molino, $eventMolino->getMolino());
    }
}
