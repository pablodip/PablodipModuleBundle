<?php

namespace Pablodip\ModuleBundle\Tests\Module;

use Pablodip\ModuleBundle\Module\ModuleView;

class ModuleViewTest extends \PHPUnit_Framework_TestCase
{
    public function testPath()
    {
        $routeNameSuffix = 'list';
        $parameters = array('foo' => 'bar');
        $url = '/foo/bar/ups';

        $module = $this->getMock('Pablodip\ModuleBundle\Module\ModuleInterface');

        $module
            ->expects($this->once())
            ->method('generateUrl')
            ->with($routeNameSuffix, $parameters, false)
            ->will($this->returnValue($url))
        ;

        $view = new ModuleView($module);
        $this->assertSame($url, $view->path($routeNameSuffix, $parameters));
    }

    public function testUrl()
    {
        $routeNameSuffix = 'list';
        $parameters = array('foo' => 'bar');
        $url = 'http://foo/bar/ups';

        $module = $this->getMock('Pablodip\ModuleBundle\Module\ModuleInterface');

        $module
            ->expects($this->once())
            ->method('generateUrl')
            ->with($routeNameSuffix, $parameters, true)
            ->will($this->returnValue($url))
        ;

        $view = new ModuleView($module);
        $this->assertSame($url, $view->url($routeNameSuffix, $parameters));
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
