<?php

namespace Pablodip\ModuleBundle\Tests\Fixtures;

use Pablodip\ModuleBundle\Module\Module;
use Pablodip\ModuleBundle\Action\Action;

class NoRoutePrefixesModule extends Module
{
    protected function defineConfiguration()
    {
        $this->addActions(array(
            new Action('list', '/', null, function () {}),
            new Action('create', '/create', 'POST', function () {}),
        ));
    }
}
