<?php

namespace Pablodip\ModuleBundle\Tests\Action;

use Pablodip\ModuleBundle\Routing\ModuleDirectoryLoader;
use Pablodip\ModuleBundle\Module\ModuleManager;
use Symfony\Component\Config\FileLocator;

class ModuleDirectoryLoaderTest extends \PHPUnit_Framework_TestCase
{
    private $container;
    private $parser;
    private $moduleManager;
    private $loader;

    protected function setUp()
    {
        $this->container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $this->parser = $this->getMock('Pablodip\ModuleBundle\Module\ModuleNameParserInterface');
        $this->moduleManager = new ModuleManager($this->container, $this->parser);
        $this->loader = new ModuleDirectoryLoader(new FileLocator(), $this->moduleManager);
    }

    public function testLoad()
    {
        $collection = $this->loader->load(__DIR__.'/..');

        $this->assertInstanceOf('Symfony\Component\Routing\RouteCollection', $collection);
        $this->assertSame(6, count($collection->all()));
    }

    public function testSupports()
    {
        $fixture = __DIR__;

        $this->assertTrue($this->loader->supports($fixture, 'module'));
        $this->assertFalse($this->loader->supports($fixture, 'foo'));
        $this->assertFalse($this->loader->supports('foo.bar', 'module'));
    }
}
