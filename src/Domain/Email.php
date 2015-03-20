<?php

namespace Ob\Hex\Domain;

use Ob\Hex\EventSourcing\Serialization\Serializable;

class Email implements Serializable
{
    // Hardly fool-proof, i.e no IP support. Definitely not RFC 5322 compliant
    const EMAIL_FORMAT = "/^(?!.{255,})(?!.{65,}@)([!#-'*+\/-9=?^-~-]+)(?>\.(?1))*@(?!.*[^.]{64,})(?>[a-z0-9](?>[a-z0-9-]*[a-z0-9])?\.){1,126}[a-z]{2,6}$/iD";

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
    public function asString()
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
        if (!preg_match(self::EMAIL_FORMAT, $email)) {
            throw new \InvalidArgumentException('Invalid email format');
        }
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'email' => $this->email,
        ];
    }

    /**
     * @param array $data
     *
     * @return Email
     */
    public static function unserialize(array $data)
    {
        return new Email($data['email']);
    }
}
