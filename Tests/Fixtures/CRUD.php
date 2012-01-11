<?php

namespace Pablodip\ModuleBundle\Tests\Fixtures;

use Pablodip\ModuleBundle\Module\Module as BaseModule;
use Pablodip\ModuleBundle\Action\Action;

class CRUD extends BaseModule
{
    protected function defineConfiguration()
    {
        $this
            ->setRouteNamePrefix('my_crud_')
            ->setRoutePatternPrefix('/foo-bar')
        ;

        $this->addActions(array(
            new Action('list', '/', null, function () {}),
            new Action('cre', '/create', 'POST', function () {}),
            new Action('update', '/up', 'PUT', function () {}),
            new Action('delete', '/delete', 'DELETE', function () {}),
        ));
    }
}
