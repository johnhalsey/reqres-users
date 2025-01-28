<?php

namespace JohnHalsey\ReqresUsers\Adapters;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\GuzzleException;

class ReqResAdapter
{
	public function __construct(private Client $client)
	{
		$this->client->base_uri = 'https://reqres.in/api/';
	}

	/**
	 * @throws GuzzleException
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
