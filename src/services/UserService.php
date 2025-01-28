<?php

namespace JohnHalsey\ReqresUsers\Services;

use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\GuzzleException;
use JohnHalsey\ReqresUsers\Adapters\ReqResAdapter;
use JohnHalsey\ReqresUsers\Exceptions\ApiRequestError;
use JohnHalsey\ReqresUsers\Exceptions\CannotGetUserException;
use JohnHalsey\ReqresUsers\Exceptions\CannotFindUserException;
use JohnHalsey\ReqresUsers\Exceptions\CannotGetUserRequestException;

class UserService
{
	private ReqResAdapter $adapter;

	public function __construct(ReqResAdapter $adapter = null)
	{
		// Automatically create the adapter if not provided
		$this->adapter = $adapter ?? new ReqResAdapter();
	}


	/**
	 * @throws ApiRequestError|CannotFindUserException
	 */
	public function getUserById(int $id): array
	{
		try {
			$response = $this->adapter->get('users/' . $id);

			if ($response->getStatusCode() === 404) {
				throw new CannotFindUserException('Cannot find user with ID: ' . $id, 404);
			}

			return $this->getResponseBody($response);
		} catch (GuzzleException $e) {
			throw new ApiRequestError(
				'Cannot get user: ' . $e->getMessage(),
				0,
				$e
			);
		}
	}

	/**
	 * @throws ApiRequestError
	 */
	public function getUsers(int $page = 1): array
	{
		try {
			$response = $this->adapter->get('users', [
				'query' => ['page' => $page]
			]);

			return $this->getResponseBody($response);
		} catch (GuzzleException $e) {
			throw new ApiRequestError(
				'Cannot get users: ' . $e->getMessage(),
				0,
				$e
			);
		}
	}

	/**
	 * @throws ApiRequestError
	 */
	public function createUser(string $name, string $job): int
	{
		try {
			$response = $this->adapter->post('users', [
				'json' => ['name' => $name, 'job' => $job]
			]);

			$body = $this->getResponseBody($response);

			if ($response->getStatusCode() !== 201) {
				throw new ApiRequestError('Could not create user, an unknwn error occurred');
			}

			// Make sure the ID exists in the response, this could be a good indication
			// that something went wrong
			if (!isset($body['id'])) {
				throw new ApiRequestError('Could not create user: ID not found in response');
			}

			return (int) $body['id'];
		} catch (GuzzleException $e) {
			throw new ApiRequestError(
				'Could not create user: ' . $e->getMessage(),
				0,
				$e
			);
		}
	}

	private function getResponseBody(ResponseInterface $response): array
	{
		return json_decode($response->getBody()->getContents(), true);
	}
}
