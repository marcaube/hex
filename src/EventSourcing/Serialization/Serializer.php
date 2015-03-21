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

    /**
     * @param string $input
     *
     * @return mixed
     */
    public function unserialize($input)
    {
        return $this->unserializeRecursively(json_decode($input, true));
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
        }

        if (is_array($input)) {
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

        foreach ($array as $key => $value) {
            $result[$key] = $this->serializeRecursively($value);
        }

        return $result;
    }

    /**
     * @param string $input
     *
     * @return mixed
     */
    private function unserializeRecursively($input)
    {
        if (is_array($input)) {
            if (isset($input['class']) && isset($input['data'])) {
                return $this->unserializeObject($input['class'], $input['data']);
            }

            return $this->unserializeArray($input);
        }

        return $input;
    }

    /**
     * @param string $class
     * @param mixed  $data
     *
     * @return object
     */
    private function unserializeObject($class, $data)
    {
        // Internal PHP classes cannot be instantiated without invoking their constructor
        if (in_array($class, ['DateTime', 'DateTimeImmutable'])) {
            return new $class($data);
        }

        $reflection = new \ReflectionClass($class);
        $object     = $reflection->newInstanceWithoutConstructor();

        foreach ($reflection->getProperties() as $property) {
            $property->setAccessible(true);
            $name = $property->getName();

            $property->setValue($object, $this->unserializeRecursively($data[$name]));
        }

        return $object;
    }

    /**
     * @param array $array
     *
     * @return array
     */
    private function unserializeArray(array $array)
    {
        if (isset($array['class']) && isset($array['data'])) {
            return $this->unserializeObject($array['class'], $array['data']);
        }

        $result = [];

        foreach ($array as $key => $value) {
            $result[$key] = $this->unserializeRecursively($value);
        }

        return $result;
    }
}
