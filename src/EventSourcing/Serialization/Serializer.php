<?php

namespace Ob\Hex\EventSourcing\Serialization;

final class Serializer implements SerializerInterface
{
    /**
     * @param mixed $input
     *
     * @return string
     */
    public function serialize($input)
    {
        return json_encode($this->serializeRecursively($input));
    }

    public function unserialize($data)
    {
        
    }

    /**
     * @param mixed $input
     *
     * @return array|string|int
     */
    private function serializeRecursively($input)
    {
        if (is_object($input)) {
            return [
                'class' => get_class($input),
                'data'  => $this->serializeObject($input),
            ];
        } elseif (is_array($input)) {
            return $this->serializeArray($input);
        }

        return $input;
    }

    /**
     * @param mixed $object
     *
     * @return array|string
     */
    private function serializeObject($object)
    {
        // Dates are a special case in PHP, because they don't have private properties,
        // even though print_r and var_dump would lead you to believe otherwise...
        if ($object instanceof \DateTime || $object instanceof \DateTimeImmutable) {
            return $object->format('Y-m-d\TH:i:s.uP');
        }

        $data       = [];
        $properties = (new \ReflectionClass($object))->getProperties();

        foreach ($properties as $property) {
            $property->setAccessible(true);
            $value = $property->getValue($object);

            $data[$property->getName()] = $this->serializeRecursively($value);
        }

        return $data;
    }

    /**
     * @param array $array
     *
     * @return array
     */
    private function serializeArray(array $array)
    {
        $result = [];

        foreach ($array as $object) {
            $result[] = $this->serializeRecursively($object);
        }

        return $result;
    }
}
