<?php

namespace Pablodip\ModuleBundle\Tests\Module;

use Pablodip\ModuleBundle\Module\Module;
use Pablodip\ModuleBundle\Module\ModuleManager;

class ModuleManagerModule1 extends Module
{
    protected function defineConfiguration()
    {
    }
}

class ModuleManagerModule2 extends Module
{
    protected function defineConfiguration()
    {
    }
}

class ModuleManagerTest extends \PHPUnit_Framework_TestCase
{
    private $container;
    private $manager;

    protected function setUp()
    {
        $this->container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $this->manager = new ModuleManager($this->container);
    }

    public function testGet()
    {
        $module1 = $this->manager->get('Pablodip\ModuleBundle\Tests\Module\ModuleManagerModule1');
        $this->assertInstanceOf('Pablodip\ModuleBundle\Tests\Module\ModuleManagerModule1', $module1);
        $this->assertSame($this->container, $module1->getContainer());
        $this->assertSame($module1, $this->manager->get('Pablodip\ModuleBundle\Tests\Module\ModuleManagerModule1'));
        $module2 = $this->manager->get('Pablodip\ModuleBundle\Tests\Module\ModuleManagerModule2');
        $this->assertInstanceOf('Pablodip\ModuleBundle\Tests\Module\ModuleManagerModule2', $module2);
        $this->assertSame($this->container, $module2->getContainer());
    }
}
