# Command

A command is a simple DTO (data transfer object) that represents a use case or scenario in your system, e.g. 
`RegisterUser` or `PayInvoice`. It is considered an imperative message, i.e. it tells the application to do something.
When a command is handled, it will mutate the state of the application.

A command should be immutable, it is a [value object](value-object.md). They are easy to serialize, so they can be sent
over the network to be handled by another machine/application.


```php
final class RegisterUserCommand implements CommandInterface
{
    /** @var EmailAddress */
    private $emailAddress;

    /** @var PlainTextPassword */
    private $plainTextPassword;

    public function __construct(EmailAddress $emailAddress, PlainTextPassword $plainTextPassword)
    {
        $this->emailAddress = $emailAddress;
        $this->plainTextPassword = $plainTextPassword;
    }

    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    public function getPlainTextPassword()
    {
        return $this->plainTextPassword;
    }
}
```
