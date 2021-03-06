# Speicher210 Functional Test Bundle

## Introduction

This Bundle provides base classes and functionality for writing and running functional tests 
with focus on testing REST endpoint.
It provides help in setting up test database and loading fixtures and mocking services in DI (even private services).

## Installation

### Download the Bundle

```bash
$ composer require --dev speicher210/functional-test-bundle
```

### Enable the Bundle

#### Symfony
```php
<?php
// config/bundles.php

return [
    // ...
    Speicher210\FunctionalTestBundle\Speicher210FunctionalTestBundle::class => ['dev' => true, 'test' => true],
    // ...
];
```

## Basic usage

Bootstrap for PHPUnit:
```php
<?php

declare(strict_types=1);

require dirname(__DIR__) . '/config/bootstrap.php';
require_once dirname(__DIR__) . '/vendor/speicher210/functional-test-bundle/src/Test/bootstrap.php';

// $kernel variable will contain the current Kernel instance

```

```php
<?php

declare(strict_types=1);

use Speicher210\FunctionalTestBundle\Test\RestControllerWebTestCase;
use Symfony\Component\HttpFoundation\Request;

final class MyUserEndpointTest extends RestControllerWebTestCase
{
    public function testReturn404IfUserIsNotFound() : void
    {
        $this->assertRestRequestReturns404('/api/user/1', Request::METHOD_GET);
    }

    public function testReturns200AndUserData() : void
    {
        $this->assertRestGetPath('/api/user/1');
    }
}
```

The assertions are done using snapshots for comparison.
By default the framework will look under `Expected` directory (from where the test class is located) 
for a file with the same name as the test and suffixed with `-1.json`.

Example of expected output can be:
`Expected/testReturns200AndUserData-1.json`
```json
{
  "id": "1",
  "first_name": "John",
  "last_name": "Doe",
  "email": "@string@",
  "sign_up_date_time": "@string@.isDateTime()"
}
```
The `-1.json`suffix will be incremented for every REST assertion made under one test.
In the expected any functionality from `coduo/php-matcher` can be used.

It is possible to automatically update content of expected files during test execution to the actual content by adding 
this extension to your phpunit config:

```xml
<extensions>
    <extension class="Speicher210\FunctionalTestBundle\Extension\RestRequestFailTestExpectedOutputFileUpdater" />
</extensions>
```

Fixtures are loaded using Doctrine fixtures. 
By default the framework will look by default under `Fixtures` directory (from where the test class is located)
for a PHP file with the same name as the test. This file must return an array of class names that extend 
`Speicher210\FunctionalTestBundle\Test\Loader\AbstractLoader` class.
Example of fixture file can be:

```php
<?php
// Fixtures/testReturns200AndUserData.php

return [
    \App\Tests\Fixtures\Loader\LoadOneUser::class
];
```

In order to rebuild and reset the database you need to create a bootstrap file for PHPUnit.
In your own bootstrap file you can include the file `Test/bootstrap.php` which will reset the test database.
The database will not be rebuild for every test, but only once when the tests start.
This means that data must be removed before running next test. This can be achieved by running tests in a transaction.
For this add the extension to your phpunit config:

```xml
<extensions>
    <extension class="DAMA\DoctrineTestBundle\PHPUnit\PHPUnitExtension" />
</extensions>
```

## Accessing and mocking services

Mocking services can be achieved by using

```php
<?php

$this->mockContainerService('my_service_id', $myServiceMock);
```

Services can also be accessed by using
```php
<?php

$this->getContainerService('my_service_id');
```
