<?php

namespace Pablodip\ModuleBundle\Tests\Extension\Data;

use Pablodip\ModuleBundle\Extension\Data\DataExtension;
use Pablodip\ModuleBundle\Module\Module;

class DataExtensionModule extends Module
{
    protected function configure()
    {
    }
}

class Article
{
    private $title;
    private $content;

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getContent()
    {
        return $this->content;
    }
}

class DataExtensionTest extends \PHPUnit_Framework_TestCase
{
    private $module;
    private $extension;

    protected function setUp()
    {
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');

        $this->module = new DataExtensionModule($container);

        $this->extension = new DataExtension();
        $this->extension->setModule($this->module);
    }

    public function testDefineConfigurationDataClassOption()
    {
        $this->extension->defineConfiguration();
        $this->assertNull($this->module->getOption('dataClass'));
    }

    public function testDefineConfigurationDataFieldsOption()
    {
        $this->extension->defineConfiguration();
        $this->assertSame(array(), $this->module->getOption('dataFields'));
    }

    public function testDefineConfigurationDataFieldGuessersOption()
    {
        $this->extension->defineConfiguration();
        $this->assertSame(array(), $this->module->getOption('dataFieldGuessers'));
    }

    public function testDefineConfigurationSetDataFieldValueCallback()
    {
        $this->extension->defineConfiguration();
        $this->assertTrue($this->module->hasCallback('setDataFieldValue'));

        $article = new Article();
        $this->module->call('setDataFieldValue', $article, 'title', 'foo');
        $this->assertSame('foo', $article->getTitle());
        $this->module->call('setDataFieldValue', $article, 'content', 'bar');
        $this->assertSame('bar', $article->getContent());
    }

    public function testDefineConfigurationGetDataFieldValueCallback()
    {
        $this->extension->defineConfiguration();
        $this->assertTrue($this->module->hasCallback('getDataFieldValue'));

        $article = new Article();
        $article->setTitle('foo');
        $article->setContent('bar');
        $this->assertSame('foo', $this->module->call('getDataFieldValue', $article, 'title'));
        $this->assertSame('bar', $this->module->call('getDataFieldValue', $article, 'content'));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testParseConfigurationDataClassNull()
    {
        $this->extension->defineConfiguration();
        $this->extension->parseConfiguration();
    }
}
