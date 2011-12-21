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

    public function testCreateQueryCallback()
    {
        $query = call_user_func($this->module->getOption('create_query_callback'));

        $this->assertInstanceOf('Model\ArticleQuery', $query);
    }

    public function testCreateQueryCallbackFilterCriteria()
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

        $query = call_user_func($this->module->getOption('create_query_callback'));

        $this->assertSame(array('foo' => 'bar', 'ups' => true), $query->getCriteria());
    }

    public function testCreateDataAfterCallbacks()
    {
        $this->assertTrue($this->module->hasOption('create_data_after_callbacks'));
    }

    public function testCreateDataCallback()
    {
        $data = call_user_func($this->module->getOption('create_data_callback'));

        $this->assertInstanceOf('Model\Article', $data);
    }

    public function testCreateDataCallbackAfterCallbacks()
    {
        $this->module->getOption('create_data_after_callbacks')->append(function ($data) {
            $data->setTitle('foobar');
        });

        $data = call_user_func($this->module->getOption('create_data_callback'));

        $this->assertSame('foobar', $data->getTitle());
    }

    public function testSaveDataCallback()
    {
        $data = $this->getMockBuilder('Mandango\Document\Document')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $data
            ->expects($this->once())
            ->method('save')
        ;

        call_user_func($this->module->getOption('save_data_callback'), $data);
    }

    public function testDeleteDataCallback()
    {
        $data = $this->getMockBuilder('Mandango\Document\Document')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $data
            ->expects($this->once())
            ->method('delete')
        ;

        call_user_func($this->module->getOption('delete_data_callback'), $data);
    }
}
