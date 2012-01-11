<?php

namespace Pablodip\ModuleBundle\Tests\Extension\Model;

use Pablodip\ModuleBundle\Extension\Model\ModelExtension;
use Pablodip\ModuleBundle\Module\Module;

class ModelExtensionModule extends Module
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

class ModelExtensionTest extends \PHPUnit_Framework_TestCase
{
    private $module;
    private $extension;

    protected function setUp()
    {
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');

        $this->module = new ModelExtensionModule($container);
        $this->extension = new ModelExtension();
        $this->extension->setModule($this->module);
    }

    public function testDefineConfigurationModelClassOption()
    {
        $this->extension->defineConfiguration();
        $this->assertNull($this->module->getOption('modelClass'));
    }

    public function testDefineConfigurationModelFieldsOption()
    {
        $this->extension->defineConfiguration();
        $this->assertTrue($this->module->hasOption('modelFields'));
    }

    public function testDefineConfigurationModelFieldGuessersOption()
    {
        $this->extension->defineConfiguration();
        $this->assertTrue($this->module->hasOption('modelFieldGuessers'));
    }

    public function testDefineConfigurationSetModelFieldValueCallback()
    {
        $this->extension->defineConfiguration();
        $this->assertSame(array($this->extension, 'setModelFieldValue'), $this->module->getCallback('setModelFieldValue'));
    }

    public function testSetModelFieldValue()
    {
        $this->extension->defineConfiguration();

        $article = new Article();
        $this->extension->setModelFieldValue($article, 'title', 'foo');
        $this->assertSame('foo', $article->getTitle());
        $this->extension->setModelFieldValue($article, 'content', 'bar');
        $this->assertSame('bar', $article->getContent());
    }

    public function testDefineConfigurationGetModelFieldValueCallback()
    {
        $this->extension->defineConfiguration();
        $this->assertSame(array($this->extension, 'getModelFieldValue'), $this->module->getCallback('getModelFieldValue'));
    }

    public function testGetModelFieldValue()
    {
        $this->extension->defineConfiguration();

        $article = new Article();
        $article->setTitle('foo');
        $article->setContent('bar');
        $this->assertSame('foo', $this->extension->getModelFieldValue($article, 'title'));
        $this->assertSame('bar', $this->extension->getModelFieldValue($article, 'content'));
    }

    public function testDefineConfigurationModelFromArrayCallback()
    {
        $this->extension->defineConfiguration();
        $this->assertSame(array($this->extension, 'modelFromArray'), $this->module->getCallback('modelFromArray'));
    }

    public function testModelFromArray()
    {
        $this->extension->defineConfiguration();
        $this->module->getOption('modelFields')->add(array(
            'title'   => array(),
            'content' => array(),
        ));

        $article = new Article();
        $this->assertTrue($this->extension->modelFromArray($article, array(
            'title'   => 'foo',
            'content' => 'bar',
        )));
        $this->assertSame('foo', $article->getTitle());
        $this->assertSame('bar', $article->getContent());
    }

    public function testModelFromArrayExtraFields()
    {
        $this->extension->defineConfiguration();

        $article = new Article();
        $this->assertFalse($this->extension->modelFromArray($article, array(
            'title' => 'foo',
            'ups'   => 'bump',
        )));
    }

    public function testDefineConfigurationModelToArrayCallback()
    {
        $this->extension->defineConfiguration();
        $this->assertSame(array($this->extension, 'modelToArray'), $this->module->getCallback('modelToArray'));
    }

    public function testModelToArray()
    {
        $this->extension->defineConfiguration();
        $this->module->getOption('modelFields')->add(array(
            'title'   => array(),
            'content' => array(),
        ));

        $article = new Article();
        $article->setTitle('foo');
        $article->setContent('bar');
        $this->assertSame(array(
            'title'   => 'foo',
            'content' => 'bar',
        ), $this->extension->modelToArray($article));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testParseConfigurationModelClassNull()
    {
        $this->extension->defineConfiguration();
        $this->extension->parseConfiguration();
    }
}
