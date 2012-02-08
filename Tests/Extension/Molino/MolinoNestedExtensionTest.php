<?php

namespace Pablodip\ModuleBundle\Tests\Extension\Molino;

use Pablodip\ModuleBundle\Extension\Molino\MolinoNestedExtension;

class MolinoNestedExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $extension = new MolinoNestedExtension('Model\Article', 'article_id', 'article', 'foo', 'bar');
        $this->assertSame('Model\Article', $extension->getParentClass());
        $this->assertSame('article_id', $extension->getRouteParameter());
        $this->assertSame('article', $extension->getQueryField());
        $this->assertSame('foo', $extension->getAssociation());
        $this->assertSame('bar', $extension->getRequestAttribute());
    }
}
