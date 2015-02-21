# Value Object

An object, encapsulating value and behavior (e.g. validation), whose values never change
([immutable](https://en.wikipedia.org/w/index.php?title=Immutable_object)). These objects are compared by equality
(`==`).

e.g. : Email, Money, Measurement, DateRange, Address, State/Status

```php
final class Email
{
    /** @var string */
    private $email;

    public function __construct($email)
    {
        if (!/* boring validation stuff */) {
            throw new InvalidArgumentException();
        }

        $this->email = $email;
    }

    public function __toString()
    {
        return $this->email;
    }
}
```
