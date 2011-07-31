<?php

namespace Pablodip\ModuleBundle\Tests\Action;

use Pablodip\ModuleBundle\Action\ActionView;

class ActionViewTest extends \PHPUnit_Framework_TestCase
{
    public function testGetOption()
    {
        $action = $this->getMock('Pablodip\ModuleBundle\Action\ActionInterface');

        $action
            ->expects($this->once())
            ->method('getOption')
            ->with('foo')
            ->will($this->returnValue('bar'))
        ;

        $view = new ActionView($action);
        $this->assertSame('bar', $view->getOption('foo'));
    }
}
