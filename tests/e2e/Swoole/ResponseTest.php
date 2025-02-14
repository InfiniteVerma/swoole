<?php

namespace Utopia\Tests;

use Tests\E2E\Client;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    public function setUp(): void
    {
        $this->client = new Client();
    }

    public function tearDown(): void
    {
        $this->client = null;
    }

    protected ?Client $client;

    public function testCanRespond(): void
    {
        $response = $this->client->call(Client::METHOD_GET, '/');
        $this->assertEquals('Hello World!', $response['body']);
    }

    public function testCanRespondWithChunck(): void
    {
        $response = $this->client->call(Client::METHOD_GET, '/chunked');
        $this->assertEquals('Hello World!', $response['body']);
    }

    public function testCanRespondWithRedirect(): void
    {
        $response = $this->client->call(Client::METHOD_GET, '/redirect');
        $this->assertEquals('Hello World!', $response['body']);
    }

    public function providerForwardProtocolHeader(): array
    {
        return [
            'http' => ['http'],
            'https' => ['https'],
            'ws' => ['ws'],
            'wss' => ['wss']
        ];
    }

    /**
     * @dataProvider providerForwardProtocolHeader
     */
    public function testCanForwardProtocolHeader(string $protocol): void
    {
        $responseinvalid = $this->client->call(Client::METHOD_GET, '/protocol', array(
            "x-forwarded-proto: {$protocol}"
        ));
        $this->assertEquals($protocol, $responseinvalid['body']);
    }

    public function testCantForwardUnknownProtocolHeader(): void
    {
        $responseinvalid = $this->client->call(Client::METHOD_GET, '/protocol', [
            'x-forwarded-proto: randomjibberish'
        ]);
        $this->assertEquals('https', $responseinvalid['body']);
    }
}
