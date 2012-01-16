<?php

namespace Pablodip\ModuleBundle\Tests\Fixtures;

use Pablodip\ModuleBundle\Module\Module;
use Pablodip\ModuleBundle\Action\Action;

class CRUDModule extends Module
{
    protected function defineConfiguration()
    {
        $this
            ->setRouteNamePrefix('my_crud_')
            ->setRoutePatternPrefix('/foo-bar')
        ;

        $this->addActions(array(
            new Action('list', '/', 'ANY', function () {}),
            new Action('cre', '/create', 'POST', function () {}),
            new Action('update', '/up', 'PUT', function () {}),
            new Action('delete', '/delete', 'DELETE', function () {}),
        ));
    }
}
