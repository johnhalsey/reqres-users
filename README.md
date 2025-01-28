# ReqRes Composer Package

This package is a simple PHP client for the ReqRes API. It provides a simple way to interact with the ReqRes API.

## Installation

- Clone this repository into your project
- Run `composer install` to install the dependencies

## Usage

You can retrive a user by ID like this:

```php
use JohnHalsey\ReqresUsers\Services\UserService;

$userService = new UserService();
try{
	$user = $userService->getUser(1);
} catch (\Exception $e) {
	// An exception is thrown if the user is not found
	// or if there is an error with the request
	echo $e->getMessage();
}

```

To retrive a paginated list of users

```php

use JohnHalsey\ReqresUsers\Services\UserService;

$userService = new UserService();

try{
	$users = $userService->getUsers();
} catch (\Exception $e) {
	// An exception is thrown if there is an error with the request
	echo $e->getMessage();
}
```

To create a user

```php
use JohnHalsey\ReqresUsers\Services\UserService;

$userService = new UserService();
try{
	$userId = $userService->createUser([
		'name' => 'John Doe',
		'job' => 'Developer'
	]);
} catch (\Exception $e) {
	// An exception is thrown if there is an error with the request
	echo $e->getMessage();
}
```

This package has the following dependencies:

- guzzlehttp/guzzle
- phpunit/phpunit
- php8.2 (minimum version)
