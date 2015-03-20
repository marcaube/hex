<?php

namespace Ob\Hex\EventSourcing\Serialization;

interface SerializerInterface
{
    /**
     * @param Serializable $object
     *
     * @return mixed
     */
    public function serialize(Serializable $object);

    /**
     * @param mixed $data
     *
     * @return Serializable
     */
    public function unserialize($data);
}
