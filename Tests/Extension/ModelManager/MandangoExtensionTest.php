<?php

namespace Pablodip\ModuleBundle\Tests\Extension\ModelManager;

use Pablodip\ModuleBundle\Tests\Fixtures\MandangoModule;
use Symfony\Component\DependencyInjection\Container;
use Mandango\Mandango;
use Model\Mapping\Metadata;
use Mandango\Cache\ArrayCache;

class MandangoExtensionTest extends \PHPUnit_Framework_TestCase
{
    private $mandango;
    private $container;
    private $module;

    protected function setUp()
    {
        $this->mandango = new Mandango(new Metadata(), new ArrayCache());
        $this->container = new Container();
        $this->container->set('mandango', $this->mandango);
        $this->module = new MandangoModule($this->container);
    }

    public function testFilterCriteriaCallbacksOption()
    {
        $this->assertTrue($this->module->hasOption('filter_criteria_callbacks'));
    }

    public function testCreateQueryClosure()
    {
        $closure = $this->module->getOption('create_query_closure');
        $query = $closure();

        $this->assertInstanceOf('Model\ArticleQuery', $query);
    }

    public function testCreateQueryClosureFilterCriteria()
    {
        $callbacks = $this->module->getOption('filter_criteria_callbacks');
        $callbacks->append(function (array $criteria) {
            $criteria['foo'] = 'bar';
            return $criteria;
        });
        $callbacks->append(function (array $criteria) {
            $criteria['ups'] = true;
            return $criteria;
        });

        $closure = $this->module->getOption('create_query_closure');
        $query = $closure();

        $this->assertSame(array('foo' => 'bar', 'ups' => true), $query->getCriteria());
    }

    public function testCreateDataAfterCallbacks()
    {
        $this->assertTrue($this->module->hasOption('create_data_after_callbacks'));
    }

    public function testCreateDataClosure()
    {
        $closure = $this->module->getOption('create_data_closure');
        $data = $closure();

        $this->assertInstanceOf('Model\Article', $data);
    }

    public function testCreateDataClosureAfterCallbacks()
    {
        $this->module->getOption('create_data_after_callbacks')->append(function ($data) {
            $data->setTitle('foobar');
        });

        $closure = $this->module->getOption('create_data_closure');
        $data = $closure();

        $this->assertSame('foobar', $data->getTitle());
    }

    public function testSaveDataClosure()
    {
        $data = $this->getMockBuilder('Mandango\Document\Document')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $data
            ->expects($this->once())
            ->method('save')
        ;

        $closure = $this->module->getOption('save_data_closure');
        $closure($data);
    }

    public function testDeleteDataClosure()
    {
        $data = $this->getMockBuilder('Mandango\Document\Document')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $data
            ->expects($this->once())
            ->method('delete')
        ;

        $closure = $this->module->getOption('delete_data_closure');
        $closure($data);
    }
}
