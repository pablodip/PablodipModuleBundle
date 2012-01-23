<?php

namespace Pablodip\ModuleBundle\Tests\Fixtures;

use Pablodip\ModuleBundle\Module\Module;
use Pablodip\ModuleBundle\Action\RouteAction;

class CRUDModule extends Module
{
    protected function defineConfiguration()
    {
        $this
            ->setRouteNamePrefix('my_crud_')
            ->setRoutePatternPrefix('/foo-bar')
        ;

        $this->addActions(array(
            new RouteAction('list', '/', 'ANY', function () {}),
            new RouteAction('cre', '/create', 'POST', function () {}),
            new RouteAction('update', '/up', 'PUT', function () {}),
            new RouteAction('delete', '/delete', 'DELETE', function () {}),
        ));
    }
}
