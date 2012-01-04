<?php

namespace Pablodip\ModuleBundle\Tests\Action;

use Pablodip\ModuleBundle\Action\AbstractAction as BaseAbstractAction;
use Symfony\Component\HttpFoundation\Response;

class AbstractAction extends BaseAbstractAction
{
}

class AbstractActionTest extends \PHPUnit_Framework_TestCase
{
    private $action;

    protected function setUp()
    {
        $this->action = new AbstractAction();
    }

    public function testSetGetModule()
    {
        $module = $this->getMock('Pablodip\ModuleBundle\Module\ModuleInterface');
        $this->action->setModule($module);
        $this->assertSame($module, $this->action->getModule());
    }

    public function testGetContainer()
    {
        $container = new \DateTime();
        $module = $this->getMock('Pablodip\ModuleBundle\Module\ModuleInterface');
        $module
            ->expects($this->once())
            ->method('getContainer')
            ->will($this->returnValue($container))
        ;

        $this->action->setModule($module);
        $this->assertSame($container, $this->action->getContainer());
    }

    public function testSetGetName()
    {
        $this->assertSame($this->action, $this->action->setName('foo'));
        $this->assertSame('foo', $this->action->getName());
    }

    public function testSetGetRouteName()
    {
        $this->assertSame($this->action, $this->action->setRouteName('list'));
        $this->assertSame('list', $this->action->getRouteName());
    }

    public function testSetGetRoutePattern()
    {
        // empty string to /
        $this->assertSame($this->action, $this->action->setRoutePattern(''));
        $this->assertSame('', $this->action->getRoutePattern());

        // normal
        $this->assertSame($this->action, $this->action->setRoutePattern('/list'));
        $this->assertSame('/list', $this->action->getRoutePattern());
    }

    public function testSetGetRouteDefaults()
    {
        $routeDefaults = array('_controller' => 'foobar');
        $this->assertSame($this->action, $this->action->setRouteDefaults($routeDefaults));
        $this->assertSame($routeDefaults, $this->action->getRouteDefaults());
    }

    public function testSetRouteDefault()
    {
        $this->assertSame($this->action, $this->action->setRouteDefault('foo', 'bar'));
        $this->assertSame(array('foo' => 'bar'), $this->action->getRouteDefaults());
        $this->assertSame($this->action, $this->action->setRouteDefault('ups', 'sf'));
        $this->assertSame(array('foo' => 'bar', 'ups' => 'sf'), $this->action->getRouteDefaults());
    }

    public function testSetGetRouteRequirements()
    {
        $routeRequirements = array('_method' => 'GET');
        $this->assertSame($this->action, $this->action->setRouteRequirements($routeRequirements));
        $this->assertSame($routeRequirements, $this->action->getRouteRequirements());
    }

    public function testSetRouteRequirement()
    {
        $this->assertSame($this->action, $this->action->setRouteRequirement('foo', 'bar'));
        $this->assertSame(array('foo' => 'bar'), $this->action->getRouteRequirements());
        $this->assertSame($this->action, $this->action->setRouteRequirement('_method', 'GET'));
        $this->assertSame(array('foo' => 'bar', '_method' => 'GET'), $this->action->getRouteRequirements());
    }

    public function testSetRouteBasic()
    {
        $this->assertSame($this->action, $this->action->setRoute('list', '/list'));
        $this->assertSame('list', $this->action->getRouteName());
        $this->assertSame('/list', $this->action->getRoutePattern());
    }

    public function testSetRouteMethod()
    {
        $this->assertSame($this->action, $this->action->setRoute('list', '/list', 'GET'));
        $this->assertSame(array('_method' => 'GET'), $this->action->getRouteRequirements());
    }

    public function testAddOption()
    {
        $this->assertSame($this->action, $this->action->addOption('foo', 'bar'));
        $this->assertSame(array('foo' => 'bar'), $this->action->getOptions());
        $this->assertSame($this->action, $this->action->addOption('page', 1));
        $this->assertSame(array('foo' => 'bar', 'page' => 1), $this->action->getOptions());
    }

    /**
     * @expectedException \LogicException
     */
    public function testAddOptionNameAlreadyExists()
    {
        $this->action->addOption('foo', 'bar');
        $this->action->addOption('foo', 'bar');
    }

