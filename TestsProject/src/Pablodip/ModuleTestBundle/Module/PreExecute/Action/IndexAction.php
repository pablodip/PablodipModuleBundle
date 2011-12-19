<?php

namespace Pablodip\ModuleTestBundle\Module\PreExecute\Action;

use Pablodip\ModuleBundle\Action\Action;
use Symfony\Component\HttpFoundation\Response;

class IndexAction extends Action
{
    protected function configure()
    {
        $this
            ->setName('index')
            ->setRoute('index', '/index')
        ;
    }

    public function executeController()
    {
        return new Response($this->get('request')->attributes->get('foo'));
    }
}
