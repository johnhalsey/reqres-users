<?php

namespace JohnHalsey\ReqresUsers\Adapters;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class ReqResAdapter
{
	private $client;

	public function __construct(Client $client = null)
	{
		// Automatically create the client if not provided
		$this->client = $client ?? new Client();
		$this->client->base_uri = 'https://reqres.in/api/';
	}

	/**
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function get(string $url, array $query = []): ResponseInterface
	{
		return $this->client->get($url, $query);
	}

	/**
	 * @throws GuzzleException
	 */
	public function post(string $url, array $data = []): ResponseInterface
	{
		return $this->client->post($url, $data);
	}
}
