<?php

namespace Ob\Hex\EventSourcing\Serialization;

interface SerializerInterface
{
    /**
     * @param mixed $object
     *
     * @return mixed
     */
    public function serialize($object);

    /**
     * @param mixed $data
     *
     * @return mixed
     */
    public function unserialize($data);
}
