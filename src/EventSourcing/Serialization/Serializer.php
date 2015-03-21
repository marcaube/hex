<?php

namespace Ob\Hex\EventSourcing\Serialization;

class Serializer implements SerializerInterface
{
    /**
     * @param Serializable $object
     *
     * @return array
     */
    public function serialize(Serializable $object)
    {
        return [
            'class' => get_class($object),
            'data'  => $object->serialize(),
        ];
    }

    /**
     * @param array $data
     *
     * @return Serializable
     */
    public function unserialize($data)
    {
        return $data['class']::unserialize($data['data']);
    }
}
