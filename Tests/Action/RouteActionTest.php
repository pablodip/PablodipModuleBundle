<?php

namespace Pablodip\ModuleBundle\Tests\Action;

use Pablodip\ModuleBundle\Action\RouteAction;

class RouteActionTest extends \PHPUnit_Framework_TestCase
{
    private $controller;
    private $action;

    protected function setUp()
    {
        $this->controller = function () {};
        $this->action = new RouteAction('list', '/list', 'GET', $this->controller);
    }

    public function testConstructor()
    {
        $this->assertSame('list', $this->action->getRouteName());
        $this->assertSame('/list', $this->action->getRoutePattern());
        $this->assertSame(array('_method' => 'GET'), $this->action->getRouteRequirements());
        $this->assertSame($this->controller, $this->action->getController());
    }
}
