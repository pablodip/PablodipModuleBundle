<?php

namespace Pablodip\ModuleBundle\Tests\Module;

use Pablodip\ModuleBundle\Module\ModuleDataView;

class ModuleDataViewTest extends \PHPUnit_Framework_TestCase
{
    public function testGetDataFieldValue()
    {
        $data = new \DateTime();
        $fieldName = 'foo';
        $returnValue = 'bar';

        $module = $this->getMock('Pablodip\ModuleBundle\Module\ModuleDataInterface');

        $module
            ->expects($this->once())
            ->method('getDataFieldValue')
            ->with($data, $fieldName)
            ->will($this->returnValue($returnValue))
        ;

        $view = new ModuleDataView($module);
        $this->assertSame($returnValue, $view->getDataFieldValue($data, $fieldName));
    }
}
