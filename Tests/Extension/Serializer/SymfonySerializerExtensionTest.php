<?php

namespace Pablodip\ModuleBundle\Tests\Extension\Serializer;

use Pablodip\ModuleBundle\Extension\Serializer\SymfonySerializerExtension;
use Pablodip\ModuleBundle\Module\Module;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class SymfonySerializerExtensionModule extends Module
{
    protected function defineConfiguration()
    {
    }
}

class SymfonySerializerData implements NormalizableInterface, DenormalizableInterface
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

    public function normalize(NormalizerInterface $normalizer, $format = null)
    {
        return array(
            'title'   => $this->getTitle(),
            'content' => $this->getContent(),
        );
    }

    public function denormalize(DenormalizerInterface $denormalizer, $data, $format = null)
    {
        if (isset($data['title'])) {
            $this->setTitle($data['title']);
        }
        if (isset($data['content'])) {
            $this->setContent($data['content']);
        }
    }
}

class SymfonySerializerExtensionTest extends \PHPUnit_Framework_TestCase
{
    private $module;
    private $extension;

    protected function setUp()
    {
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');

        $this->module = new SymfonySerializerExtensionModule($container);

        $this->extension = new SymfonySerializerExtension();
        $this->extension->setModule($this->module);
    }

    public function testGetName()
    {
        $this->assertSame('serializer', $this->extension->getName());
    }

    public function testDefineConfigurationSerializerNormalizersOption()
    {
        $this->extension->defineConfiguration();
        $this->assertTrue($this->module->hasOption('serializerNormalizers'));
    }

    public function testDefineConfigurationSerializerEncodersOption()
    {
        $this->extension->defineConfiguration();
        $this->assertTrue($this->module->hasOption('serializerEncoders'));
    }

    public function testSerialize()
    {
        $this->extension->defineConfiguration();

        $data = array('foo' => 'bar');
        $this->assertSame(json_encode($data), $this->extension->serialize($data));
    }

    public function testDeserialize()
    {
        $this->extension->defineConfiguration();

        $data = array('title' => 'foo', 'content' => 'bar');
        $type = 'Pablodip\ModuleBundle\Tests\Extension\Serializer\SymfonySerializerData';
        $result = $this->extension->deserialize(json_encode($data), $type);
        $this->assertInstanceOf($type, $result);
        $this->assertSame('foo', $result->getTitle());
        $this->assertSame('bar', $result->getContent());
    }
}
