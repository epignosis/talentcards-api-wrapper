<?php

namespace Epignosis\TalentCards;

use Epignosis\TalentCards\Contracts\RequestableInterface;
use Epignosis\TalentCards\Exceptions\BadResponseException;
use Epignosis\TalentCards\Exceptions\ConnectionException;
use Epignosis\TalentCards\Exceptions\InvalidEndpointException;
use GuzzleHttp;
use GuzzleHttp\Psr7\Request;

class Client implements RequestableInterface
{
    /**
     * @var GuzzleHttp\Client
     */
    protected $client;

    /**
     * @var \Psr\Http\Message\RequestInterface
     */
    protected $request;

    /**
     * @var \Psr\Http\Message\ResponseInterface
     */
    protected $response;

    /**
     * API Key
     *
     * The custom API key provided by TalentCards
     *
     * @var string
     */
    protected $key;

    /**
     * URL
     *
     * The URL that is set to query the TalentCards API.
     * This is the account URL used to access the project
     * management system. This is passed in on construction.
     *
     * @var string
     */
    protected $url = 'https://www.talentcards.io/api/v1/company';

    public function __construct($client = null)
    {
        if ($client === null) {
            $this->client = new GuzzleHttp\Client($this->config());
        }
    }

    /**
     * @return array
     */
    public function config()
    {
        return [
            'headers' => [
                'Accept'        => 'application/vnd.api+json',
                'Authorization' => 'Bearer '.$this->key,
                'Content-Type'  => 'application/vnd.api+json',
            ],
        ];
    }

    /**
     * Sets the TalentCards API key
     *
     * @param string $key
     * @return \Epignosis\TalentCards\Client
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Sets the Guzzle HTTP client
     *
     * @param $client
     * @return \Epignosis\TalentCards\Client
     */
    public function setClient($client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * HTTP Get verb
     *
     * @param $endpoint
     *
     * @param array $query
     * @return Client
     *
     * @throws \Epignosis\TalentCards\Exceptions\InvalidEndpointException
     */
    public function get($endpoint, array $query = [])
    {
        $this->buildRequest($endpoint, 'GET', [], $query);

        return $this;
    }

    /**
     *
     * Build up request including authentication, body,
     * and string queries if necessary.
     *
     * @param string $endpoint
     * @param string $method
     * @param array $body
     * @param array $query
     *
     * @return \Epignosis\TalentCards\Client
     * @throws \Epignosis\TalentCards\Exceptions\InvalidEndpointException
     */
    public function buildRequest($endpoint, $method, array $body = [], array $query = [])
    {
        $query_string = '';
        $encoded_body = null;

        if (\count($body) > 0) {
            $encoded_body = json_encode($body);
        }

        if (\count($query) > 0) {
            $query_string = $this->buildQuery($query);
        }

        $url = $this->buildUrl($endpoint, $query_string);

        $this->request = new Request($method, $url, $this->config(), $encoded_body);

        return $this;
    }

    /**
     * Builds the Request's query-string
     *
     * @param array $parameters
     * @return string
     */
    public function buildQuery(array $parameters)
    {
        $query_parameters = [];

        foreach ($parameters as $param => $value) {
            if (\is_array($value)) {
                if ($param === 'filter') {
                    foreach ($value as $item => $item_value) {
                        $query_parameters[] = $param.'['.$item.']'.'='.$item_value;
                    }
                } elseif ($param === 'sort') {
                    $query_parameters[] = $param.'='.implode(',', $value);
                } elseif ($param === 'fields') {
                    foreach ($value as $item => $item_value) {
                        $query_parameters[] = $param.'['.$item.']'.'='.implode(',', $item_value);
                    }
                }
            }
        }

        return rawurlencode(implode('&', $query_parameters));
    }

    /**
     *
     * Builds the url to make the request to TalentCards
     * and passes it into Guzzle.
     *
     * @param $endpoint
     *
     * @param string $query
     * @return string
     * @throws \Epignosis\TalentCards\Exceptions\InvalidEndpointException
     */
    public function buildUrl($endpoint, $query = '')
    {
        if (! filter_var($this->getUrl().$endpoint, FILTER_VALIDATE_URL)) {
            throw new InvalidEndpointException('Error invalid endpoint provided');
        }

        if ('' !== $query) {
            return "{$this->getUrl()}{$endpoint}?{$query}";
        }

        return "{$this->getUrl()}{$endpoint}";
    }

    /**
     * Returns the Request's URL
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Sets the Request's URL
     *
     * @param string $url
     * @return Client
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * HTTP Patch verb
     *
     * @param $endpoint
     * @param $data
     *
     * @return Client
     * @throws \Epignosis\TalentCards\Exceptions\InvalidEndpointException
     */
    public function patch($endpoint, $data)
    {
        return $this->buildRequest($endpoint, 'PATCH', $data);
    }

    /**
     * HTTP Post verb
     *
     * @param $endpoint
     * @param $data
     *
     * @return Client
     * @throws \Epignosis\TalentCards\Exceptions\InvalidEndpointException
     */
    public function post($endpoint, $data)
    {
        return $this->buildRequest($endpoint, 'POST', $data);
    }

    /**
     * HTTP Put verb
     *
     * @param $endpoint
     * @param $data
     *
     * @return Client
     * @throws \Epignosis\TalentCards\Exceptions\InvalidEndpointException
     */
    public function put($endpoint, $data)
    {
        return $this->buildRequest($endpoint, 'PUT', $data);
    }

    /**
     * HTTP delete verb
     *
     * @param $endpoint
     *
     * @param $data
     *
     * @return Client
     * @throws \Epignosis\TalentCards\Exceptions\InvalidEndpointException
     */
    public function delete($endpoint, array $data = [])
    {
        return $this->buildRequest($endpoint, 'DELETE', $data);
    }

    /**
     * Response
     *
     * Sends the Request and
     * returns the Response as a JSON payload
     *
     * @param bool $toArray
     * @return mixed|string
     * @throws \RuntimeException
     * @throws \Epignosis\TalentCards\Exceptions\ConnectionException
     * @throws \Epignosis\TalentCards\Exceptions\BadResponseException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function response($toArray = true)
    {
        try {
            $this->response = $this->client->send($this->request, $this->config());

            if ($toArray) {
                return json_decode($this->response->getBody()->getContents(), $toArray);
            }

            return $this->response->getBody()->getContents();
        } catch (GuzzleHttp\Exception\ConnectException $e) {
            throw new ConnectionException('Could not connect to the TalentCards server. Please check your internet connectivity and try again later.');
        } catch (GuzzleHttp\Exception\ServerException $e) {
            throw new BadResponseException('The TalentCards API is experiencing some issues.');
        }
    }

    /**
     * Returns the Request
     *
     * @return mixed
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Returns the Response's status code
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->response->getStatusCode();
    }
}