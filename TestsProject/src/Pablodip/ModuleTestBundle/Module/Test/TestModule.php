<?php

namespace Pablodip\ModuleTestBundle\Module\Test;

use Pablodip\ModuleBundle\Module\Module;

class TestModule extends Module
{
    protected function configure()
    {
        $this
            ->setRouteNamePrefix('test_module')
            ->setRoutePatternPrefix('/test-module')
            ->addActions(array(
                new Action\SimpleAction(),
                new Action\RedirectAction(),
            ))
        ;
    }
}
