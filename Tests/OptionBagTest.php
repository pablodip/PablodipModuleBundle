<?php

namespace Pablodip\ModuleBundle\Tests;

use Pablodip\ModuleBundle\OptionBag;

class OptionBagTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $bags = array('foo' => 'bar', 'bar' => 'foo');
        $parser = function ($name, $value) { return array($name, $value); };

        $bag = new OptionBag($bags, $parser);
        $this->assertSame($bags, $bag->all());
        $this->assertSame($parser, $bag->getParser());
    }

    public function testSetGetParser()
    {
        $bag = new OptionBag();
        $bag->setParser($parser = function () {});
        $this->assertSame($parser, $bag->getParser());
    }

    public function testAll()
    {
        $bag = new OptionBag($bags = array('foo' => 'bar', 'ups' => 'bump'));
        $this->assertSame($bags, $bag->all());
    }

    public function testkeys()
    {
        $bag = new OptionBag(array('foo' => 'bar', 'ups' => 'bump'));
        $this->assertSame(array('foo', 'ups'), $bag->keys());
    }

    public function testSet()
    {
        $bag = new OptionBag();
        $bag->set('foo', 'bar');
        $this->assertSame(array('foo' => 'bar'), $bag->all());
        $bag->set('ups', 'bump');
        $this->assertSame(array('foo' => 'bar', 'ups' => 'bump'), $bag->all());
    }

    public function testSetParser()
    {
        $bag = new OptionBag();
        $bag->setParser(function ($name, $value) {
            return array('name-'.$name, 'value-'.$value);
        });
        $bag->set('foo', 'bar');
        $this->assertSame(array('name-foo' => 'value-bar'), $bag->all());
    }

    public function testAdd()
    {
        $bag = new OptionBag();
        $bag->add(array('foo' => 'bar'));
        $bag->add(array('bar' => 'foo'));
        $this->assertSame(array('foo' => 'bar', 'bar' => 'foo'), $bag->all());
    }

    public function testReplace()
    {
        $bag = new OptionBag();
        $bag->replace($bags = array('foo' => 'bar', 'bar' => 'foo'));
        $this->assertSame($bags, $bag->all());
    }

    public function testRemove()
    {
        $bag = new OptionBag(array('foo' => 'bar', 'ups' => 'bump'));
        $bag->remove('foo');
        $this->assertSame(array('ups' => 'bump'), $bag->all());
    }

    public function testCount()
    {
        $bag = new OptionBag();
        $this->assertSame(0, $bag->count());
        $bag->add(array('foo' => 'bar', 'bar' => 'foo'));
        $this->assertSame(2, $bag->count());
    }

    public function testCountableInterface()
    {
        $bag = new OptionBag();
        $this->assertInstanceOf('Countable', $bag);
    }

    public function testGetIterator()
    {
        $bag = new OptionBag($options = array('foo' => 'bar', 'ups' => 'bump'));
        $iterator = $bag->getIterator();
        $this->assertInstanceOf('ArrayIterator', $iterator);
        $this->assertSame($options, $iterator->getArrayCopy());
    }

    public function testItertorAggregateInterface()
    {
        $bag = new OptionBag();
        $this->assertInstanceOf('IteratorAggregate', $bag);
    }
}
