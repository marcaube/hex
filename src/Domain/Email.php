<?php

namespace Ob\Hex\Domain;

class Email
{
    /**
     * @var string
     */
    private $email;

    /**
     * @param string $email
     */
    public function __construct($email)
    {
        $this->ensureIsString($email);
        $this->ensureHasValidFormat($email);

        $this->email = $email;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    private function ensureIsString($email)
    {
        if (!is_string($email)) {
            throw new \InvalidArgumentException('Email address must be a string');
        }
    }

    /**
     * @param string $email
     */
    private function ensureHasValidFormat($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid email', $email));
        }
    }
}
