<?php

namespace Pablodip\ModuleBundle\Tests\Action;

use Pablodip\ModuleBundle\Field\Field;

class FieldTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorName()
    {
        $field = new Field('foo');
        $this->assertSame('foo', $field->getName());
    }

    public function testSetGetOption()
    {
        $field = new Field('man');

        $field->setOption('foo', 'bar');
        $field->setOption('man', 'dango');

        $this->assertTrue($field->hasOption('foo'));
        $this->assertTrue($field->hasOption('man'));
        $this->assertFalse($field->hasOption('no'));

        $this->assertSame('bar', $field->getOption('foo'));
        $this->assertSame('dango', $field->getOption('man'));

        $this->assertSame(array(
            'foo' => 'bar',
            'man' => 'dango',
        ), $field->getOptions());

        $field->setOptions($options = array(
            'bar' => 'foo',
            'mon' => 'dator',
        ));
        $this->assertSame($options, $field->getOptions());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetOptionNotExists()
    {
        $field = new Field('foo');
        $field->getOption('bar');
    }

    public function testGetLabel()
    {
        $field = new Field('foo_bar');
        $this->assertSame('Foo_bar', $field->getLabel());

        $field->setOption('label', 'mandango');
        $this->assertSame('mandango', $field->getLabel());
    }
}
