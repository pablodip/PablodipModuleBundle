<?php

namespace Pablodip\ModuleBundle\Tests\Action;

use Pablodip\ModuleBundle\Action\Action;

class ActionTest extends \PHPUnit_Framework_TestCase
{
    private $controller;
    private $action;

    protected function setUp()
    {
        $this->controller = function () {};
        $this->action = new Action('list', '/list', 'GET', $this->controller);
    }

    public function testConstructor()
    {
        $this->assertSame('list', $this->action->getRouteNameSuffix());
        $this->assertSame('/list', $this->action->getRoutePatternSuffix());
        $this->assertSame(array('_method' => 'GET'), $this->action->getRouteRequirements());
        $this->assertSame($this->controller, $this->action->getController());
    }
}
