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
     * Options:
     *
     *   * parent_class
     *   * route_parameter
     *   * query_field
     *   * association
     *   * request_attribute (optional, _parent by default)
     *
     * @param array $options An array of options.
     */
    public function __construct(array $options)
    {
        $cleanOptions = $this->cleanOptions($options);

        $this->parentClass = $cleanOptions['parent_class'];
        $this->routeParameter = $cleanOptions['route_parameter'];
        $this->queryField = $cleanOptions['query_field'];
        $this->association = $cleanOptions['association'];
        $this->requestAttribute = $cleanOptions['request_attribute'];
    }

    private function cleanOptions(array $rawOptions)
    {
        $options = array_replace(array(
            'parent_class'      => null,
            'route_parameter'   => null,
            'query_field'       => null,
            'association'       => null,
            'request_attribute' => '_parent',
        ), $rawOptions);

        foreach ($options as $name => $value) {
            if (!$value || !is_string($value)) {
                throw new \InvalidArgumentException(sprintf('The option "%s" is not valid.', $name));
            }
        }

        return $options;
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

        $this->getModule()->addParameterToPropagate($this->getRouteParameter());
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
