<?php

namespace Pablodip\ModuleBundle\Tests\Fixtures;

use Pablodip\ModuleBundle\Module\Module;
use Pablodip\ModuleBundle\Action\Action;
use Pablodip\ModuleBundle\Action\RouteAction;

class CRUDModule extends Module
{
    protected function defineConfiguration()
    {
        $this
            ->setRouteNamePrefix('my_crud_')
            ->setRoutePatternPrefix('/foo-bar')
        ;

        $listAction = new RouteAction('list', '/', 'ANY', function () {});
        $listAction->setRouteOption('expose', true);

        $this->addActions(array(
            new Action('internal', function () {}),
            $listAction,
            new RouteAction('cre', '/create', 'POST', function () {}),
            new RouteAction('update', '/up', 'PUT', function () {}),
            new RouteAction('delete', '/delete', 'DELETE', function () {}),
        ));
    }
}
