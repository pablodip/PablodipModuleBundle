<?php

namespace Pablodip\ModuleBundle\Tests\Extension\Data;

use Pablodip\ModuleBundle\Extension\Data\DataExtension;
use Pablodip\ModuleBundle\Extension\Data\MandangoDataManagerExtension;
use Pablodip\ModuleBundle\Module\Module;
use Symfony\Component\DependencyInjection\Container;
use Mandango\Mandango;
use Model\Mapping\Metadata;
use Mandango\Cache\ArrayCache;
use Mandango\Connection;

class MandangoDataManagerExtensionModule extends Module
{
    protected function configure()
    {
    }
}

class FunctionalMandangoDataManagerExtensionModule extends Module
{
    protected function registerExtensions()
    {
        $extensions = parent::registerExtensions();
        $extensions[] = new DataExtension();
        $extensions[] = new MandangoDataManagerExtension();

        return $extensions;
    }

    protected function configure()
    {
        $this->setOption('dataClass', 'Model\Article');
    }
}

class MandangoDataManagerExtensionTest extends \PHPUnit_Framework_TestCase
{
    private $mandango;
    private $container;
    private $module;
    private $extension;
    private $functionalModule;
    private $functionalExtension;

    protected function setUp()
    {
        $this->mandango = new Mandango(new Metadata(), new ArrayCache());
        $this->mandango->setConnection('global', new Connection('mongodb://localhost:27017', 'mandango_data_manager_extension'));
        $this->mandango->setDefaultConnectionName('global');

        $this->container = new Container();
        $this->container->set('mandango', $this->mandango);

        $this->module = new MandangoDataManagerExtensionModule($this->container);
        $this->extension = new MandangoDataManagerExtension();
        $this->extension->setModule($this->module);

        $this->functionalModule = new FunctionalMandangoDataManagerExtensionModule($this->container);
        $this->functionalExtension = $this->functionalModule->getExtension('mandango_data_manager');
    }

    public function testDefineConfigurationFilterCriteriaCallbacksOption()
    {
        $this->extension->defineConfiguration();
        $this->assertTrue($this->module->hasOption('filterCriteriaCallbacks'));
    }

    public function testDefineConfigurationFilterCriteriaCallback()
    {
        $this->extension->defineConfiguration();
        $this->assertSame(array($this->extension, 'filterCriteria'), $this->module->getCallback('filterCriteria'));
    }

    public function testFilterCriteria()
    {
        $this->extension->defineConfiguration();

        $this->module->getOption('filterCriteriaCallbacks')->append(function (array $criteria) {
            $criteria['foo'] = 'bar';
            return $criteria;
        });
        $this->module->getOption('filterCriteriaCallbacks')->append(function (array $criteria) {
            $criteria['ups'] = true;
            return $criteria;
        });

        $this->assertSame(array(
            'title' => 'si',
            'foo'   => 'bar',
            'ups'   => true,
        ), $this->module->call('filterCriteria', array('title' => 'si')));
    }

    public function testCreateQuery()
    {
        $query = $this->functionalExtension->createQuery();
        $this->assertInstanceOf('Model\ArticleQuery', $query);
    }

    public function testFindDataById()
    {
        $articles = array();
        for ($i = 0; $i < 10; $i++) {
            $articles[$i] = $this->mandango->create('Model\Article')->setTitle('foo')->save();
        }

        $this->assertSame($articles[1], $this->functionalExtension->findDataById($articles[1]->getId()));
    }

    public function testCreateData()
    {
        $data = $this->functionalExtension->createData();
        $this->assertInstanceOf('Model\Article', $data);
        $this->assertTrue($data->isNew());
    }

    public function testSaveData()
    {
        $article = $this->mandango->create('Model\Article')->setTitle('foo');
        $this->functionalExtension->saveData($article);
        $this->assertFalse($article->isNew());
    }

    public function testDeleteData()
    {
        $article = $this->mandango->create('Model\Article')->setTitle('foo')->save();
        $this->functionalExtension->deleteData($article);
        $this->assertNull($this->mandango->getRepository('Model\Article')->findOneById($article->getId()));
    }
}
