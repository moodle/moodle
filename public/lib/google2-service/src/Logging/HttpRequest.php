<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\Logging;

class HttpRequest extends \Google\Model
{
  /**
   * The number of HTTP response bytes inserted into cache. Set only when a
   * cache fill was attempted.
   *
   * @var string
   */
  public $cacheFillBytes;
  /**
   * Whether or not an entity was served from cache (with or without
   * validation).
   *
   * @var bool
   */
  public $cacheHit;
  /**
   * Whether or not a cache lookup was attempted.
   *
   * @var bool
   */
  public $cacheLookup;
  /**
   * Whether or not the response was validated with the origin server before
   * being served from cache. This field is only meaningful if cache_hit is
   * True.
   *
   * @var bool
   */
  public $cacheValidatedWithOriginServer;
  /**
   * The request processing latency on the server, from the time the request was
   * received until the response was sent. For WebSocket connections, this field
   * refers to the entire time duration of the connection.
   *
   * @var string
   */
  public $latency;
  /**
   * Protocol used for the request. Examples: "HTTP/1.1", "HTTP/2"
   *
   * @var string
   */
  public $protocol;
  /**
   * The referer URL of the request, as defined in HTTP/1.1 Header Field
   * Definitions (https://datatracker.ietf.org/doc/html/rfc2616#section-14.36).
   *
   * @var string
   */
  public $referer;
  /**
   * The IP address (IPv4 or IPv6) of the client that issued the HTTP request.
   * This field can include port information. Examples: "192.168.1.1",
   * "10.0.0.1:80", "FE80::0202:B3FF:FE1E:8329".
   *
   * @var string
   */
  public $remoteIp;
  /**
   * The request method. Examples: "GET", "HEAD", "PUT", "POST".
   *
   * @var string
   */
  public $requestMethod;
  /**
   * The size of the HTTP request message in bytes, including the request
   * headers and the request body.
   *
   * @var string
   */
  public $requestSize;
  /**
   * The scheme (http, https), the host name, the path and the query portion of
   * the URL that was requested. Example:
   * "http://example.com/some/info?color=red".
   *
   * @var string
   */
  public $requestUrl;
  /**
   * The size of the HTTP response message sent back to the client, in bytes,
   * including the response headers and the response body.
   *
   * @var string
   */
  public $responseSize;
  /**
   * The IP address (IPv4 or IPv6) of the origin server that the request was
   * sent to. This field can include port information. Examples: "192.168.1.1",
   * "10.0.0.1:80", "FE80::0202:B3FF:FE1E:8329".
   *
   * @var string
   */
  public $serverIp;
  /**
   * The response code indicating the status of response. Examples: 200, 404.
   *
   * @var int
   */
  public $status;
  /**
   * The user agent sent by the client. Example: "Mozilla/4.0 (compatible; MSIE
   * 6.0; Windows 98; Q312461; .NET CLR 1.0.3705)".
   *
   * @var string
   */
  public $userAgent;

