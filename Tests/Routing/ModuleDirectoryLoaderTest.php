<?php

namespace Pablodip\ModuleBundle\Tests\Action;

use Pablodip\ModuleBundle\Routing\ModuleDirectoryLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Container;

class ModuleDirectoryLoaderTest extends \PHPUnit_Framework_TestCase
{
    private $loader;

    protected function setUp()
    {
        $this->loader = new ModuleDirectoryLoader(new FileLocator(), new Container());
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
