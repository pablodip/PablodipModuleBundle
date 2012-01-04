<?php

namespace Pablodip\ModuleBundle\Tests\Action;

use Pablodip\ModuleBundle\Action\BaseAction as BaseBaseAction;

class BaseAction extends BaseBaseAction
{
    static public $name;
    static public $routeNameSuffix;
    static public $routePatternSuffix;
    static public $controller;

    protected function configure()
    {
        if (null !== self::$name) {
            $this->setName(self::$name);
        }
        if (null !== self::$routeNameSuffix) {
            $this->setRouteNameSuffix(self::$routeNameSuffix);
        }
        if (null !== self::$routePatternSuffix) {
            $this->setRoutePatternSuffix(self::$routePatternSuffix);
        }
        if (null !== self::$controller) {
            $this->setController(self::$controller);
        }
    }
}

class BaseActionTest extends \PHPUnit_Framework_TestCase
{
    private $module;

    protected function setUp()
    {
        BaseAction::$name = 'list';
        BaseAction::$routeNameSuffix = 'list';
        BaseAction::$routePatternSuffix = '/list';
        BaseAction::$controller = function () {};

        $this->module = $this->getMock('Pablodip\ModuleBundle\Module\ModuleInterface');
    }

    public function testConstructorConfigure()
    {
        $action = new BaseAction();
        $action->setModule($this->module);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testConfigureNoName()
    {
        BaseAction::$name = null;

        $action = new BaseAction();
        $action->setModule($this->module);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testConstructorConfigureNoRouteNameSuffix()
    {
        BaseAction::$routeNameSuffix = null;

        $action = new BaseAction();
        $action->setModule($this->module);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testConstructorConfigureNoRoutePatternSuffix()
    {
        BaseAction::$routePatternSuffix = null;

        $action = new BaseAction();
        $action->setModule($this->module);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testConstructorConfigureNoController()
    {
        BaseAction::$controller = null;

        $action = new BaseAction();
        $action->setModule($this->module);
    }
}
