<?php

namespace Pablodip\ModuleBundle\Tests\Extension\Serializer;

use Pablodip\ModuleBundle\Extension\Serializer\BaseSerializerExtension as BaseBaseSerializerExtension;
use Pablodip\ModuleBundle\Module\Module;

class BaseSerializerExtension extends BaseBaseSerializerExtension
{
    public function getName()
    {
        return 'base_serializer';
    }

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

    public function testDefineConfigurationSerializerFormatOption()
    {
        $this->extension->defineConfiguration();
        $this->assertTrue($this->module->hasOption('serializerFormat'));
    }

    public function testDefineConfigurationSerializerContentTypeOption()
    {
        $this->extension->defineConfiguration();
        $this->assertTrue($this->module->hasOption('serializerContentType'));
    }

    public function testDefineConfigurationSerializeCallback()
    {
        $this->extension->defineConfiguration();
        $this->assertTrue($this->module->hasCallback('serialize'));
    }

    public function testDefineConfigurationDeserializeCallback()
    {
        $this->extension->defineConfiguration();
        $this->assertTrue($this->module->hasCallback('deserialize'));
    }

    public function testDefineConfigurationCreateSerializedResponseCallback()
    {
        $this->extension->defineConfiguration();
        $this->assertSame(array($this->extension, 'createSerializedResponse'), $this->module->getCallback('createSerializedResponse'));
    }

    public function testDefineConfigurationCreateSerializedNotFoundResponseCallback()
    {
        $this->extension->defineConfiguration();
        $this->assertSame(array($this->extension, 'createSerializedNotFoundResponse'), $this->module->getCallback('createSerializedNotFoundResponse'));
    }

    public function testDefineConfigurationCreateSerializeCallback()
    {
        $this->extension->defineConfiguration();
        $this->assertSame(array($this->extension, 'serialize'), $this->module->getCallback('serialize'));
    }

    public function testDefineConfigurationCreateDeserializeCallback()
    {
        $this->extension->defineConfiguration();
        $this->assertSame(array($this->extension, 'deserialize'), $this->module->getCallback('deserialize'));
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
