<?php

namespace Pablodip\ModuleBundle\Tests\Extension;

use Pablodip\ModuleBundle\Extension\BaseExtension as BaseBaseExtension;
use Pablodip\ModuleBundle\Module\Module;

class BaseExtension extends BaseBaseExtension
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

class BaseExtensionTest extends \PHPUnit_Framework_TestCase
{
    private $extension;

    protected function setUp()
    {
        $this->extension = new BaseExtension();
    }

    public function testSetGetModule()
    {
        $module = $this->getMock('Pablodip\ModuleBundle\Module\ModuleInterface');

        $this->extension->setModule($module);
        $this->assertSame($module, $this->extension->getModule());
    }

    /**
     * @expectedException \LogicException
     */
    public function testSetModuleAlreadySet()
    {
        $module1 = $this->getMock('Pablodip\ModuleBundle\Module\ModuleInterface');
        $module2 = $this->getMock('Pablodip\ModuleBundle\Module\ModuleInterface');

        $this->extension->setModule($module1);
        $this->extension->setModule($module2);
    }

    /**
     * @expectedException \LogicException
     */
    public function testGetModuleNotSet()
    {
        $this->extension->getModule();
    }
}
