<?php

namespace Pablodip\ModuleBundle\Tests\Action;

use Pablodip\ModuleBundle\Routing\ModuleFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Container;

class ModuleFileLoaderTest extends \PHPUnit_Framework_TestCase
{
    private $loader;

    protected function setUp()
    {
        $this->loader = new ModuleFileLoader(new FileLocator(), new Container());
    }

    public function testLoad()
    {
        $collection = $this->loader->load(__DIR__.'/../Fixtures/CRUDModule.php');

        $this->assertInstanceOf('Symfony\Component\Routing\RouteCollection', $collection);
        $this->assertSame(4, count($collection->all()));

        foreach (array(
            'list'   => array('', array(), array()),
            'cre'    => array('/create', array('_method' => 'POST')),
            'update' => array('/up', array('_method' => 'PUT')),
            'delete' => array('/delete', array('_method' => 'DELETE')),
        ) as $name => $data) {
            $route = $collection->get('my_crud_'.$name);
            $this->assertNotNull($route);
            $this->assertSame('/foo-bar'.$data[0], $route->getPattern());
            $this->assertSame($data[1], $route->getRequirements());
        }
    }

    public function testLoadHomepage()
    {
        $collection = $this->loader->load(__DIR__.'/../Fixtures/NoRoutePrefixesModule.php');

        $route = $collection->get('list');
        $this->assertNotNull($route);
        $this->assertSame('/', $route->getPattern());
    }

    public function testSupports()
    {
        $fixture = __FILE__;

        $this->assertTrue($this->loader->supports($fixture, 'pablodip_module'));
        $this->assertFalse($this->loader->supports($fixture, 'foo'));
        $this->assertFalse($this->loader->supports('foo.bar', 'pablodip_module'));
    }
}
