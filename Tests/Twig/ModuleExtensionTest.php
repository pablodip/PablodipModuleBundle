<?php

namespace Pablodip\ModuleBundle\Tests\Twig;

use Pablodip\ModuleBundle\Twig\ModuleExtension;

class ModuleExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetModule()
    {
        $moduleName = 'foobar';
        $view = new \ArrayObject();

        $module = $this->getMock('Pablodip\ModuleBundle\Module\ModuleInterface');
        $module
            ->expects($this->once())
            ->method('createView')
            ->will($this->returnValue($view))
        ;

        $moduleManager = $this->getMock('Pablodip\ModuleBundle\Module\ModuleManagerInterface');
        $moduleManager
            ->expects($this->once())
            ->method('get')
            ->with($moduleName)
            ->will($this->returnValue($module))
        ;

        $extension = new ModuleExtension($moduleManager);
        $this->assertSame($view, $extension->getModule($moduleName));
    }
}
