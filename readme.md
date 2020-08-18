# Doctrine Utilities

A collection of utility classes for the [Doctrine](https://www.doctrine-project.org/projects/orm.html) ORM (Object Relational Mapper).

## Installation

With [Composer](http://getcomposer.org) installed on your computer and initialized for your project, run this command in your project’s root directory:

```bash
composer require lamansky/doctrine
```

Requires PHP 7.4 or above.

You don’t need to install Doctrine2 separately. It will be bundled with this package.

## Classes

The library contains two classes that are useful for almost any Doctrine project: `ProjectEntityManager` and `UTCDateTimeType`.

### ProjectEntityManager

An abstract EntityManagerDecorator that automatically namespaces your Entity class names. To use, first create a class for your project that extends `ProjectEntityManager` and defines the class namespace prefixes:

```php
<?php
namespace MyProject\Doctrine;
use Lamansky\Doctrine\ProjectEntityManager;

class EntityManager extends ProjectEntityManager {
    protected function getEntityNamespacePrefix () : string {
        return 'MyProject\Model\Entity\\';
    }

    protected function getProxyNamespacePrefix () : string {
        return 'MyProject\Model\Proxy\\';
    }
}
```

Then, initialize your entity manager like this:

```php
<?php
use MyProject\Doctrine\EntityManager;

global $em;

$em = new EntityManager(
    DB_HOST,
    DB_USER,
    DB_PASS,
    DB_NAME,
    IS_DEV_ENV, // This should be true or false.
    __DIR__ . '/Model/Entity', // This is the path to your Entity class files directory.
    __DIR__ . '/Model/Proxy' // This is the directory path in which generated Proxy class files should be stored.
);
```

### UTCDateTimeType

Ensures that all datetimes are converted to the UTC timezone when being stored in the database and are converted back to the server’s local timezone when being retrieved. Returns [Carbon](https://carbon.nesbot.com/) objects instead of built-in `DateTime` objects.

Just use this code to override the built-in datetime types:

```php
<?php
use Doctrine\DBAL\Types\Type;

Type::overrideType('datetime', 'Lamansky\Doctrine\UTCDateTimeType');
Type::overrideType('datetimetz', 'Lamansky\Doctrine\UTCDateTimeType');
```
