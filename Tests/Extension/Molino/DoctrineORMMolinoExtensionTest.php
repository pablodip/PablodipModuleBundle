<?php

namespace Pablodip\ModuleBundle\Tests\Extension\Molino;

use Pablodip\ModuleBundle\Extension\Molino\DoctrineORMMolinoExtension;
use Pablodip\ModuleBundle\Module\Module;

class Doctrine
{
    private $entityManager;

    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getEntityManager()
    {
        return $this->entityManager;
    }
}

class DoctrineORMMolinoExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testRegisterMolino()
    {
        $entityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $doctrine = new Doctrine($entityManager);

        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $container
            ->expects($this->any())
            ->method('get')
            ->with('doctrine')
            ->will($this->returnValue($doctrine))
        ;

        $module = $this->getMock('Pablodip\ModuleBundle\Module\ModuleInterface');
        $module
            ->expects($this->any())
            ->method('getContainer')
            ->will($this->returnValue($container))
        ;

        $extension = new DoctrineORMMolinoExtension();
        $extension->setModule($module);
        $extension->defineConfiguration();

        $molino = $extension->getMolino();
        $this->assertInstanceOf('Molino\Doctrine\ORM\Molino', $molino);
        $this->assertSame($entityManager, $molino->getEntityManager());
    }
}
