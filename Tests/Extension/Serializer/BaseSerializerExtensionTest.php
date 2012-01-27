<?php

namespace Pablodip\ModuleBundle\Tests\Extension\Serializer;

use Pablodip\ModuleBundle\Extension\Serializer\BaseSerializerExtension as BaseBaseSerializerExtension;
use Pablodip\ModuleBundle\Module\Module;

class BaseSerializerExtension extends BaseBaseSerializerExtension
{
    public function serialize($data)
    {
    }

    public function deserialize($data, $type)
    {
    }
}

class BaseSerializerExtensionModule extends Module
{
    protected function defineConfiguration()
    {
    }
}

class BaseSerializerExtensionTest extends \PHPUnit_Framework_TestCase
{
    private $module;
    private $extension;

    protected function setUp()
    {
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');

        $this->module = new BaseSerializerExtensionModule($container);

        $this->extension = new BaseSerializerExtension();
        $this->extension->setModule($this->module);
    }

    public function testGetName()
    {
        $this->assertSame('serializer', $this->extension->getName());
    }

    public function testDefineConfigurationSerializerFormatOption()
    {
        $this->extension->defineConfiguration();
        $this->assertTrue($this->module->hasOption('serializer_format'));
    }

    public function testDefineConfigurationSerializerContentTypeOption()
    {
        $this->extension->defineConfiguration();
        $this->assertTrue($this->module->hasOption('serializer_content_type'));
    }

    public function testCreateSerializedResponse()
    {
        $this->extension->defineConfiguration();

        $response = $this->extension->createSerializedResponse();
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
    }

    public function testCreateSerializedNotFoundExtension()
    {
        $this->extension->defineConfiguration();

        $response = $this->extension->createSerializedNotFoundResponse();
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertSame(404, $response->getStatusCode());
    }
}
