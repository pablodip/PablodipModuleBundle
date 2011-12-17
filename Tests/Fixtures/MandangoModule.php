<?php

namespace Pablodip\ModuleBundle\Tests\Fixtures;

use Pablodip\ModuleBundle\Module\ModuleData;
use Pablodip\ModuleBundle\Extension\ModelManager\MandangoExtension;

class MandangoModule extends ModuleData
{
    protected function configure()
    {
        $this->setDataClass('Model\Article');

        $extension = new MandangoExtension();
        $extension->apply($this);
    }
}
