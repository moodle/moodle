<?php
/**
 * Copyright 2024 Google Inc. All Rights Reserved.
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

namespace Google\Auth\Logging;

use Psr\Log\LogLevel;

/**
 * A trait used to call a PSR-3 logging interface.
 *
 * @internal
 */
trait LoggingTrait
{
    /**
     * @param RpcLogEvent $event
     */
    private function logRequest(RpcLogEvent $event): void
    {
        $debugEvent = [
            'timestamp' => $event->timestamp,
            'severity' => strtoupper(LogLevel::DEBUG),
            'processId' => $event->processId ?? null,
            'requestId' => $event->requestId ?? null,
            'rpcName' => $event->rpcName ?? null,
        ];

        $debugEvent = array_filter($debugEvent, fn ($value) => !is_null($value));

        $jsonPayload = [
            'request.method' => $event->method,
            'request.url' => $event->url,
            'request.headers' => $event->headers,
            'request.payload' => $this->truncatePayload($event->payload),
            'request.jwt' => $this->getJwtToken($event->headers ?? []),
            'retryAttempt' => $event->retryAttempt
        ];

        // Remove null values
        $debugEvent['jsonPayload'] = array_filter($jsonPayload, fn ($value) => !is_null($value));

        $stringifiedEvent = json_encode($debugEvent, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        // There was an error stringifying the event, return to not break execution
        if ($stringifiedEvent === false) {
            return;
        }

        $this->logger->debug($stringifiedEvent);
    }

    /**
     * @param RpcLogEvent $event
     */
    private function logResponse(RpcLogEvent $event): void
    {
        $debugEvent = [
            'timestamp' => $event->timestamp,
            'severity' => strtoupper(LogLevel::DEBUG),
            'processId' => $event->processId ?? null,
            'requestId' => $event->requestId ?? null,
            'jsonPayload' => [
                'response.status' => $event->status,
                'response.headers' => $event->headers,
                'response.payload' => $this->truncatePayload($event->payload),
                'latencyMillis' => $event->latency,
            ]
        ];

        // Remove null values
        $debugEvent = array_filter($debugEvent, fn ($value) => !is_null($value));
        $debugEvent['jsonPayload'] = array_filter(
            $debugEvent['jsonPayload'],
            fn ($value) => !is_null($value)
        );

        $stringifiedEvent = json_encode($debugEvent, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        // There was an error stringifying the event, return to not break execution
        if ($stringifiedEvent !== false) {
            $this->logger->debug($stringifiedEvent);
        }
    }

    /**
     * @param array<mixed> $headers
     * @return null|array<string, string|false>
     */
    private function getJwtToken(array $headers): null|array
    {
        if (empty($headers)) {
            return null;
        }

        $tokenHeader = $headers['Authorization'] ?? '';
        $token = str_replace('Bearer ', '', $tokenHeader);

        if (substr_count($token, '.') !== 2) {
            return null;
        }

        [$header, $token, $_] = explode('.', $token);

        return [
            'header' => base64_decode($header),
            'token' => base64_decode($token)
        ];
    }

    /**
     * @param null|string $payload
     * @return string
     */
    private function truncatePayload(null|string $payload): null|string
    {
        $maxLength = 500;

        if (is_null($payload) || strlen($payload) <= $maxLength) {
            return $payload;
        }

        return substr($payload, 0, $maxLength) . '...';
    }
}
