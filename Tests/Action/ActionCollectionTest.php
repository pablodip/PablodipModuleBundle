<?php

namespace Pablodip\ModuleBundle\Tests\Action;

use Pablodip\ModuleBundle\Action\ActionCollection as BaseActionCollection;

class ActionCollection extends BaseActionCollection
{
    protected function configure()
    {
        $this
            ->setName('my.action_name')
            ->addOptions(array(
                'list' => true,
                'edit' => true,
            ))
        ;
    }

    public function getActions()
    {
        return array();
    }
}

class ActionCollectionClean extends BaseActionCollection
{
    protected function configure()
    {
        $this
            ->setName('my.action_name')
        ;
    }

    public function getActions()
    {
        return array();
    }
}

class ActionCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorOptions()
    {
        $collection = new ActionCollection();
        $this->assertSame(array('list' => true, 'edit' => true), $collection->getOptions());

        $collection = new ActionCollection(array('edit' => false));
        $this->assertSame(array('list' => true, 'edit' => false), $collection->getOptions());

        try {
            $collection = new ActionCollection(array('foo' => 'bar'));
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
        }
    }

    public function testNameNamespaceFullName()
    {
        $collection = new ActionCollection();

        // name
        $this->assertSame($collection, $collection->setName('foobar'));
        $this->assertNull($collection->getNamespace());
        $this->assertSame('foobar', $collection->getName());
        $this->assertSame('foobar', $collection->getFullName());

        // name + namespace
        $collection->setName('upsfoo.bar');
        $this->assertSame('upsfoo', $collection->getNamespace());
        $this->assertSame('bar', $collection->getName());
        $this->assertSame('upsfoo.bar', $collection->getFullName());

        // more than one dot
        $collection->setName('some.more.long');
        $this->assertSame('some.more', $collection->getNamespace());
        $this->assertSame('long', $collection->getName());
        $this->assertSame('some.more.long', $collection->getFullName());

        // empty name
        try {
            $collection->setName('');
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
        }
    }


    public function testOptions()
    {
        $collection = new ActionCollectionClean();

        $this->assertSame($collection, $collection->addOption('foo', 'bar'));
        $this->assertSame($collection, $collection->addOption('bar', 'foo'));
        $this->assertSame($collection, $collection->addOptions(array(
            'man' => 'dango',
            'mon' => 'dator',
        )));

        try {
            $collection->addOption('foo', 'bu');
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('LogicException', $e);
        }
        try {
            $collection->addOptions(array(
                'bar' => 'ba',
            ));
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('LogicException', $e);
        }

        $this->assertSame('bar', $collection->getOption('foo'));
        $this->assertSame('foo', $collection->getOption('bar'));
        $this->assertSame('dango', $collection->getOption('man'));
        $this->assertSame('dator', $collection->getOption('mon'));

        try {
            $collection->getOption('no');
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
        }

        $this->assertSame(array(
            'foo' => 'bar',
            'bar' => 'foo',
            'man' => 'dango',
            'mon' => 'dator',
        ), $collection->getOptions());

        $this->assertSame($collection, $collection->setOption('foo', 'ups'));
        $this->assertSame('ups', $collection->getOption('foo'));
        $this->assertSame('foo', $collection->getOption('bar'));
        $this->assertSame('dango', $collection->getOption('man'));
        $this->assertSame('dator', $collection->getOption('mon'));

        $this->assertSame($collection, $collection->setOption('bar', 'min'));
        $this->assertSame('ups', $collection->getOption('foo'));
        $this->assertSame('min', $collection->getOption('bar'));
        $this->assertSame('dango', $collection->getOption('man'));
        $this->assertSame('dator', $collection->getOption('mon'));

        try {
            $collection->setOption('no', 'bar');
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
        }
    }
}
