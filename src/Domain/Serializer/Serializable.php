<?php

namespace Ob\Hex\Domain\Serializer;

interface Serializable
{
    /**
     * @return array
     */
    public function serialize();

    /**
     * @return mixed
     */
    public static function unserialize(array $data);
}
