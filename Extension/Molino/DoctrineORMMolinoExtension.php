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
use Molino\Doctrine\ORM\Molino;

/**
 * DoctrineORMMolinoExtension.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class DoctrineORMMolinoExtension extends BaseMolinoExtension
{
    /**
     * {@inheritdoc}
     */
    protected function registerMolino()
    {
        return new Molino($this->getModule()->getContainer()->get('doctrine')->getEntityManager());
    }
}
