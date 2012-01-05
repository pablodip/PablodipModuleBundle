<?php

namespace Pablodip\ModuleTestBundle\Module\PreExecute;

use Pablodip\ModuleBundle\Module\Module;
use Pablodip\ModuleBundle\Action\Action;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PreExecuteModule extends Module
{
    protected function configure()
    {
        $this
            ->setRouteNamePrefix('pre_execute_module')
            ->setRoutePatternPrefix('/pre-execute-module')
            ->addControllerPreExecute(function ($module) {
                $module->getContainer()->get('request')->attributes->set('foo', 'ups');
            })
            ->addControllerPreExecute(function ($module) {
                if ($module->getContainer()->get('request')->query->get('redirect')) {
                    return new RedirectResponse($module->generateModuleUrl('index'));
                }
            })
        ;

        $this->addAction(new Action('index', '/index', 'GET', function (Action $action) {
            return new Response($action->get('request')->attributes->get('foo'));
        }));
    }
}
