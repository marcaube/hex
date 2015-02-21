# Entity

An object, encapsulating value and behaviour, whose values can change (mutable) while its identity stays the same.
These objects are compared by identity (`===`). An entity is responsible for keeping its state consistent.

e.g. Customer, Person, Business, Order, Product, Transaction, Bank Account

```php
final class Customer
{
    /** @var int */
    private $id;

    // ...
}
```
