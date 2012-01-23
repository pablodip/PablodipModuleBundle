<?php

namespace Pablodip\ModuleTestBundle\Module;

use Pablodip\ModuleBundle\Module\Module;
use Pablodip\ModuleBundle\Action\RouteAction;
use Symfony\Component\HttpFoundation\Response;

class TestModule extends Module
{
    protected function defineConfiguration()
    {
        $this
            ->setRouteNamePrefix('test_module_')
            ->setRoutePatternPrefix('/test-module')
        ;

        $this->addAction(new RouteAction('simple', '/simple', 'GET', function () {
            return new Response(200);
        }));

        $this->addAction(new RouteAction('redirect', '/redirect', 'GET', function (RouteAction $action) {
            return $action->redirect($action->generateModuleUrl('simple'));
        }));

        $this->addAction(new RouteAction('guess_template', '/guess-template', 'GET', function (RouteAction $action) {
            return $action->render(array());
        }));
    }
}
