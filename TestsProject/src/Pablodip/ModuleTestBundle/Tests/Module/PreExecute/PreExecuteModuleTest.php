<?php

namespace Pablodip\ModuleTestBundle\Tests\Module\Test;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PreExecuteModuleTest extends WebTestCase
{
    public function testPreExecute1()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/pre-execute-module/index');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertSame('ups', $client->getResponse()->getContent());
    }

    public function testPreExecute2()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/pre-execute-module/index?redirect=1');
        $this->assertSame(302, $client->getResponse()->getStatusCode());
    }
}
