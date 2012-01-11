<?php

namespace Pablodip\ModuleBundle\Tests\Extension\Data;

use Pablodip\ModuleBundle\Extension\Data\DataExtension;
use Pablodip\ModuleBundle\Module\Module;

class DataExtensionModule extends Module
{
    protected function defineConfiguration()
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
        $this->assertTrue($this->module->hasOption('dataFields'));
    }

    public function testDefineConfigurationDataFieldGuessersOption()
    {
        $this->extension->defineConfiguration();
        $this->assertTrue($this->module->hasOption('dataFieldGuessers'));
    }

    public function testDefineConfigurationSetDataFieldValueCallback()
    {
        $this->extension->defineConfiguration();
        $this->assertSame(array($this->extension, 'setDataFieldValue'), $this->module->getCallback('setDataFieldValue'));
    }

    public function testSetDataFieldValue()
    {
        $this->extension->defineConfiguration();

        $article = new Article();
        $this->extension->setDataFieldValue($article, 'title', 'foo');
        $this->assertSame('foo', $article->getTitle());
        $this->extension->setDataFieldValue($article, 'content', 'bar');
        $this->assertSame('bar', $article->getContent());
    }

    public function testDefineConfigurationGetDataFieldValueCallback()
    {
        $this->extension->defineConfiguration();
        $this->assertSame(array($this->extension, 'getDataFieldValue'), $this->module->getCallback('getDataFieldValue'));
    }

    public function testGetDataFieldValue()
    {
        $this->extension->defineConfiguration();

        $article = new Article();
        $article->setTitle('foo');
        $article->setContent('bar');
        $this->assertSame('foo', $this->extension->getDataFieldValue($article, 'title'));
        $this->assertSame('bar', $this->extension->getDataFieldValue($article, 'content'));
    }

    public function testDefineConfigurationDataFromArrayCallback()
    {
        $this->extension->defineConfiguration();
        $this->assertSame(array($this->extension, 'dataFromArray'), $this->module->getCallback('dataFromArray'));
    }

    public function testDataFromArray()
    {
        $this->extension->defineConfiguration();
        $this->module->getOption('dataFields')->add(array(
            'title'   => array(),
            'content' => array(),
        ));

        $article = new Article();
        $this->assertTrue($this->extension->dataFromArray($article, array(
            'title'   => 'foo',
            'content' => 'bar',
        )));
        $this->assertSame('foo', $article->getTitle());
        $this->assertSame('bar', $article->getContent());
    }

    public function testDataFromArrayExtraFields()
    {
        $this->extension->defineConfiguration();

        $article = new Article();
        $this->assertFalse($this->extension->dataFromArray($article, array(
            'title' => 'foo',
            'ups'   => 'bump',
        )));
    }

    public function testDefineConfigurationDataToArrayCallback()
    {
        $this->extension->defineConfiguration();
        $this->assertSame(array($this->extension, 'dataToArray'), $this->module->getCallback('dataToArray'));
    }

    public function testDataToArray()
    {
        $this->extension->defineConfiguration();
        $this->module->getOption('dataFields')->add(array(
            'title'   => array(),
            'content' => array(),
        ));

        $article = new Article();
        $article->setTitle('foo');
        $article->setContent('bar');
        $this->assertSame(array(
            'title'   => 'foo',
            'content' => 'bar',
        ), $this->extension->dataToArray($article));
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
