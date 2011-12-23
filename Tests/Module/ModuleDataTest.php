<?php

namespace Pablodip\ModuleBundle\Tests\Module;

use Pablodip\ModuleBundle\Module\ModuleData as BaseModuleData;
use Symfony\Component\DependencyInjection\Container;
use Pablodip\ModuleBundle\Field\Field;

class ModuleData extends BaseModuleData
{
    protected function configure()
    {
        $this->setDataClass('ups');
    }
}

class Data
{
    private $name;
    private $createdAt;

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}

class ModuleDataTest extends \PHPUnit_Framework_TestCase
{
    private $module;

    protected function setUp()
    {
        $this->module = new ModuleData(new Container());
    }

    public function testDataClass()
    {
        $this->assertSame($this->module, $this->module->setDataClass('ups'));
        $this->assertSame('ups', $this->module->getDataClass());
    }

    public function testAddField()
    {
        $this->assertSame($this->module, $this->module->addField('foo'));
        $this->assertSame($this->module, $this->module->addField('bar', $options = array('ups' => 'spu')));

        $this->assertEquals(new Field('foo'), $this->module->getField('foo'));
        $this->assertEquals(new Field('bar', $options), $this->module->getField('bar'));
        $this->assertSame(2, count($this->module->getFields()));
    }

    public function testAddFields()
    {
        $this->assertSame($this->module, $this->module->addFields(array(
            'foo',
            'bar' => $options = array('ups' => 'spu'),
        )));

        $this->assertEquals(new Field('foo'), $this->module->getField('foo'));
        $this->assertEquals(new Field('bar', $options), $this->module->getField('bar'));
        $this->assertSame(2, count($this->module->getFields()));
    }

    public function testAddFieldsNameAsValue()
    {
        $this->assertSame($this->module, $this->module->addFields(array(
            'foo' => array(),
            'bar',
            'ups' => $options = array('foobar' => true),
        )));

        $this->assertEquals(new Field('foo'), $this->module->getField('foo'));
        $this->assertEquals(new Field('bar'), $this->module->getField('bar'));
        $this->assertEquals(new Field('ups', $options), $this->module->getField('ups'));
        $this->assertSame(3, count($this->module->getFields()));
    }

    public function testHasField()
    {
        $this->module->addField('foo');

        $this->assertTrue($this->module->hasField('foo'));
        $this->assertFalse($this->module->hasField('bar'));
    }

    public function testGetField()
    {
        $this->module->addFields(array('foo', 'bar'));

        $this->assertEquals(new Field('foo'), $foo = $this->module->getField('foo'));
        $this->assertSame($foo, $this->module->getField('foo'));
        $this->assertEquals(new Field('bar'), $foo = $this->module->getField('bar'));
        $this->assertSame($foo, $this->module->getField('bar'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetFieldNotExist()
    {
        $this->module->addField('foo');
        $this->module->getField('bar');
    }

    public function testGetFields()
    {
        $this->module->addFields(array('foo', 'bar'));

        $this->assertEquals(array(new Field('foo'), new Field('bar')), $fields = $this->module->getFields());
        $this->assertSame($fields, $this->module->getFields());
    }

    public function testFieldGuessers()
    {
        $fieldGuesser1 = $this->getMock('Pablodip\ModuleBundle\Field\Guesser\FieldGuesserInterface');
        $fieldGuesser2 = $this->getMock('Pablodip\ModuleBundle\Field\Guesser\FieldGuesserInterface');

        $this->assertSame($this->module, $this->module->addFieldGuesser($fieldGuesser1));
        $this->assertSame($this->module, $this->module->addFieldGuesser($fieldGuesser2));

        $this->assertSame(array($fieldGuesser1, $fieldGuesser2), $this->module->getFieldGuessers());
    }

    public function testSetDataFieldValue()
    {
        $data = new Data();

        $this->module->setDataFieldValue($data, 'name', 'foo');
        $this->module->setDataFieldValue($data, 'createdAt', 'bar');
        $this->assertSame('foo', $data->getName());
        $this->assertSame('bar', $data->getCreatedAt());
    }

    public function testGetDataFieldValue()
    {
        $data = new Data();
        $data->setName('foo');
        $data->setCreatedAt('bar');

        $this->assertSame('foo', $this->module->getDataFieldValue($data, 'name'));
        $this->assertSame('bar', $this->module->getDataFieldValue($data, 'createdAt'));
    }
}
