<?php

namespace Pablodip\ModuleBundle\Tests\Extension\Model;

use Pablodip\ModuleBundle\Extension\Model\ModelExtension;
use Pablodip\ModuleBundle\Module\Module;
use Pablodip\ModuleBundle\Field\FieldBag;

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
        $this->assertNull($this->module->getOption('model_class'));
    }

    public function testDefineConfigurationModelFieldsOption()
    {
        $this->extension->defineConfiguration();
        $this->assertInstanceOf('Pablodip\ModuleBundle\Field\FieldBag', $this->module->getOption('model_fields'));
    }

    public function testDefineConfigurationModelFieldGuessersOption()
    {
        $this->extension->defineConfiguration();
        $this->assertInstanceOf('Pablodip\ModuleBundle\OptionBag', $this->module->getOption('model_field_guessers'));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testParseConfigurationModelClassNull()
    {
        $this->extension->defineConfiguration();
        $this->extension->parseConfiguration();
    }

    public function testFilterFields()
    {
        $this->extension->defineConfiguration();
        $this->module->getOption('model_fields')->add(array(
            'foo' => array('foo' => 'bar'),
            'bar' => array('bar' => 'foo'),
            'ups' => array('foo' => 'bar', 'bar' => 'foo'),
        ));

        $filteredFields = $this->extension->filterFields($fields = new FieldBag(array(
            'foo',
            'ups' => array('bar' => 'ups'),
        )));
        $this->assertEquals(new FieldBag(array(
            'foo' => array('foo' => 'bar'),
            'ups' => array('foo' => 'bar', 'bar' => 'ups'),
        )), $filteredFields);
    }

    public function testFilterFieldsEmpty()
    {
        $this->extension->defineConfiguration();
        $this->module->getOption('model_fields')->add(array(
            'foo' => array('foo' => 'bar'),
            'bar' => array('bar' => 'foo'),
            'ups' => array('foo' => 'bar', 'bar' => 'foo'),
        ));

        $filteredFields = $this->extension->filterFields(new FieldBag());
        $this->assertEquals($filteredFields, $this->module->getOption('model_fields'));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testFilterFieldsFieldDoesNotExist()
    {
        $this->extension->defineConfiguration();
        $this->module->getOption('model_fields')->add(array(
            'foo',
        ));
        $this->extension->filterFields(new FieldBag(array('foo', 'bar')));
    }
}
