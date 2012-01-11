<?php

namespace Pablodip\ModuleBundle\Tests\Extension\Model;

use Pablodip\ModuleBundle\Extension\Model\BaseModelManagerExtension as BaseBaseModelManagerExtension;
use Pablodip\ModuleBundle\Module\Module;

class BaseModelManagerExtension extends BaseBaseModelManagerExtension
{
    public function getName()
    {
        return 'base_model_manager';
    }

    public function createModelQuery($modelClass)
    {
    }

    public function findModelById($modelClass, $id)
    {
    }

    public function createModel($modelClass)
    {
    }

    public function saveModel($model)
    {
    }

    public function deleteModel($model)
    {
    }
}

class BaseModelManagerExtensionModule extends Module
{
    protected function defineConfiguration()
    {
    }
}

class BaseModelManagerExtensionTest extends \PHPUnit_Framework_TestCase
{
    private $module;
    private $extension;

    protected function setUp()
    {
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');

        $this->module = new BaseModelManagerExtensionModule($container);

        $this->extension = new BaseModelManagerExtension();
        $this->extension->setModule($this->module);
    }

    public function testDefineConfigurationCreateModelAfterCallbacksOption()
    {
        $this->extension->defineConfiguration();
        $this->assertTrue($this->module->hasOption('createModelAfterCallbacks'));
    }

    public function testDefineConfigurationSaveModelBeforeCallbacksOption()
    {
        $this->extension->defineConfiguration();
        $this->assertTrue($this->module->hasOption('saveModelBeforeCallbacks'));
    }

    public function testDefineConfigurationDeleteModelBeforeCallbacksOption()
    {
        $this->extension->defineConfiguration();
        $this->assertTrue($this->module->hasOption('deleteModelBeforeCallbacks'));
    }

    public function testDefineConfigurationCreateModelQueryCallback()
    {
        $this->extension->defineConfiguration();
        $this->assertTrue($this->module->hasCallback('createModelQuery'));
    }

    public function testDefineConfigurationFindModelByIdCallback()
    {
        $this->extension->defineConfiguration();
        $this->assertTrue($this->module->hasCallback('findModelById'));
    }

    public function testDefineConfigurationCreateModelCallback()
    {
        $this->extension->defineConfiguration();
        $this->assertTrue($this->module->hasCallback('createModel'));
    }

    public function testDefineConfigurationSaveModelCallback()
    {
        $this->extension->defineConfiguration();
        $this->assertTrue($this->module->hasCallback('saveModel'));
    }

    public function testDefineConfigurationDeleteModelCallback()
    {
        $this->extension->defineConfiguration();
        $this->assertTrue($this->module->hasCallback('deleteModel'));
    }
}
