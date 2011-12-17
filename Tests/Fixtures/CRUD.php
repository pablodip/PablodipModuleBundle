<?php

namespace Pablodip\ModuleBundle\Tests\Fixtures;

use Pablodip\ModuleBundle\Module\Module as ModuleBase;

class CRUD extends ModuleBase
{
    protected function configure()
    {
        $this
            ->setRouteNamePrefix('my_crud')
            ->setRoutePatternPrefix('/foo-bar')
        ;

        $this->addActions(array(
            new ListAction(),
            new CreateAction(),
            new UpdateAction(),
            new DeleteAction(),
        ));
    }
}

use Pablodip\ModuleBundle\Action\Action;

class ListAction extends Action
{
    protected function configure()
    {
        $this
            ->setName('list')
            ->setRoute('list', '/')
        ;
    }

    public function executeController()
    {
    }
}

class CreateAction extends Action
{
    protected function configure()
    {
        $this
            ->setName('create')
            ->setRoute('cre', '/create', array(), array('_method' => 'POST'))
        ;
    }

    public function executeController()
    {
    }
}

class UpdateAction extends Action
{
    protected function configure()
    {
        $this
            ->setName('update')
            ->setRoute('update', '/up', array(), array('_method' => 'PUT'))
        ;
    }

    public function executeController()
    {
    }
}

class DeleteAction extends Action
{
    protected function configure()
    {
        $this
            ->setName('delete')
            ->setRoute('delete', '/delete', array(), array('_method' => 'DELETE'))
        ;
    }

    public function executeController()
    {
    }
}
