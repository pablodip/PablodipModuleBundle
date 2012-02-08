<?php

/*
 * This file is part of the PablodipModuleBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pablodip\ModuleBundle\Extension\Molino;

use Pablodip\ModuleBundle\Extension\BaseExtension;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Molino\Event\Events;
use Molino\Event\ModelEvent;
use Molino\Event\QueryEvent;

/**
 * MolinoNestedExtension.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class MolinoNestedExtension extends BaseExtension
{
    private $parentClass;
    private $routeParameter;
    private $queryField;
    private $association;

    /**
     * Constructor.
     *
     * @param string $parentClass      The parent class.
     * @param string $routeParameter   The route parameter.
     * @param string $queryField       The query field.
     * @param string $association      The association.
     * @param string $requestAttribute The request attribute.
     */
    public function __construct($parentClass, $routeParameter, $queryField, $association, $requestAttribute = '_parent')
    {
        $this->parentClass = $parentClass;
        $this->routeParameter = $routeParameter;
        $this->queryField = $queryField;
        $this->association = $association;
        $this->requestAttribute = $requestAttribute;
    }

    /**
     * Returns the parent class.
     *
     * @return string The parent class.
     */
    public function getParentClass()
    {
        return $this->parentClass;
    }

    /**
     * Returns the route parameter.
     *
     * @return string The route parameter.
     */
    public function getRouteParameter()
    {
        return $this->routeParameter;
    }

    /**
     * Returns the query field.
     *
     * @return string The query field.
     */
    public function getQueryField()
    {
        return $this->queryField;
    }

    /**
     * Returns the association.
     *
     * @return string The association.
     */
    public function getAssociation()
    {
        return $this->association;
    }

    /**
     * Returns the request attribute.
     *
     * @return string The request attribute.
     */
    public function getRequestAttribute()
    {
        return $this->requestAttribute;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'molino_nested';
    }

    /**
     * {@inheritdoc}
     */
    public function defineConfiguration()
    {
        $this->addCheckParentControllerPreExecute();
        $this->addCreateQueryEvent();
        $this->addCreateModelEvent();
    }

    private function addCheckParentControllerPreExecute()
    {
        $extension = $this;

        $this->getModule()->addControllerPreExecute(function ($module) use ($extension) {
            $container = $module->getContainer();
            $request = $container->get('request');

            $parent = $module->getExtension('molino')->getMolino()
                ->findOneById($extension->getParentClass(), $request->attributes->get($extension->getRouteParameter()))
            ;
            if (!$parent) {
                throw new NotFoundHttpException();
            }

            $request->attributes->set($extension->getRequestAttribute(), $parent);
        });
    }

    private function addCreateQueryEvent()
    {
        $eventDispatcher = $this->getModule()->getExtension('molino')->getEventDispatcher();
        $extension = $this;

        $eventDispatcher->addListener(Events::CREATE_QUERY, function (QueryEvent $event) use ($extension) {
            $event->getQuery()->filterEqual($extension->getQueryField(),
                $extension->getModule()
                    ->getContainer()
                    ->get('request')->attributes->get($extension->getRequestAttribute())
                    ->getId()
            );
        });
    }

    private function addCreateModelEvent()
    {
        $eventDispatcher = $this->getModule()->getExtension('molino')->getEventDispatcher();
        $extension = $this;

        $eventDispatcher->addListener(Events::CREATE, function (ModelEvent $event) use ($extension) {
            $event->getModel()->{'set'.ucfirst($extension->getAssociation())}(
                $extension->getModule()
                    ->getContainer()
                    ->get('request')
                    ->attributes->get($extension->getRequestAttribute())
            );
        });
    }
}
