<?php

namespace Ob\Hex\Domain\Serializer;

class JsonSerializer implements SerializerInterface
{
    /**
     * @param Serializable $object
     *
     * @return string
     */
    public function serialize(Serializable $object)
    {
        return json_encode([
            'class' => get_class($object),
            'data'  => $object->serialize(),
        ]);
    }

    /**
     * @param string $json
     *
     * @return Serializable
     */
    public function unserialize($json)
    {
        $object = json_decode($json, true);

        return $object['class']::unserialize($object['data']);
    }
}
