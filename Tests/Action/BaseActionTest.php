<?php

namespace Pablodip\ModuleBundle\Tests\Action;

use Pablodip\ModuleBundle\Action\BaseAction as BaseBaseAction;

class BaseAction extends BaseBaseAction
{
    static public $name;
    static public $routeName;
    static public $routePattern;
    static public $controller;

    protected function configure()
    {
        if (null !== self::$name) {
            $this->setName(self::$name);
        }
        if (null !== self::$routeName) {
            $this->setRouteName(self::$routeName);
        }
        if (null !== self::$routePattern) {
            $this->setRoutePattern(self::$routePattern);
        }
        if (null !== self::$controller) {
            $this->setController(self::$controller);
        }
    }
}

class BaseActionTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        BaseAction::$name = 'list';
        BaseAction::$routeName = 'list';
        BaseAction::$routePattern = '/list';
        BaseAction::$controller = function () {};
    }

    public function testConstructorConfigure()
    {
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testConstructorConfigureNoName()
    {
        BaseAction::$name = null;

        new BaseAction();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testConstructorConfigureNoRouteName()
    {
        BaseAction::$routeName = null;

        new BaseAction();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testConstructorConfigureNoRoutePattern()
    {
        BaseAction::$routePattern = null;

        new BaseAction();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testConstructorConfigureNoController()
    {
        BaseAction::$controller = null;

        new BaseAction();
    }
}
