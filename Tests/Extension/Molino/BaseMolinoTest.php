<?php

namespace Pablodip\ModuleBundle\Tests\Extension\Molino;

use Pablodip\ModuleBundle\Extension\Molino\BaseMolinoExtension as OriginalBaseMolinoExtension;
use Pablodip\ModuleBundle\Module\Module;

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
    private $molino;
    private $extension;

    protected function setUp()
    {
        $this->molino = $this->getMock('Molino\MolinoInterface');
        BaseMolinoExtension::$registerMolino = $this->molino;
        $this->extension = new BaseMolinoExtension();
    }

    public function testGetName()
    {
        $this->assertSame('molino', $this->extension->getName());
    }

    public function testDefineConfigurationRegisterMolino()
    {
        $this->extension->defineConfiguration();
        $this->assertSame($this->molino, $this->extension->getMolino());
        BaseMolinoExtension::$registerMolino = $this->getMock('Molino\MolinoInterface');
        $this->assertSame($this->molino, $this->extension->getMolino());
    }
}
