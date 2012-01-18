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
use Molino\MolinoInterface;
use Molino\EventMolino;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * BaseMolinoExtension.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
abstract class BaseMolinoExtension extends BaseExtension
{
    private $isEvent;
    private $eventDispatcher;
    private $molino;

    /**
     * Constructor.
     *
     * @param Boolean                       $isEvent         Whether the molino is event or not.
     * @param EventDispatcherInterface|null $eventDispatcher A event dispatcher for the event molino (optional).
     *
     * @throws \LogicException If there is event dispatcher and the molino is not event.
     */
    public function __construct($isEvent = false, EventDispatcherInterface $eventDispatcher = null)
    {
        $this->isEvent = (Boolean) $isEvent;
        if ($this->isEvent) {
            $this->eventDispatcher = $eventDispatcher ?: new EventDispatcher();
        } elseif (null !== $eventDispatcher) {
            throw new \LogicException('The event dispatcher is not needed if the molino is not event.');
        }
    }

    /**
     * Returns whether the molino is event or not.
     *
     * @return Boolean Whether the molino is event or not.
     */
    public function isEvent()
    {
        return $this->isEvent;
    }

    /**
     * Returns the event dispatcher.
     *
     * @return EventDispatcherInterface The event dispatcher.
     *
     * @throws \LogicException If the molino is not event.
     */
    public function getEventDispatcher()
    {
        if (!$this->isEvent) {
            throw new \LogicException('The molino is not event.');
        }

        return $this->eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'molino';
    }

    /**
     * {@inheritdoc}
     */
    public function defineConfiguration()
    {
        $molino = $this->registerMolino();
        if (!$molino instanceof MolinoInterface) {
            throw new \RuntimeException('The molino must be an instance of MolinoInterface.');
        }
        $this->molino = $this->isEvent ? new EventMolino($molino, $this->eventDispatcher) : $molino;
    }

    /**
     * Returns the molino.
     *
     * @return MolinoInterface The molino.
     */
    public function getMolino()
    {
        return $this->molino;
    }

    /**
     * Returns the molino to register.
     *
     * @return MolinoInterface A molino.
     */
    abstract protected function registerMolino();
}
