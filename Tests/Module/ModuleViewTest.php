<?php

namespace Pablodip\ModuleBundle\Tests\Module;

use Pablodip\ModuleBundle\Module\ModuleView;

class ModuleViewTest extends \PHPUnit_Framework_TestCase
{
    public function testPath()
    {
        $actionRouteName = 'list';
        $parameters = array('foo' => 'bar');
        $url = '/foo/bar/ups';

        $module = $this->getMock('Pablodip\ModuleBundle\Module\ModuleInterface');

        $module
            ->expects($this->once())
            ->method('generateActionUrl')
            ->with($actionRouteName, $parameters, false)
            ->will($this->returnValue($url))
        ;

        $view = new ModuleView($module);
        $this->assertSame($url, $view->path($actionRouteName, $parameters));
    }

    public function testUrl()
    {
        $actionRouteName = 'list';
        $parameters = array('foo' => 'bar');
        $url = 'http://foo/bar/ups';

        $module = $this->getMock('Pablodip\ModuleBundle\Module\ModuleInterface');

        $module
            ->expects($this->once())
            ->method('generateActionUrl')
            ->with($actionRouteName, $parameters, true)
            ->will($this->returnValue($url))
        ;

        $view = new ModuleView($module);
        $this->assertSame($url, $view->url($actionRouteName, $parameters));
    }

    public function testGetParametersToPropagate()
    {
        $parametersToPropagate = array('foo' => 'bar');

        $module = $this->getMock('Pablodip\ModuleBundle\Module\ModuleInterface');

        $module
            ->expects($this->once())
            ->method('getParametersToPropagate')
            ->will($this->returnValue($parametersToPropagate))
        ;

        $view = new ModuleView($module);
        $this->assertSame($parametersToPropagate, $view->getParametersToPropagate());
    }
}
