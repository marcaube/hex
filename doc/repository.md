# Repository

A repository provides one or more ways to get entities, collections of entities or calculation values (e.g. count)
related to entities. A repository abstracts the persistance mechanism and creates the illusion of in-memory collections.

```php
interface CustomerRepository
{
    public function add(Customer $customer);

    public function remove(Customer $customer);

    /** @return Customer */
    public function find(CustomerId $customerId);

    /** @return Customer[] */
    public function findAll();

    /** @return Customer[] */
    public function findRegisteredInYear(Year $year);
}

final class MySqlCustomerRepository implements CustomerRepository
{
    // ...
}
```
