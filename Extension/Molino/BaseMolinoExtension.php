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

/**
 * BaseMolinoExtension.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
abstract class BaseMolinoExtension extends BaseExtension
{
    private $molino;

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
        $this->molino = $molino;
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
