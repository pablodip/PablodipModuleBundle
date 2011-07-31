<?php

namespace Pablodip\ModuleBundle\Tests\Module;

use Pablodip\ModuleBundle\Module\ModuleFactory;

class ModuleFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorGetModuleIds()
    {
        $moduleIds = array('foo', 'bar');

        $factory = new ModuleFactory($moduleIds);
        $this->assertSame($moduleIds, $factory->getModuleIds());
    }
}
