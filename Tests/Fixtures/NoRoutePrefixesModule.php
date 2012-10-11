<?php

namespace Pablodip\ModuleBundle\Tests\Fixtures;

use Pablodip\ModuleBundle\Module\Module;
use Pablodip\ModuleBundle\Action\RouteAction;

class NoRoutePrefixesModule extends Module
{
    protected function defineConfiguration()
    {
        $this->addActions(array(
            new RouteAction('list', '/', 'ANY', function () {}),
            new RouteAction('create', '/create', 'POST', function () {}),
        ));
    }
}
