<?php

namespace Pablodip\ModuleBundle\Tests\Module;

use Pablodip\ModuleBundle\Module\MolinoModule as BaseModuleMolino;

class ModuleMolino extends BaseModuleMolino
{
    static public $registerMolino;

    protected function defineConfiguration()
    {
    }

    protected function registerMolino()
    {
        return static::$registerMolino;
    }
}

class MolinoModuleTest extends \PHPUnit_Framework_TestCase
{
    private $molino;
    private $container;
    private $module;

    protected function setUp()
    {
        $this->molino = $this->getMock('Molino\MolinoInterface');
        ModuleMolino::$registerMolino = $this->molino;
        $this->container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $this->module = new ModuleMolino($this->container);
    }

    public function testRegisterMolino()
    {
        $this->assertSame($this->molino, $this->module->getMolino());
    }

    public function testRegisterMolinoOnce()
    {
        ModuleMolino::$registerMolino = $this->getMock('Molino\MolinoInterface');
        $this->assertSame($this->molino, $this->module->getMolino());
    }
}
