<?php
/**
 * Copyright 2015 Google Inc. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace Google\Auth\HttpHandler;

use Google\Auth\Logging\LoggingTrait;
use Google\Auth\Logging\RpcLogEvent;
use GuzzleHttp\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

class Guzzle6HttpHandler
{
    use LoggingTrait;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var null|LoggerInterface
     */
    private $logger;

    /**
     * @param ClientInterface $client
     * @param null|LoggerInterface $logger
     */
    public function __construct(ClientInterface $client, ?LoggerInterface $logger = null)
    {
        $this->client = $client;
        $this->logger = $logger;
    }

    /**
     * Accepts a PSR-7 request and an array of options and returns a PSR-7 response.
     *
     * @param RequestInterface $request
     * @param array<mixed> $options
     * @return ResponseInterface
     */
    public function __invoke(RequestInterface $request, array $options = [])
    {
        $requestEvent = null;

        if ($this->logger) {
            $requestEvent = $this->requestLog($request, $options);
        }

        $response = $this->client->send($request, $options);

        if ($this->logger) {
            $this->responseLog($response, $requestEvent);
        }

        return $response;
    }

    /**
     * Accepts a PSR-7 request and an array of options and returns a PromiseInterface
     *
     * @param RequestInterface $request
     * @param array<mixed> $options
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function async(RequestInterface $request, array $options = [])
    {
        $requestEvent = null;

        if ($this->logger) {
            $requestEvent = $this->requestLog($request, $options);
        }

        $promise = $this->client->sendAsync($request, $options);

        if ($this->logger) {
            $promise->then(function (ResponseInterface $response) use ($requestEvent) {
                $this->responseLog($response, $requestEvent);
                return $response;
            });
        }

        return $promise;
    }

    /**
     * @internal
     * @param RequestInterface $request
     * @param array<mixed> $options
     */
    public function requestLog(RequestInterface $request, array $options): RpcLogEvent
    {
        $requestEvent = new RpcLogEvent();

        $requestEvent->method = $request->getMethod();
        $requestEvent->url = (string) $request->getUri();
        $requestEvent->headers = $request->getHeaders();
        $requestEvent->payload = $request->getBody()->getContents();
        $requestEvent->retryAttempt = $options['retryAttempt'] ?? null;
        $requestEvent->serviceName = $options['serviceName'] ?? null;
        $requestEvent->processId = (int) getmypid();
        $requestEvent->requestId = $options['requestId'] ?? crc32((string) spl_object_id($request) . getmypid());

        $this->logRequest($requestEvent);

        return $requestEvent;
    }

    /**
     * @internal
     */
    public function responseLog(ResponseInterface $response, RpcLogEvent $requestEvent): void
    {
        $responseEvent = new RpcLogEvent($requestEvent->milliseconds);

        $responseEvent->headers = $response->getHeaders();
        $responseEvent->payload = $response->getBody()->getContents();
        $responseEvent->status = $response->getStatusCode();
        $responseEvent->processId = $requestEvent->processId;
        $responseEvent->requestId = $requestEvent->requestId;

        $this->logResponse($responseEvent);
    }
}