    public function testAddOptions()
    {
        $this->assertSame($this->action, $this->action->addOptions(array('foo' => 'bar', 'bar' => 'foo')));
        $this->assertSame(array('foo' => 'bar', 'bar' => 'foo'), $this->action->getOptions());
        $this->assertSame($this->action, $this->action->addOptions(array('page' => 1)));
        $this->assertSame(array('foo' => 'bar', 'bar' => 'foo', 'page' => 1), $this->action->getOptions());
    }

    /**
     * @expectedException \LogicException
     */
    public function testAddOptionsNameAlreadyExists()
    {
        $this->action->addOption('foo', 'bar');
        $this->action->addOptions(array('foo' => 'bar'));
    }

    public function testSetOption()
    {
        $this->action->addOptions(array(
            'foo' => 'bar',
            'ups' => 2,
        ));
        $this->assertSame($this->action, $this->action->setOption('foo', 'page'));
        $this->assertSame(array('foo' => 'page', 'ups' => 2), $this->action->getOptions());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetOptionOptionNotExists()
    {
        $this->action->setOption('foo', 'bar');
    }

    public function testHasOption()
    {
        $this->action->addOption('foo', 'bar');
        $this->assertTrue($this->action->hasOption('foo'));
        $this->assertFalse($this->action->hasOption('bar'));
    }

    public function testGetOption()
    {
        $this->action->addOption('foo', 'bar');
        $this->action->addOption('page', 1);

        $this->assertSame('bar', $this->action->getOption('foo'));
        $this->assertSame(1, $this->action->getOption('page'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetOptionOptionNotExists()
    {
        $this->action->getOption('foo');
    }

    public function testGetOptions()
    {
        $this->action->addOption('foo', 'bar');
        $this->action->addOption('page', 1);
        $this->assertSame(array('foo' => 'bar', 'page' => 1), $this->action->getOptions());
    }

    public function testSetGetController()
    {
        $controller = function () {};
        $this->assertSame($this->action, $this->action->setController($controller));
        $this->assertSame($controller, $this->action->getController());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetControllerNotCallback()
    {
        $this->action->setController('string');
    }

    public function testExecuteController()
    {
        $retval = new \DateTime();
        $this->action->setController(function () use ($retval) {
            return $retval;
        });

        $this->assertSame($retval, $this->action->executeController());
    }

    public function testGenerateUrl()
    {
        $route = 'route_name';
        $parameters = array('foo' => 'bar');
        $absolute = true;
        $retval = new \DateTime();

        $router = $this->getMock('Symfony\Component\Routing\RouterInterface');
        $router
            ->expects($this->once())
            ->method('generate')
            ->with($route, $parameters, $absolute)
            ->will($this->returnValue($retval))
        ;

        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $container
            ->expects($this->any())
            ->method('get')
            ->with('router')
            ->will($this->returnValue($router))
        ;

        $module = $this->getMock('Pablodip\ModuleBundle\Module\ModuleInterface');
        $module
            ->expects($this->any())
            ->method('getContainer')
            ->will($this->returnValue($container))
        ;

        $this->action->setModule($module);
        $this->assertSame($retval, $this->action->generateUrl($route, $parameters, $absolute));
    }

    public function testForward()
    {
        $controller = 'ups';
        $path = array('foo' => 'bar');
        $query = array('bar' => 'fo');
        $retval = new \DateTime();

        $httpKernel = $this->getMockBuilder('Symfony\Bundle\FrameworkBundle\HttpKernel')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $httpKernel
            ->expects($this->once())
            ->method('forward')
            ->with($controller, $path, $query)
            ->will($this->returnValue($retval))
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

        $this->action->setModule($module);

        $this->assertSame($retval, $this->action->forward($controller, $path, $query));
    }

    public function testRedirect()
    {
        $module = $this->getMock('Pablodip\ModuleBundle\Module\ModuleInterface');
        $module
            ->expects($this->any())
            ->method('getContainer')
            ->will($this->returnValue(''))
        ;

        $this->action->setModule($module);

        $response = $this->action->redirect($url = 'http://symfony.com');
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);
        $this->assertSame($url, $response->headers->get('Location'));
        $this->assertTrue($response->isRedirect());
    }

    public function testRenderView()
    {
        $template = 'ups';
        $parameters = array('foo' => 'bar');
        $moduleView = new \DateTime();
        $retval = new \DateTime();

        $templating = $this->getMock('Symfony\Component\Templating\EngineInterface');

        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $container
            ->expects($this->any())
            ->method('get')
            ->with('templating')
            ->will($this->returnValue($templating))
        ;

        $module = $this->getMock('Pablodip\ModuleBundle\Module\ModuleInterface');
        $module
            ->expects($this->any())
            ->method('getContainer')
            ->will($this->returnValue($container))
        ;
        $module
            ->expects($this->any())
            ->method('createView')
            ->will($this->returnValue($moduleView))
        ;

        $this->action->setModule($module);

        $templating
            ->expects($this->once())
            ->method('render')
            ->with($template, array_merge($parameters, array('_module' => $moduleView)))
            ->will($this->returnValue($retval))
        ;

        $this->assertSame($retval, $this->action->renderView($template, $parameters));
    }

    public function testRender()
    {
        $template = 'ups';
        $parameters = array('foo' => 'bar');
        $response = new Response();
        $moduleView = new \DateTime();
        $retval = new \DateTime();

        $templating = $this->getMock('Symfony\Bundle\FrameworkBundle\Templating\EngineInterface');

        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $container
            ->expects($this->any())
            ->method('get')
            ->with('templating')
            ->will($this->returnValue($templating))
        ;

        $module = $this->getMock('Pablodip\ModuleBundle\Module\ModuleInterface');
        $module
            ->expects($this->any())
            ->method('getContainer')
            ->will($this->returnValue($container))
        ;
        $module
            ->expects($this->any())
            ->method('createView')
            ->will($this->returnValue($moduleView))
        ;

        $this->action->setModule($module);

        $templating
            ->expects($this->once())
            ->method('renderResponse')
            ->with($template, array_merge($parameters, array('_module' => $moduleView)), $response)
            ->will($this->returnValue($retval))
        ;

        $this->assertSame($retval, $this->action->render($template, $parameters, $response));
    }

    public function testCreateNotFoundException()
    {
        $exception = $this->action->createNotFoundException();
        $this->assertInstanceOf('Symfony\Component\HttpKernel\Exception\NotFoundHttpException', $exception);
    }

    public function testCreateForm()
    {
        $type = 'typeups';
        $data = 'data';
        $options = array('foo' => 'bar');
        $retval = new \DateTime();

        $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory
            ->expects($this->once())
            ->method('create')
            ->with($type, $data, $options)
            ->will($this->returnValue($retval))
        ;

        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $container
            ->expects($this->any())
            ->method('get')
            ->with('form.factory')
            ->will($this->returnValue($formFactory))
        ;

        $module = $this->getMock('Pablodip\ModuleBundle\Module\ModuleInterface');
        $module
            ->expects($this->any())
            ->method('getContainer')
            ->will($this->returnValue($container))
        ;

        $this->action->setModule($module);

        $this->assertSame($retval, $this->action->createForm($type, $data, $options));
    }

    public function testCreateFormBuilder()
    {
        $data = 'data';
        $options = array('foo' => 'bar');
        $retval = new \DateTime();

        $formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');
        $formFactory
            ->expects($this->once())
            ->method('createBuilder')
            ->with('form', $data, $options)
            ->will($this->returnValue($retval))
        ;

        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $container
            ->expects($this->any())
            ->method('get')
            ->with('form.factory')
            ->will($this->returnValue($formFactory))
        ;

        $module = $this->getMock('Pablodip\ModuleBundle\Module\ModuleInterface');
        $module
            ->expects($this->any())
            ->method('getContainer')
            ->will($this->returnValue($container))
        ;

        $this->action->setModule($module);

        $this->assertSame($retval, $this->action->createFormBuilder($data, $options));
    }

    public function testHas()
    {
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $container
            ->expects($this->once())
            ->method('has')
            ->with('foo')
            ->will($this->returnValue(true))
        ;

        $module = $this->getMock('Pablodip\ModuleBundle\Module\ModuleInterface');
        $module
            ->expects($this->any())
            ->method('getContainer')
            ->will($this->returnValue($container))
        ;

        $this->action->setModule($module);

        $this->assertTrue($this->action->has('foo'));
    }

    public function testGet()
    {
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $service = new \DateTime();
        $container
            ->expects($this->once())
            ->method('get')
            ->with('bar')
            ->will($this->returnValue($service))
        ;

        $module = $this->getMock('Pablodip\ModuleBundle\Module\ModuleInterface');
        $module
            ->expects($this->any())
            ->method('getContainer')
            ->will($this->returnValue($container))
        ;

        $this->action->setModule($module);

        $this->assertSame($service, $this->action->get('bar'));
    }
}