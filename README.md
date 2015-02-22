# Hex

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/travis/marcaube/hex/master.svg?style=flat-square)](https://travis-ci.org/marcaube/hex)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/marcaube/hex.svg?style=flat-square)](https://scrutinizer-ci.com/g/marcaube/hex/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/marcaube/hex.svg?style=flat-square)](https://scrutinizer-ci.com/g/marcaube/hex)
[![Sensio Labs Insight](https://img.shields.io/sensiolabs/i/cf3f42b3-32f1-4c08-9302-65c4827f8ef1.svg?style=flat-square)](https://insight.sensiolabs.com/projects/cf3f42b3-32f1-4c08-9302-65c4827f8ef1)


Hex is a sandbox where I try stuff and learn about DDD, Hexagonal Architecture, CQRS and Event Sourcing.


## Domain

The problem I'm trying to model in Hex is a meeting room, for which employees can make reservations. The business rules
are quite simple:

- [x] A **meeting room** has a limited capacity
- [ ] **Meetings** cannot take place oustide business hours
- [x] Only one meeting can take place at the same time (no overlap)
- [x] Meeting duration cannot exceed 3h
- [x] **Employees** can make a **reservation** up to 7 days in advance
- [x] Employees can consult the meeting room **schedule**


## Documentation

Here are succinct descriptions of concepts related to [OOP](https://en.wikipedia.org/wiki/Object-oriented_programming),
[DDD](https://en.wikipedia.org/wiki/Domain-driven_design), Hexagonal Architecture, CQRS and Event Sourcing.

- [Entity](doc/entity.md)
- [Repository](doc/repository.md)
- [Value Object](doc/value-object.md)


## Testing

```bash
$ ./vendor/bin/phpunit

# You can also give the tests a run for their money
$ ./vendor/bin/humbug
```


## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
