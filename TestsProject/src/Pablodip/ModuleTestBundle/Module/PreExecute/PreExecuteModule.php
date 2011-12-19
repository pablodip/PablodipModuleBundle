<?php

namespace Pablodip\ModuleTestBundle\Module\PreExecute;

use Pablodip\ModuleBundle\Module\Module;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PreExecuteModule extends Module
{
    protected function configure()
    {
        $this
            ->setRouteNamePrefix('pre_execute_module')
            ->setRoutePatternPrefix('/pre-execute-module')
            ->addActions(array(
                new Action\IndexAction(),
            ))
            ->addControllerPreExecute(function ($module) {
                $module->getContainer()->get('request')->attributes->set('foo', 'ups');
            })
            ->addControllerPreExecute(function ($module) {
                if ($module->getContainer()->get('request')->query->get('redirect')) {
                    return new RedirectResponse($module->generateUrl('index'));
                }
            })
        ;
    }
}
