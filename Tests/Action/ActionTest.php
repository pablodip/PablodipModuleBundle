<?php

namespace Pablodip\ModuleBundle\Tests\Action;

use Pablodip\ModuleBundle\Action\Action;

class ActionTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $controller = function () {};
        $action = new Action('list', '/list', 'GET', $controller);

        $this->assertSame('list', $action->getName());
        $this->assertSame('list', $action->getRouteName());
        $this->assertSame('/list', $action->getRoutePattern());
        $this->assertSame(array('_method' => 'GET'), $action->getRouteRequirements());
        $this->assertSame($controller, $action->getController());
    }
}
