<?php

namespace Pablodip\ModuleTestBundle\Module;

use Pablodip\ModuleBundle\Module\Module;
use Pablodip\ModuleBundle\Action\RouteAction;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PreExecuteModule extends Module
{
    protected function defineConfiguration()
    {
        $this
            ->setRouteNamePrefix('pre_execute_module_')
            ->setRoutePatternPrefix('/pre-execute-module')
            ->addControllerPreExecute(function ($module) {
                $module->getContainer()->get('request')->attributes->set('foo', 'ups');
            })
            ->addControllerPreExecute(array($this, 'preExecute'))
        ;

        $this->addAction(new RouteAction('index', '/index', 'GET', function (RouteAction $action) {
            return new Response($action->get('request')->attributes->get('foo'));
        }));
    }

    public function preExecute($module)
    {
        if ($module->getContainer()->get('request')->query->get('redirect')) {
            return new RedirectResponse($module->generateModuleUrl('index'));
        }
    }
}
