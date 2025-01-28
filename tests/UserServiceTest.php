<?php

namespace JohnHalsey\ReqresUsers\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Exception\RequestException;
use JohnHalsey\ReqresUsers\Services\UserService;
use JohnHalsey\ReqresUsers\Adapters\ReqResAdapter;
use JohnHalsey\ReqresUsers\Exceptions\ApiRequestError;
use JohnHalsey\ReqresUsers\Exceptions\CannotFindUserException;
use JohnHalsey\ReqresUsers\Exceptions\CannotGetUserRequestException;

class UserServiceTest extends TestCase
{
	public function test_it_can_get_user_by_id()
	{
		// Mock the ReqResAdapter
		$mockAdapter = $this->createMock(ReqResAdapter::class);
		$mockAdapter->method('get')
			->with('users/2')
			->willReturn(new Response(body: json_encode([
				'data' => [
					'id'         => 2,
					'email'      => 'don.henley@eagle.com',
					'first_name' => 'Don',
					'last_name'  => 'henley',
					'avatar'     => 'https://reqres.in/img/faces/2-image.jpg'
				]
			])));

		// Instantiate the UserService with the mocked adapter
		$userService = new UserService($mockAdapter);

		// Test the getUserById method
		$user = $userService->getUserById(2);

		$this->assertNotNull($user);
		$this->assertEquals(2, $user['data']['id']);
		$this->assertEquals('don.henley@eagle.com', $user['data']['email']);
	}

	public function test_it_will_throw_an_exception_if_user_id_not_found()
	{
		$this->expectException(CannotFindUserException::class);

		// Mock the ReqResAdapter
		$mockAdapter = $this->createMock(ReqResAdapter::class);
		$mockAdapter->method('get')
			->with('users/401223')
			->willReturn(new Response(status: 404));

		// Instantiate the UserService with the mocked adapter
		$userService = new UserService($mockAdapter);

		// Test the getUserById method
		$user = $userService->getUserById(401223);
	}

	public function test_it_will_throw_exception_if_request_error()
	{
		$this->expectExceptionMessage('Cannot get user: Server error');
		$this->expectException(ApiRequestError::class);

		$mockClient = $this->createMock(\GuzzleHttp\Client::class);

		$mockException = new RequestException(
			'Server error',
			new Request('GET', 'https://reqres.in/api/users/2')
		);

		$mockClient->method('get')
			->willThrowException($mockException);

		$adapter = new ReqResAdapter($mockClient);

		$userService = new UserService($adapter);

		$userService->getUserById(2);
	}

	public function test_it_can_get_paginated_users()
	{
		// Mock the ReqResAdapter
		$mockAdapter = $this->createMock(ReqResAdapter::class);
		$mockAdapter->method('get')
			->with('users')
			->willReturn(new Response(body: json_encode([
				'data' => [
					[
						'id'         => 2,
						'email'      => 'don.henley@eagle.com',
						'first_name' => 'Don',
						'last_name'  => 'Henley',
						'avatar'     => 'https://reqres.in/img/faces/2-image.jpg'
					],
					[
						'id'         => 3,
						'email'      => 'glenn.frey@eagle.com',
						'first_name' => 'Glen',
						'last_name'  => 'Frey',
						'avatar'     => 'https://reqres.in/img/faces/3-image.jpg'
					],
					[
						'id'         => 4,
						'email'      => 'timmy.schmit@eagle.com',
						'first_name' => 'Timmy',
						'last_name'  => 'Schmit',
						'avatar'     => 'https://reqres.in/img/faces/3-image.jpg'
					],
				]
			])));

		$userService = new UserService($mockAdapter);

		$users = $userService->getUsers();

		$this->assertNotNull($users);
		$this->assertEquals(2, $users['data'][0]['id']);
		$this->assertEquals(3, $users['data'][1]['id']);
		$this->assertEquals(4, $users['data'][2]['id']);
	}

	public function test_it_will_throw_exception_if_get_users_request_error()
	{
		$this->expectExceptionMessage('Cannot get users: Server error');
		$this->expectException(ApiRequestError::class);

		$mockClient = $this->createMock(Client::class);

		$mockException = new RequestException(
			'Server error',
			new Request('GET', 'https://reqres.in/api/users')
		);

		$mockClient->method('get')
			->willThrowException($mockException);

		$adapter = new ReqResAdapter($mockClient);

		$userService = new UserService($adapter);

		$userService->getUsers();
	}

	public function test_it_can_create_user()
	{
		// Mock the ReqResAdapter
		$mockAdapter = $this->createMock(ReqResAdapter::class);
		$mockAdapter->method('post')
			->with('users', [
				'json' => ['name' => 'Joe Walsh', 'job' => 'Guitarist']
			])
			->willReturn(new Response(status: 201, body: json_encode([
				'name'       => 'Joe Walsh',
				'job'        => 'Guitarist',
				'id'         => 3,
				'created_at' => '2025-01-27T20:06:20.926Z',
			])));

		// Instantiate the UserService with the mocked adapter
		$userService = new UserService($mockAdapter);

		// Test the createUser method
		$newUserId = $userService->createUser('Joe Walsh', 'Guitarist');

		$this->assertEquals(3, $newUserId);
	}

	public function test_it_will_throw_exception_if_cannot_create_user()
	{
		$this->expectExceptionMessage('Could not create user: Server error');
		$this->expectException(ApiRequestError::class);

		$mockClient = $this->createMock(Client::class);

		$mockException = new RequestException(
			'Server error',
			new Request('POST', 'https://reqres.in/api/users', [], json_encode([
				'json' => ['name' => 'Deacon Frey', 'job' => 'Singer']
			]))
		);

		$mockClient->method('post')
			->willThrowException($mockException);

		$adapter = new ReqResAdapter($mockClient);

		$userService = new UserService($adapter);

		$userService->createUser('Deacon Frey', 'Singer');
	}
}
