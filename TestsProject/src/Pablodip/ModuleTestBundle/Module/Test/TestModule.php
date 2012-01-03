<?php

namespace Pablodip\ModuleTestBundle\Module\Test;

use Pablodip\ModuleBundle\Module\Module;
use Pablodip\ModuleBundle\Action\Action;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class TestModule extends Module
{
    protected function configure()
    {
        $module = $this;

        $this
            ->setRouteNamePrefix('test_module')
            ->setRoutePatternPrefix('/test-module')
        ;

        $this->addAction(new Action('simple', '/simple', 'GET', function () {
            return new Response(200);
        }));

        $this->addAction(new Action('redirect', '/redirect', 'GET', function () use ($module) {
            return new RedirectResponse($module->generateUrl('simple'));
        }));
    }
}
