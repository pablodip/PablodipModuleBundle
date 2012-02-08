<?php

namespace Pablodip\ModuleBundle\Tests\Module;

use Symfony\Component\ClassLoader\UniversalClassLoader;
use Pablodip\ModuleBundle\Module\ModuleNameParser;

class ModuleNameParserTest extends \PHPUnit_Framework_TestCase
{
    private $loader;
    private $kernel;
    private $parser;

    protected function setUp()
    {
        $this->loader = new UniversalClassLoader();
        $this->loader->registerNamespaces(array(
            'TestBundle'      => __DIR__.'/../Fixtures',
            'TestApplication' => __DIR__.'/../Fixtures',
        ));
        $this->loader->register();

        $bundles = array(
            'PablodipFooBundle' => array($this->getBundle('TestBundle\Pablodip\FooBundle', 'PablodipFooBundle')),
            'WhiteOctoberBarBundle' => array($this->getBundle('TestBundle\WhiteOctober\BarBundle', 'WhiteOctoberBarBundle')),
        );

        $this->kernel = $this->getMock('Symfony\Component\HttpKernel\KernelInterface');
        $this->kernel
            ->expects($this->any())
            ->method('getBundle')
            ->will($this->returnCallback(function ($bundle) use ($bundles) {
                return $bundles[$bundle];
            }))
        ;

        $this->parser = new ModuleNameParser($this->kernel);
    }

    protected function tearDown()
    {
        spl_autoload_unregister(array($this->loader, 'loadClass'));
        $this->loader = null;
    }

    public function testParse()
    {
        $this->assertSame('TestBundle\Pablodip\FooBundle\Module\BlogModule', $this->parser->parse('PablodipFooBundle:Blog'));
        $this->assertSame('TestBundle\WhiteOctober\BarBundle\Module\AdminModule', $this->parser->parse('WhiteOctoberBarBundle:Admin'));
        $this->assertSame('TestBundle\Pablodip\FooBundle\Module\Ups\BarModule', $this->parser->parse('PablodipFooBundle:Ups/Bar'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testParseNotValidNotation()
    {
        $this->parser->parse('foo');
    }

    private function getBundle($namespace, $name)
    {
        $bundle = $this->getMock('Symfony\Component\HttpKernel\Bundle\BundleInterface');
        $bundle->expects($this->any())->method('getName')->will($this->returnValue($name));
        $bundle->expects($this->any())->method('getNamespace')->will($this->returnValue($namespace));

        return $bundle;
    }
}
