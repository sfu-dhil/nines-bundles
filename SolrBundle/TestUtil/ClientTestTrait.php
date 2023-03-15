<?php

declare(strict_types=1);

namespace Nines\SolrBundle\TestUtil;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Nyholm\Psr7\Factory\Psr17Factory;
use Solarium\Client;
use Solarium\Core\Client\Adapter\Psr18Adapter;

trait ClientTestTrait {
    private array $responses = [];

    /**
     * @param array|string $data
     * @param ?int $status
     */
    protected function addResponse($data, ?int $status = 200) : void {
        if (is_array($data)) {
            $data = json_encode($data);
        }
        $this->responses[] = new Response($status, ['Content-Type' => 'text/json'], $data);
    }

    protected function mockAdapter() : Psr18Adapter {
        $mock = new MockHandler($this->responses);
        $stack = HandlerStack::create($mock);

        $httpClient = new GuzzleClient(['handler' => $stack]);
        $factory = new Psr17Factory();
        $this->responses = [];

        return new Psr18Adapter($httpClient, $factory, $factory);
    }

    protected function mockClient() : Client {
        $client = static::getContainer()->get(Client::class);
        $adapter = $this->mockAdapter();
        $client->setAdapter($adapter);

        return $client;
    }
}
