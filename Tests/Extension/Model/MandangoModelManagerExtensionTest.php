<?php

namespace Pablodip\ModuleBundle\Tests\Extension\Model;

use Pablodip\ModuleBundle\Extension\Model\ModelExtension;
use Pablodip\ModuleBundle\Extension\Model\MandangoModelManagerExtension;
use Pablodip\ModuleBundle\Module\Module;
use Symfony\Component\DependencyInjection\Container;
use Mandango\Mandango;
use Model\Mapping\Metadata;
use Mandango\Cache\ArrayCache;
use Mandango\Connection;

class MandangoModelManagerExtensionModule extends Module
{
    protected function defineConfiguration()
    {
    }
}

class FunctionalMandangoModelManagerExtensionModule extends Module
{
    protected function registerExtensions()
    {
        $extensions = parent::registerExtensions();
        $extensions[] = new ModelExtension();
        $extensions[] = new MandangoModelManagerExtension();

        return $extensions;
    }

    protected function defineConfiguration()
    {
        $this->setOption('modelClass', 'Model\Article');
    }
}

class MandangoModelManagerExtensionTest extends \PHPUnit_Framework_TestCase
{
    private $mandango;
    private $container;
    private $module;
    private $extension;

    protected function setUp()
    {
        if (!class_exists('Mongo')) {
            $this->markTestSkipped('Mongo is not available.');
        }

        $this->mandango = new Mandango(new Metadata(), new ArrayCache());
        $this->mandango->setConnection('global', new Connection('mongodb://localhost:27017', 'mandango_model_manager_extension'));
        $this->mandango->setDefaultConnectionName('global');

        $this->container = new Container();
        $this->container->set('mandango', $this->mandango);
    }

    private function setUpExtension()
    {
        $this->module = new MandangoModelManagerExtensionModule($this->container);
        $this->extension = new MandangoModelManagerExtension();
        $this->extension->setModule($this->module);
    }

    private function setUpFunctionalExtension()
    {
        $this->module = new FunctionalMandangoModelManagerExtensionModule($this->container);
        $this->extension = $this->module->getExtension('mandango_model_manager');
    }

    public function testDefineConfigurationFilterMandangoCriteriaCallbacksOption()
    {
        $this->setUpExtension();

        $this->extension->defineConfiguration();
        $this->assertTrue($this->module->hasOption('filterMandangoCriteriaCallbacks'));
    }

    public function testDefineConfigurationFilterMandangoCriteriaCallback()
    {
        $this->setUpExtension();

        $this->extension->defineConfiguration();
        $this->assertSame(array($this->extension, 'filterMandangoCriteria'), $this->module->getCallback('filterMandangoCriteria'));
    }

    public function testFilterMandangoCriteria()
    {
        $this->setUpExtension();

        $this->extension->defineConfiguration();

        $this->module->getOption('filterMandangoCriteriaCallbacks')->add(array(
            function ($modelClass, array $criteria) {
                $criteria['foo'] = 'bar';
                return $criteria;
            },
            function ($modelClass, array $criteria) {
                $criteria['ups'] = true;
                return $criteria;
            },
        ));

        $this->assertSame(array(
            'title' => 'si',
            'foo'   => 'bar',
            'ups'   => true,
        ), $this->module->call('filterMandangoCriteria', 'Model\Article', array('title' => 'si')));
    }

    public function testCreateModelQuery()
    {
        $this->setUpFunctionalExtension();

        $query = $this->extension->createModelQuery('Model\Article');
        $this->assertInstanceOf('Model\ArticleQuery', $query);
    }

    public function testFindModelById()
    {
        $this->setUpFunctionalExtension();

        $articles = array();
        for ($i = 0; $i < 10; $i++) {
            $articles[$i] = $this->mandango->create('Model\Article')->setTitle('foo')->save();
        }

        $this->assertSame($articles[1], $this->extension->findModelById('Model\Article', $articles[1]->getId()));
    }

    public function testCreateModel()
    {
        $this->setUpFunctionalExtension();

        $model = $this->extension->createModel('Model\Article');
        $this->assertInstanceOf('Model\Article', $model);
        $this->assertTrue($model->isNew());
    }

    public function testSaveModel()
    {
        $this->setUpFunctionalExtension();

        $article = $this->mandango->create('Model\Article')->setTitle('foo');
        $this->extension->saveModel($article);
        $this->assertFalse($article->isNew());
    }

    public function testDeleteModel()
    {
        $this->setUpFunctionalExtension();

        $article = $this->mandango->create('Model\Article')->setTitle('foo')->save();
        $this->extension->deleteModel($article);
        $this->assertNull($this->mandango->getRepository('Model\Article')->findOneById($article->getId()));
    }
}
