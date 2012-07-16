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
    private $eventDispatcher;
    private $molino;

    /**
     * Constructor.
     *
     * @param EventDispatcherInterface|null $eventDispatcher A event dispatcher for the event molino (optional).
     */
    public function __construct(EventDispatcherInterface $eventDispatcher = null)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Returns whether or not is evented.
     *
     * @return Boolean Whether or not is evented.
     */
    public function IsEvented()
    {
        return $this->eventDispatcher !== null;
    }

    /**
     * Returns the event dispatcher.
     *
     * @return EventDispatcherInterface|null The event dispatcher or null.
     */
    public function getEventDispatcher()
    {
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

        $this->molino = $this->isEvented() ? new EventMolino($molino, $this->eventDispatcher) : $molino;
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
