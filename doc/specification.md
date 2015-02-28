# Specification

A specification is an object used to encapsulate business rules which do not belong inside an
[entitiy](entity.md) or [value object](value-object.md). It can be used to enforce rules or fetch entities matching
certain criteria from a collection, e.g.

- Invoice is overdue
- Customer is premium if he has >= 3 orders

It is really powerful for reports and data analysis. Another huge advantage is that you can change the logic in a
single place when the business rule evolves (e.g. >= 5 orders for premium rates).

```php
interface CustomerSpecification
{
    /** @return bool */
    public function isSatisfiedBy(Customer $customer);
}

final class CustomerIsPremium implements CustomerSpecification
{
    /** @return bool */
    public function isSatisfiedBy(Customer $customer)
    {
        return $this->orderRepository->countFor($customer) > 3;
    }
}
```

They can also be combined to form a [composite specification](https://en.wikipedia.org/wiki/Specification_pattern), e.g.
A customer is premium if he has >= 3 orders and at least 1 is paid.
