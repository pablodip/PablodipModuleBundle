<?php

namespace Pablodip\ModuleBundle\Tests\Extension\Data;

use Pablodip\ModuleBundle\Extension\Data\BaseDataManagerExtension as BaseBaseDataManagerExtension;
use Pablodip\ModuleBundle\Module\Module;

class BaseDataManagerExtension extends BaseBaseDataManagerExtension
{
    public function getName()
    {
        return 'base_data_manager';
    }

    public function createQuery()
    {
    }

    public function findDataById($id)
    {
    }

    public function createData()
    {
    }

    public function saveData($data)
    {
    }

    public function deleteData($data)
    {
    }
}

class BaseDataManagerExtensionModule extends Module
{
    protected function defineConfiguration()
    {
    }
}

class BaseDataManagerExtensionTest extends \PHPUnit_Framework_TestCase
{
    private $module;
    private $extension;

    protected function setUp()
    {
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');

        $this->module = new BaseDataManagerExtensionModule($container);

        $this->extension = new BaseDataManagerExtension();
        $this->extension->setModule($this->module);
    }

    public function testDefineConfigurationCreateDataAfterCallbacksOption()
    {
        $this->extension->defineConfiguration();
        $this->assertTrue($this->module->hasOption('createDataAfterCallbacks'));
    }

    public function testDefineConfigurationSaveDataBeforeCallbacksOption()
    {
        $this->extension->defineConfiguration();
        $this->assertTrue($this->module->hasOption('saveDataBeforeCallbacks'));
    }

    public function testDefineConfigurationDeleteDataBeforeCallbacksOption()
    {
        $this->extension->defineConfiguration();
        $this->assertTrue($this->module->hasOption('deleteDataBeforeCallbacks'));
    }

    public function testDefineConfigurationCreateQueryCallback()
    {
        $this->extension->defineConfiguration();
        $this->assertTrue($this->module->hasCallback('createQuery'));
    }

    public function testDefineConfigurationFindDataByIdCallback()
    {
        $this->extension->defineConfiguration();
        $this->assertTrue($this->module->hasCallback('findDataById'));
    }

    public function testDefineConfigurationCreateDataCallback()
    {
        $this->extension->defineConfiguration();
        $this->assertTrue($this->module->hasCallback('createData'));
    }

    public function testDefineConfigurationSaveDataCallback()
    {
        $this->extension->defineConfiguration();
        $this->assertTrue($this->module->hasCallback('saveData'));
    }

    public function testDefineConfigurationDeleteDataCallback()
    {
        $this->extension->defineConfiguration();
        $this->assertTrue($this->module->hasCallback('deleteData'));
    }
}
