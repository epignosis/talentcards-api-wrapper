<?php

use Epignosis\TalentCards\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    /**
     * @var Epignosis\TalentCards\Client $apiClient
     */
    protected $apiClient;

    private $mockHandler;

    /** @test */
    public function it_validates_urls()
    {
        $this->mockResponse(200, '{"foo":"bar"}');
        $this->apiClient->setUrl('INVALID');

        $this->setExpectedException('Epignosis\TalentCards\Exceptions\InvalidEndpointException');
        $this->apiClient->get('/')->response();
    }

    private function mockResponse($status, $body = null)
    {
        $this->mockHandler = MockHandler::createWithMiddleware([new Response($status, [], $body)]);

        $client = new Client;

        $config = ['handler' => $this->mockHandler];

        $client->setKey('VALID')->setClient(new GuzzleHttp\Client($config));

        $this->apiClient = $client;
    }

    /** @test */
    public function it_can_make_get_requests()
    {
        $this->mockResponse(200, '{"foo":"bar"}');

        $this->apiClient->get('/', ['foo' => 'bar'])->response();

        $request_method = $this->apiClient->getRequest()->getMethod();

        $this->assertEquals('GET', $request_method);
    }

    /** @test */
    public function it_can_return_an_array_result()
    {
        $this->mockResponse(200, '{"foo":"bar"}');
        $response = $this->apiClient->get('/')->response(true);
        $this->assertInternalType('array', $response, 'Response was not an array!');
    }

    /** @test */
    public function it_can_return_a_json_result()
    {
        $this->mockResponse(200, '{"foo":"bar"}');
        $response = $this->apiClient->get('/')->response(false);
        $this->assertJson($response, 'Response was not JSON!');
    }

    /** @test */
    public function it_can_make_post_requests()
    {
        $this->mockResponse(200, '{"foo":"bar"}');

        $this->apiClient->post('/', [])->response();

        $request_method = $this->apiClient->getRequest()->getMethod();

        $this->assertEquals('POST', $request_method);
    }

    /** @test */
    public function it_can_make_put_requests()
    {
        $this->mockResponse(200, '{"foo":"bar"}');

        $this->apiClient->put('/', ['foo' => 'bar'])->response();

        $request_method = $this->apiClient->getRequest()->getMethod();

        $this->assertEquals('PUT', $request_method);
    }

    /** @test */
    public function it_can_make_patch_requests()
    {
        $this->mockResponse(200, '{"foo":"bar"}');

        $this->apiClient->patch('/', [])->response();

        $request_method = $this->apiClient->getRequest()->getMethod();

        $this->assertEquals('PATCH', $request_method);
    }

    /** @test */
    public function it_can_make_delete_requests()
    {
        $this->mockResponse(200, '{"foo":"bar"}');

        $this->apiClient->delete('/')->response();

        $request_method = $this->apiClient->getRequest()->getMethod();

        $this->assertEquals('DELETE', $request_method);
    }

    /**
     * @test
     */
    public function it_can_get_status_code()
    {
        $this->mockResponse(200, '{"foo":"bar"}');

        $response = $this->apiClient->get('/')->response();

        $status = $this->apiClient->getStatusCode();
        $this->assertEquals(200, $status);
        $this->assertArrayHasKey('foo', $response);
    }

    /**
     * @test
     */
    public function it_can_get_the_config()
    {
        $this->mockResponse(200, '{"foo":"bar"}');

        $config = $this->apiClient->config();

        $this->assertInternalType('array', $config);
        $this->assertArrayHasKey('headers', $config);
        $this->assertArrayHasKey('Authorization', $config['headers']);
        $this->assertSame('Bearer VALID', $config['headers']['Authorization']);
    }

    /**
     * @test
     */
    public function it_can_handle_unexpected_error_response()
    {
        $this->mockResponse(500);
        $this->setExpectedException('\Epignosis\TalentCards\Exceptions\BadResponseException');
        $this->apiClient->get('/')->response();
    }

    /** @test */
    public function it_throws_an_exception_when_there_is_a_network_connectivity_issue()
    {
        $this->mockHandler = MockHandler::createWithMiddleware([
                                                                   new \GuzzleHttp\Exception\ConnectException('Error Communicating with Server',
                                                                                                              new Request('GET',
                                                                                                                          'test')),
                                                               ]);
        $client            = new Client;
        $config            = ['handler' => $this->mockHandler];
        $client->setKey('VALID')->setClient(new GuzzleHttp\Client($config));
        $this->apiClient = $client;

        $this->setExpectedException('Epignosis\TalentCards\Exceptions\ConnectionException');

        try {
            $this->apiClient->get('/')->response();
        } catch (Epignosis\TalentCards\Exceptions\ConnectionException $e) {
            $this->assertSame('Could not connect to the TalentCards server. Please check your internet connectivity and try again later.',
                              $e->getMessage());
            throw $e;
        }

        $this->fail('Expected Exception is not thrown');
    }

    /**
     * @test
     */
    public function it_can_build_correct_request_url_with_empty_query_string()
    {
        $this->mockResponse(200, '{"foo":"bar"}');
        $endpoint = '/test';
        $this->apiClient->get($endpoint)->response();
        $url = $this->apiClient->buildUrl($endpoint, '');

        $this->assertEquals("{$this->apiClient->getUrl()}/test", $url);
    }

    /**
     * @test
     */
    public function it_can_build_correct_request_url_with_a_non_empty_query_string()
    {
        $this->mockResponse(200, '{"foo":"bar"}');
        $endpoint = '/test';

        $query_params = [
            'sort'   => ['-email'],
            'filter' => [
                'first-name' => 'John',
            ],
            'fields' => [
                'users' => [
                    '*',
                ],
            ],
        ];

        $expected = "{$this->apiClient->getUrl()}/test?".
            rawurlencode('sort=-email&filter[first-name]=John&fields[users]=*');

        $this->apiClient->get($endpoint)->response();

        $url = $this->apiClient->buildUrl($endpoint, $this->apiClient->buildQuery($query_params));

        $this->assertEquals($expected, $url);
    }
}