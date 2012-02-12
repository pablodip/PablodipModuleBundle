<?php

namespace Pablodip\ModuleBundle\Tests\Module;

use Pablodip\ModuleBundle\Module\ModuleView;

class ModuleViewTest extends \PHPUnit_Framework_TestCase
{
    public function testPath()
    {
        $actionName = 'list';
        $parameters = array('foo' => 'bar');
        $url = '/foo/bar/ups';

        $module = $this->getMock('Pablodip\ModuleBundle\Module\ModuleInterface');

        $module
            ->expects($this->once())
            ->method('generateModuleUrl')
            ->with($actionName, $parameters, false)
            ->will($this->returnValue($url))
        ;

        $view = new ModuleView($module);
        $this->assertSame($url, $view->path($actionName, $parameters));
    }

    public function testUrl()
    {
        $actionName = 'list';
        $parameters = array('foo' => 'bar');
        $url = 'http://foo/bar/ups';

        $module = $this->getMock('Pablodip\ModuleBundle\Module\ModuleInterface');

        $module
            ->expects($this->once())
            ->method('generateModuleUrl')
            ->with($actionName, $parameters, true)
            ->will($this->returnValue($url))
        ;

        $view = new ModuleView($module);
        $this->assertSame($url, $view->url($actionName, $parameters));
    }

    public function testGetOption()
    {
        $name = 'foo';
        $value = new \ArrayObject();

        $module = $this->getMock('Pablodip\ModuleBundle\Module\ModuleInterface');
        $module
            ->expects($this->once())
            ->method('getOption')
            ->with($name)
            ->will($this->returnValue($value))
        ;

        $view = new ModuleView($module);
        $this->assertSame($value, $view->getOption($name));
    }

    public function testGetActionOption()
    {
        $action = 'list';
        $name = 'foo';
        $value = new \ArrayObject();

        $action = $this->getMock('Pablodip\ModuleBundle\Action\ActionInterface');
        $action
            ->expects($this->once())
            ->method('getOption')
            ->with($name)
            ->will($this->returnValue($value))
        ;

        $module = $this->getMock('Pablodip\ModuleBundle\Module\ModuleInterface');
        $module
            ->expects($this->once())
            ->method('getAction')
            ->with($action)
            ->will($this->returnValue($action))
        ;

        $view = new ModuleView($module);
        $this->assertSame($value, $view->getActionOption($action, $name));
    }

    public function testRender()
    {
        $actionName = 'list';
        $attributes = array('sort' => 'name');
        $options = array('standalone' => true);
        $retval = new \ArrayObject();

        $httpKernel = $this->getMockBuilder('Symfony\Bundle\FrameworkBundle\HttpKernel')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $container
            ->expects($this->any())
            ->method('get')
            ->with('http_kernel')
            ->will($this->returnValue($httpKernel))
        ;

        $module = $this->getMock('Pablodip\ModuleBundle\Module\ModuleInterface');
        $module
            ->expects($this->any())
            ->method('getContainer')
            ->will($this->returnValue($container))
        ;

        $httpKernel
            ->expects($this->once())
            ->method('render')
            ->with('PablodipModuleBundle:Module:execute', array_merge($options, array(
                'attributes' => array_merge($attributes, array(
                    '_module.module' => get_class($module),
                    '_module.action' => $actionName,
                )),
            )))
            ->will($this->returnValue($retval))
        ;

        $view = new ModuleView($module);
        $this->assertSame($retval, $view->render($actionName, $attributes, $options));
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
