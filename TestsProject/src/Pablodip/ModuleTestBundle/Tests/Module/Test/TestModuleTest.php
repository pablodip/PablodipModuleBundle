<?php

namespace Pablodip\ModuleTestBundle\Tests\Module\Test;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TestModuleTest extends WebTestCase
{
    public function testSimpleAction()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/test-module/simple');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testRedirectAction()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/test-module/redirect');
        $this->assertSame(302, $client->getResponse()->getStatusCode());
    }
}
