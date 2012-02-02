<?php

namespace Pablodip\ModuleBundle\Tests\Action;

use Pablodip\ModuleBundle\Field\FieldBag;
use Pablodip\ModuleBundle\Field\Field;

class FieldBagTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $fields = array(new Field('foo'), new Field('bar'));
        $bag = new FieldBag($fields);
        $this->assertSame($fields, $bag->all());
    }

    public function testKeyIntegerValueString()
    {
        $bag = new FieldBag();
        $bag->set(0, 'foo');
        $this->assertEquals(array('foo' => new Field('foo')), $bag->all());
    }

    public function testValueArray()
    {
        $bag = new FieldBag();
        $bag->set('foo', array('bar' => 'ups'));
        $this->assertEquals(array('foo' => new Field('foo', array('bar' => 'ups'))), $bag->all());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testValueNotField()
    {
        $bag = new FieldBag();
        $bag->set('foo', 'bar');
    }
}
