<?php

namespace Pablodip\ModuleBundle\Tests;

use Pablodip\ModuleBundle\OptionBag;

class OptionBagTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $options = array('foo' => 'bar', 'bar' => 'foo');
        $parser = function ($name, $value) { return array($name, $value); };

        $option = new OptionBag($options, $parser);
        $this->assertSame($options, $option->all());
        $this->assertSame($parser, $option->getParser());
    }

    public function testSetGetParser()
    {
        $option = new OptionBag();
        $option->setParser($parser = function () {});
        $this->assertSame($parser, $option->getParser());
    }

    public function testAll()
    {
        $option = new OptionBag($options = array('foo' => 'bar', 'ups' => 'bump'));
        $this->assertSame($options, $option->all());
    }

    public function testkeys()
    {
        $option = new OptionBag(array('foo' => 'bar', 'ups' => 'bump'));
        $this->assertSame(array('foo', 'ups'), $option->keys());
    }

    public function testSet()
    {
        $option = new OptionBag();
        $option->set('foo', 'bar');
        $this->assertSame(array('foo' => 'bar'), $option->all());
        $option->set('ups', 'bump');
        $this->assertSame(array('foo' => 'bar', 'ups' => 'bump'), $option->all());
    }

    public function testSetParser()
    {
        $option = new OptionBag();
        $option->setParser(function ($name, $value) {
            return array('name-'.$name, 'value-'.$value);
        });
        $option->set('foo', 'bar');
        $this->assertSame(array('name-foo' => 'value-bar'), $option->all());
    }

    public function testAdd()
    {
        $option = new OptionBag();
        $option->add(array('foo' => 'bar'));
        $option->add(array('bar' => 'foo'));
        $this->assertSame(array('foo' => 'bar', 'bar' => 'foo'), $option->all());
    }

    public function testReplace()
    {
        $option = new OptionBag();
        $option->replace($options = array('foo' => 'bar', 'bar' => 'foo'));
        $this->assertSame($options, $option->all());
    }

    public function testRemove()
    {
        $option = new OptionBag(array('foo' => 'bar', 'ups' => 'bump'));
        $option->remove('foo');
        $this->assertSame(array('ups' => 'bump'), $option->all());
    }
}