  /**
   * The number of HTTP response bytes inserted into cache. Set only when a
   * cache fill was attempted.
   *
   * @param string $cacheFillBytes
   */
  public function setCacheFillBytes($cacheFillBytes)
  {
    $this->cacheFillBytes = $cacheFillBytes;
  }
  /**
   * @return string
   */
  public function getCacheFillBytes()
  {
    return $this->cacheFillBytes;
  }
  /**
   * Whether or not an entity was served from cache (with or without
   * validation).
   *
   * @param bool $cacheHit
   */
  public function setCacheHit($cacheHit)
  {
    $this->cacheHit = $cacheHit;
  }
  /**
   * @return bool
   */
  public function getCacheHit()
  {
    return $this->cacheHit;
  }
  /**
   * Whether or not a cache lookup was attempted.
   *
   * @param bool $cacheLookup
   */
  public function setCacheLookup($cacheLookup)
  {
    $this->cacheLookup = $cacheLookup;
  }
  /**
   * @return bool
   */
  public function getCacheLookup()
  {
    return $this->cacheLookup;
  }
  /**
   * Whether or not the response was validated with the origin server before
   * being served from cache. This field is only meaningful if cache_hit is
   * True.
   *
   * @param bool $cacheValidatedWithOriginServer
   */
  public function setCacheValidatedWithOriginServer($cacheValidatedWithOriginServer)
  {
    $this->cacheValidatedWithOriginServer = $cacheValidatedWithOriginServer;
  }
  /**
   * @return bool
   */
  public function getCacheValidatedWithOriginServer()
  {
    return $this->cacheValidatedWithOriginServer;
  }
  /**
   * The request processing latency on the server, from the time the request was
   * received until the response was sent. For WebSocket connections, this field
   * refers to the entire time duration of the connection.
   *
   * @param string $latency
   */
  public function setLatency($latency)
  {
    $this->latency = $latency;
  }
  /**
   * @return string
   */
  public function getLatency()
  {
    return $this->latency;
  }
  /**
   * Protocol used for the request. Examples: "HTTP/1.1", "HTTP/2"
   *
   * @param string $protocol
   */
  public function setProtocol($protocol)
  {
    $this->protocol = $protocol;
  }
  /**
   * @return string
   */
  public function getProtocol()
  {
    return $this->protocol;
  }
  /**
   * The referer URL of the request, as defined in HTTP/1.1 Header Field
   * Definitions (https://datatracker.ietf.org/doc/html/rfc2616#section-14.36).
   *
   * @param string $referer
   */
  public function setReferer($referer)
  {
    $this->referer = $referer;
  }
  /**
   * @return string
   */
  public function getReferer()
  {
    return $this->referer;
  }
  /**
   * The IP address (IPv4 or IPv6) of the client that issued the HTTP request.
   * This field can include port information. Examples: "192.168.1.1",
   * "10.0.0.1:80", "FE80::0202:B3FF:FE1E:8329".
   *
   * @param string $remoteIp
   */
  public function setRemoteIp($remoteIp)
  {
    $this->remoteIp = $remoteIp;
  }
  /**
   * @return string
   */
  public function getRemoteIp()
  {
    return $this->remoteIp;
  }
  /**
   * The request method. Examples: "GET", "HEAD", "PUT", "POST".
   *
   * @param string $requestMethod
   */
  public function setRequestMethod($requestMethod)
  {
    $this->requestMethod = $requestMethod;
  }
  /**
   * @return string
   */
  public function getRequestMethod()
  {
    return $this->requestMethod;
  }
  /**
   * The size of the HTTP request message in bytes, including the request
   * headers and the request body.
   *
   * @param string $requestSize
   */
  public function setRequestSize($requestSize)
  {
    $this->requestSize = $requestSize;
  }
  /**
   * @return string
   */
  public function getRequestSize()
  {
    return $this->requestSize;
  }
  /**
   * The scheme (http, https), the host name, the path and the query portion of
   * the URL that was requested. Example:
   * "http://example.com/some/info?color=red".
   *
   * @param string $requestUrl
   */
  public function setRequestUrl($requestUrl)
  {
    $this->requestUrl = $requestUrl;
  }
  /**
   * @return string
   */
  public function getRequestUrl()
  {
    return $this->requestUrl;
  }
  /**
   * The size of the HTTP response message sent back to the client, in bytes,
   * including the response headers and the response body.
   *
   * @param string $responseSize
   */
  public function setResponseSize($responseSize)
  {
    $this->responseSize = $responseSize;
  }
  /**
   * @return string
   */
  public function getResponseSize()
  {
    return $this->responseSize;
  }
  /**
   * The IP address (IPv4 or IPv6) of the origin server that the request was
   * sent to. This field can include port information. Examples: "192.168.1.1",
   * "10.0.0.1:80", "FE80::0202:B3FF:FE1E:8329".
   *
   * @param string $serverIp
   */
  public function setServerIp($serverIp)
  {
    $this->serverIp = $serverIp;
  }
  /**
   * @return string
   */
  public function getServerIp()
  {
    return $this->serverIp;
  }
  /**
   * The response code indicating the status of response. Examples: 200, 404.
   *
   * @param int $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return int
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * The user agent sent by the client. Example: "Mozilla/4.0 (compatible; MSIE
   * 6.0; Windows 98; Q312461; .NET CLR 1.0.3705)".
   *
   * @param string $userAgent
   */
  public function setUserAgent($userAgent)
  {
    $this->userAgent = $userAgent;
  }
  /**
   * @return string
   */
  public function getUserAgent()
  {
    return $this->userAgent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HttpRequest::class, 'Google_Service_Logging_HttpRequest');
