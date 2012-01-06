<?php

/*
 * This file is part of the PablodipModuleBundle package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pablodip\ModuleBundle\Extension\Serializer;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\CustomNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

/**
 * SymfonySerializerExtension.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class SymfonySerializerExtension extends BaseSerializerExtension
{
    private $serializer;

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'symfony_serializer';
    }

    /**
     * {@inheritdoc}
     */
    public function defineConfiguration()
    {
        parent::defineConfiguration();

        $this->getModule()->addOptions(array(
            'serializerNormalizers' => new \ArrayObject(array(
                new CustomNormalizer(),
            )),
            'serializerEncoders'    => new \ArrayObject(array(
                new JsonEncoder(),
            )),
        ));
    }

    public function serialize($data)
    {
        return $this->getSerializer()->serialize($data, $this->getModule()->getOption('serializerFormat'));
    }

    public function deserialize($data, $type)
    {
        return $this->getSerializer()->deserialize($data, $type, $this->getModule()->getOption('serializerFormat'));
    }

    private function getSerializer()
    {
        if (null === $this->serializer) {
            $normalizers = (array) $this->getModule()->getOption('serializerNormalizers');
            $encoders = (array) $this->getModule()->getOption('serializerEncoders');
            $this->serializer = new Serializer($normalizers, $encoders);
        }

        return $this->serializer;
    }
}
