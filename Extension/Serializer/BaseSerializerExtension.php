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

use Pablodip\ModuleBundle\Extension\BaseExtension;
use Symfony\Component\HttpFoundation\Response;

/**
 * BaseSerializerExtension.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
abstract class BaseSerializerExtension extends BaseExtension
{
    /**
     * {@inheritdoc}
     */
    public function defineConfiguration()
    {
        $this->getModule()->addOptions(array(
            'serializerFormat'      => 'json',
            'serializerContentType' => 'application/json',
        ));

        $this->getModule()->addCallbacks(array(
            'serialize'   => array($this, 'serialize'),
            'deserialize' => array($this, 'deserialize'),
            'createSerializedResponse'         => array($this, 'createSerializedResponse'),
            'createSerializedNotFoundResponse' => array($this, 'createSerializedNotFoundResponse'),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function parseConfiguration()
    {
    }

    abstract public function serialize($data);

    abstract public function deserialize($data, $type);

    public function createSerializedResponse($content = '', $statusCode = 200, array $headers = array())
    {
        if (!is_string($content)) {
            $content = $this->getModule()->call('serialize', $content);
        }
        $headers['Content-Type'] = $this->getModule()->getOption('serializerContentType');

        return new Response($content, $statusCode, $headers);
    }

    public function createSerializedNotFoundResponse()
    {
        return $this->getModule()->call('createSerializedResponse', array('message' => 'Not Found.'), 404);
    }
}
