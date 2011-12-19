<?php

namespace Pablodip\ModuleTestBundle\Module\Test\Action;

use Pablodip\ModuleBundle\Action\Action;
use Symfony\Component\HttpFoundation\Response;

class SimpleAction extends Action
{
    protected function configure()
    {
        $this
            ->setName('simple')
            ->setRoute('simple', '/simple')
        ;
    }

    public function executeController()
    {
        return new Response(200);
    }
}
