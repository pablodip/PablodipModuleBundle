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
        $this->assertNull($this->module->getOption('model_class'));
    }

    public function testDefineConfigurationModelFieldsOption()
    {
        $this->extension->defineConfiguration();
        $this->assertTrue($this->module->hasOption('model_fields'));
    }

    public function testDefineConfigurationModelFieldGuessersOption()
    {
        $this->extension->defineConfiguration();
        $this->assertTrue($this->module->hasOption('model_field_guessers'));
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
