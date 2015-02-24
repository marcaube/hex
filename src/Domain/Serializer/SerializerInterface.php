<?php

namespace Ob\Hex\Domain\Serializer;

interface SerializerInterface
{
    /**
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
