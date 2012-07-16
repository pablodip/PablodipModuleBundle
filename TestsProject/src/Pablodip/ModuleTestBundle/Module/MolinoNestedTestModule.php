<?php

namespace Pablodip\ModuleTestBundle\Module;

use Pablodip\ModuleBundle\Module\Module;
use Pablodip\ModuleBundle\Extension\Molino\MandangoMolinoExtension;
use Pablodip\ModuleBundle\Extension\Molino\MolinoNestedExtension;
use Pablodip\ModuleBundle\Action\RouteAction;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\EventDispatcher\EventDispatcher;

class MolinoNestedTestModule extends Module
{
    protected function registerExtensions()
    {
        return array(
            new MandangoMolinoExtension(new EventDispatcher()),
            new MolinoNestedExtension('Model\PablodipModuleTestBundle\Article', 'article_id', 'article', 'article'),
        );
    }

    protected function defineConfiguration()
    {
        $this
            ->setRouteNamePrefix('molino_nested_')
            ->setRoutePatternPrefix('/molino-nested/{article_id}/comments')
        ;

        $this->addAction(new RouteAction('list', '/', 'GET', function (RouteAction $action) {
            $comments = array();
            foreach ($action->getMolino()
                ->createSelectQuery('Model\PablodipModuleTestBundle\Comment')->all()
            as $result) {
                $comments[] = $result->getText();
            }

            return new Response(implode("\n", $comments));
        }));

        $this->addAction(new RouteAction('add', '/', 'POST', function (RouteAction $action) {
            $comment = $action->getMolino()->create('Model\PablodipModuleTestBundle\Comment');

            return new Response($comment->getArticle()->getId());
        }));

        $this->addAction(new RouteAction('propagate', '/parameter-to-propagate', 'GET', function (RouteAction $action) {
            return new Response($action->generateModuleUrl('list'));
        }));
    }
}
