<?php

namespace Pablodip\ModuleBundle\Tests\Extension\Molino;

use Pablodip\ModuleBundle\Extension\Molino\MandangoMolinoExtension;
use Pablodip\ModuleBundle\Module\Module;

class MandangoMolinoExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testRegisterMolino()
    {
        $mandango = $this->getMockBuilder('Mandango\Mandango')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $container
            ->expects($this->any())
            ->method('get')
            ->with('mandango')
            ->will($this->returnValue($mandango))
        ;

        $module = $this->getMock('Pablodip\ModuleBundle\Module\ModuleInterface');
        $module
            ->expects($this->any())
            ->method('getContainer')
            ->will($this->returnValue($container))
        ;

        $extension = new MandangoMolinoExtension();
        $extension->setModule($module);
        $extension->defineConfiguration();

        $molino = $extension->getMolino();
        $this->assertInstanceOf('Molino\Mandango\Molino', $molino);
        $this->assertSame($mandango, $molino->getMandango());
    }
}
