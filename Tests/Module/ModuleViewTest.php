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

    public function testGetActionOption()
    {
        $actionName = 'fo';
        $optionName = 'bar';
        $optionValue = 'ups';

        $module = $this->getMock('Pablodip\ModuleBundle\Module\ModuleInterface');

        $module
            ->expects($this->once())
            ->method('getActionOption')
            ->with($actionName, $optionName)
            ->will($this->returnValue($optionValue))
        ;

        $view = new ModuleView($module);
        $this->assertSame($optionValue, $view->getActionOption($actionName, $optionName));
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

    public function testGetDataFieldValue()
    {
        $data = new \DateTime();
        $fieldName = 'foo';
        $returnValue = 'bar';

        $module = $this->getMock('Pablodip\ModuleBundle\Module\ModuleInterface');

        $module
            ->expects($this->once())
            ->method('getDataFieldValue')
            ->with($data, $fieldName)
            ->will($this->returnValue($returnValue))
        ;

        $view = new ModuleView($module);
        $this->assertSame($returnValue, $view->getDataFieldValue($data, $fieldName));
    }
}
