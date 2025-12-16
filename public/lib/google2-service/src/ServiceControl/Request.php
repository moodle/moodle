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

namespace Google\Service\ServiceControl;

class Request extends \Google\Model
{
  protected $authType = Auth::class;
  protected $authDataType = '';
  /**
   * The HTTP request headers. If multiple headers share the same key, they must
   * be merged according to the HTTP spec. All header keys must be lowercased,
   * because HTTP header keys are case-insensitive.
   *
   * @var string[]
   */
  public $headers;
  /**
   * The HTTP request `Host` header value.
   *
   * @var string
   */
  public $host;
  /**
   * The unique ID for a request, which can be propagated to downstream systems.
   * The ID should have low probability of collision within a single day for a
   * specific service.
   *
   * @var string
   */
  public $id;
  /**
   * The HTTP request method, such as `GET`, `POST`.
   *
   * @var string
   */
  public $method;
  /**
   * The values from Origin header from the HTTP request, such as
   * "https://console.cloud.google.com". Modern browsers can only have one
   * origin. Special browsers and/or HTTP clients may require multiple origins.
   *
   * @var string
   */
  public $origin;
  /**
   * The HTTP URL path, excluding the query parameters.
   *
   * @var string
   */
  public $path;
  /**
   * The network protocol used with the request, such as "http/1.1", "spdy/3",
   * "h2", "h2c", "webrtc", "tcp", "udp", "quic". See
   * https://www.iana.org/assignments/tls-extensiontype-values/tls-
   * extensiontype-values.xhtml#alpn-protocol-ids for details.
   *
   * @var string
   */
  public $protocol;
  /**
   * The HTTP URL query in the format of `name1=value1&name2=value2`, as it
   * appears in the first line of the HTTP request. No decoding is performed.
   *
   * @var string
   */
  public $query;
  /**
   * A special parameter for request reason. It is used by security systems to
   * associate auditing information with a request.
   *
   * @var string
   */
  public $reason;
  /**
   * The HTTP URL scheme, such as `http` and `https`.
   *
   * @var string
   */
  public $scheme;
  /**
   * The HTTP request size in bytes. If unknown, it must be -1.
   *
   * @var string
   */
  public $size;
  /**
   * The timestamp when the `destination` service receives the last byte of the
   * request.
   *
   * @var string
   */
  public $time;

  /**
   * The request authentication. May be absent for unauthenticated requests.
   * Derived from the HTTP request `Authorization` header or equivalent.
   *
   * @param Auth $auth
   */
  public function setAuth(Auth $auth)
  {
    $this->auth = $auth;
  }
  /**
   * @return Auth
   */
  public function getAuth()
  {
    return $this->auth;
  }
  /**
   * The HTTP request headers. If multiple headers share the same key, they must
   * be merged according to the HTTP spec. All header keys must be lowercased,
   * because HTTP header keys are case-insensitive.
   *
   * @param string[] $headers
   */
  public function setHeaders($headers)
  {
    $this->headers = $headers;
  }
  /**
   * @return string[]
   */
  public function getHeaders()
  {
    return $this->headers;
  }
  /**
   * The HTTP request `Host` header value.
   *
   * @param string $host
   */
  public function setHost($host)
  {
    $this->host = $host;
  }
  /**
   * @return string
   */
  public function getHost()
  {
    return $this->host;
  }
  /**
   * The unique ID for a request, which can be propagated to downstream systems.
   * The ID should have low probability of collision within a single day for a
   * specific service.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * The HTTP request method, such as `GET`, `POST`.
   *
   * @param string $method
   */
  public function setMethod($method)
  {
    $this->method = $method;
  }
  /**
   * @return string
   */
  public function getMethod()
  {
    return $this->method;
  }
  /**
   * The values from Origin header from the HTTP request, such as
   * "https://console.cloud.google.com". Modern browsers can only have one
   * origin. Special browsers and/or HTTP clients may require multiple origins.
   *
   * @param string $origin
   */
  public function setOrigin($origin)
  {
    $this->origin = $origin;
  }
  /**
   * @return string
   */
  public function getOrigin()
  {
    return $this->origin;
  }
  /**
   * The HTTP URL path, excluding the query parameters.
   *
   * @param string $path
   */
  public function setPath($path)
  {
    $this->path = $path;
  }
  /**
   * @return string
   */
  public function getPath()
  {
    return $this->path;
  }
  /**
   * The network protocol used with the request, such as "http/1.1", "spdy/3",
   * "h2", "h2c", "webrtc", "tcp", "udp", "quic". See
   * https://www.iana.org/assignments/tls-extensiontype-values/tls-
   * extensiontype-values.xhtml#alpn-protocol-ids for details.
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
   * The HTTP URL query in the format of `name1=value1&name2=value2`, as it
   * appears in the first line of the HTTP request. No decoding is performed.
   *
   * @param string $query
   */
  public function setQuery($query)
  {
    $this->query = $query;
  }
  /**
   * @return string
   */
  public function getQuery()
  {
    return $this->query;
  }
  /**
   * A special parameter for request reason. It is used by security systems to
   * associate auditing information with a request.
   *
   * @param string $reason
   */
  public function setReason($reason)
  {
    $this->reason = $reason;
  }
  /**
   * @return string
   */
  public function getReason()
  {
    return $this->reason;
  }
  /**
   * The HTTP URL scheme, such as `http` and `https`.
   *
   * @param string $scheme
   */
  public function setScheme($scheme)
  {
    $this->scheme = $scheme;
  }
  /**
   * @return string
   */
  public function getScheme()
  {
    return $this->scheme;
  }
  /**
   * The HTTP request size in bytes. If unknown, it must be -1.
   *
   * @param string $size
   */
  public function setSize($size)
  {
    $this->size = $size;
  }
  /**
   * @return string
   */
  public function getSize()
  {
    return $this->size;
  }
  /**
   * The timestamp when the `destination` service receives the last byte of the
   * request.
   *
   * @param string $time
   */
  public function setTime($time)
  {
    $this->time = $time;
  }
  /**
   * @return string
   */
  public function getTime()
  {
    return $this->time;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Request::class, 'Google_Service_ServiceControl_Request');
