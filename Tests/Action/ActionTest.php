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
        $this->action = new Action('foo', $this->controller);
    }

    public function testConstructor()
    {
        $this->assertSame('foo', $this->action->getName());
        $this->assertSame($this->controller, $this->action->getController());
    }
}
