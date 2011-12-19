<?php

namespace Pablodip\ModuleTestBundle\Module\Test\Action;

use Pablodip\ModuleBundle\Action\Action;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RedirectAction extends Action
{
    protected function configure()
    {
        $this
            ->setName('redirect')
            ->setRoute('redirect', '/redirect')
        ;
    }

    public function executeController()
    {
        return new RedirectResponse($this->generateModuleUrl('simple'));
    }
}
