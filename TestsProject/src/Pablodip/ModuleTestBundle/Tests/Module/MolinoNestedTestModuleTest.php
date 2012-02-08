<?php

namespace Pablodip\ModuleTestBundle\Tests\Module;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Molino\Mandango\Molino;

class MolinoNestedTestModuleTest extends WebTestCase
{
    protected function setUp()
    {
        if (!class_exists('Mongo')) {
            $this->markTestSkipped('Mongo is not available.');
        }

        parent::setUp();
    }

    public function testCheckParentControllerPreExecute()
    {
        $client = static::createClient();
        $molino = $this->createMolinoAndClean($client);

        $article1 = $molino->create($this->getArticleClass());
        $article1->setTitle('foo');
        $molino->save($article1);

        $article2 = $molino->create($this->getArticleClass());
        $article2->setTitle('bar');
        $molino->save($article2);

        $crawler = $client->request('GET', '/molino-nested/'.$article2->getId().'/comments');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertSame($article2, $client->getRequest()->attributes->get('_parent'));
    }

    public function testCheckParentControllerPreExecuteNotFound()
    {
        $client = static::createClient();
        $molino = $this->createMolinoAndClean($client);

        $crawler = $client->request('GET', '/molino-nested/no/comments');
        $this->assertSame(404, $client->getResponse()->getStatusCode());
    }

    public function testCreateQueryEvent()
    {
        $client = static::createClient();
        $molino = $this->createMolinoAndClean($client);

        $article1 = $molino->create($this->getArticleClass());
        $article1->setTitle('foo');
        $molino->save($article1);

        $article2 = $molino->create($this->getArticleClass());
        $article2->setTitle('bar');
        $molino->save($article2);

        $z = 0;
        foreach (array($article1, $article2) as $article) {
            for ($i = 0; $i < 2; $i++) {
                $comment = $molino->create($this->getCommentClass());
                $comment->setText('comment'.++$z);
                $comment->setArticle($article);
                $molino->save($comment);
            }
        }

        $crawler = $client->request('GET', '/molino-nested/'.$article2->getId().'/comments');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertSame("comment3\ncomment4", $client->getResponse()->getContent());
    }

    public function testCreateModelEvent()
    {
        $client = static::createClient();
        $molino = $this->createMolinoAndClean($client);

        $article1 = $molino->create($this->getArticleClass());
        $article1->setTitle('foo');
        $molino->save($article1);

        $article2 = $molino->create($this->getArticleClass());
        $article2->setTitle('bar');
        $molino->save($article2);

        $crawler = $client->request('POST', '/molino-nested/'.$article1->getId().'/comments');
        $this->assertEquals($article1->getId(), $client->getResponse()->getContent());

        $crawler = $client->request('POST', '/molino-nested/'.$article2->getId().'/comments');
        $this->assertEquals($article2->getId(), $client->getResponse()->getContent());
    }

    private function createMolino($client)
    {
        return new Molino($client->getContainer()->get('mandango'));
    }

    public function createMolinoAndClean($client)
    {
        $molino = $this->createMolino($client);

        $molino->createDeleteQuery($this->getArticleClass())->execute();
        $molino->createDeleteQuery($this->getCommentClass())->execute();

        return $molino;
    }

    public function getArticleClass()
    {
        return 'Model\PablodipModuleTestBundle\Article';
    }

    public function getCommentClass()
    {
        return 'Model\PablodipModuleTestBundle\Comment';
    }
}
