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

namespace Google\Service\Compute;

class CircuitBreakers extends \Google\Model
{
  /**
   * The maximum number of connections to the backend service. If not specified,
   * there is no limit.
   *
   * Not supported when the backend service is referenced by a URL map that is
   * bound to target gRPC proxy that has validateForProxyless field set to true.
   *
   * @var int
   */
  public $maxConnections;
  /**
   * The maximum number of pending requests allowed to the backend service. If
   * not specified, there is no limit.
   *
   * Not supported when the backend service is referenced by a URL map that is
   * bound to target gRPC proxy that has validateForProxyless field set to true.
   *
   * @var int
   */
  public $maxPendingRequests;
  /**
   * The maximum number of parallel requests that allowed to the backend
   * service. If not specified, there is no limit.
   *
   * @var int
   */
  public $maxRequests;
  /**
   * Maximum requests for a single connection to the backend service. This
   * parameter is respected by both the HTTP/1.1 and HTTP/2 implementations. If
   * not specified, there is no limit. Setting this parameter to 1 will
   * effectively disable keep alive.
   *
   * Not supported when the backend service is referenced by a URL map that is
   * bound to target gRPC proxy that has validateForProxyless field set to true.
   *
   * @var int
   */
  public $maxRequestsPerConnection;
  /**
   * The maximum number of parallel retries allowed to the backend cluster. If
   * not specified, the default is 1.
   *
   * Not supported when the backend service is referenced by a URL map that is
   * bound to target gRPC proxy that has validateForProxyless field set to true.
   *
   * @var int
   */
  public $maxRetries;

  /**
   * The maximum number of connections to the backend service. If not specified,
   * there is no limit.
   *
   * Not supported when the backend service is referenced by a URL map that is
   * bound to target gRPC proxy that has validateForProxyless field set to true.
   *
   * @param int $maxConnections
   */
  public function setMaxConnections($maxConnections)
  {
    $this->maxConnections = $maxConnections;
  }
  /**
   * @return int
   */
  public function getMaxConnections()
  {
    return $this->maxConnections;
  }
  /**
   * The maximum number of pending requests allowed to the backend service. If
   * not specified, there is no limit.
   *
   * Not supported when the backend service is referenced by a URL map that is
   * bound to target gRPC proxy that has validateForProxyless field set to true.
   *
   * @param int $maxPendingRequests
   */
  public function setMaxPendingRequests($maxPendingRequests)
  {
    $this->maxPendingRequests = $maxPendingRequests;
  }
  /**
   * @return int
   */
  public function getMaxPendingRequests()
  {
    return $this->maxPendingRequests;
  }
  /**
   * The maximum number of parallel requests that allowed to the backend
   * service. If not specified, there is no limit.
   *
   * @param int $maxRequests
   */
  public function setMaxRequests($maxRequests)
  {
    $this->maxRequests = $maxRequests;
  }
  /**
   * @return int
   */
  public function getMaxRequests()
  {
    return $this->maxRequests;
  }
  /**
   * Maximum requests for a single connection to the backend service. This
   * parameter is respected by both the HTTP/1.1 and HTTP/2 implementations. If
   * not specified, there is no limit. Setting this parameter to 1 will
   * effectively disable keep alive.
   *
   * Not supported when the backend service is referenced by a URL map that is
   * bound to target gRPC proxy that has validateForProxyless field set to true.
   *
   * @param int $maxRequestsPerConnection
   */
  public function setMaxRequestsPerConnection($maxRequestsPerConnection)
  {
    $this->maxRequestsPerConnection = $maxRequestsPerConnection;
  }
  /**
   * @return int
   */
  public function getMaxRequestsPerConnection()
  {
    return $this->maxRequestsPerConnection;
  }
  /**
   * The maximum number of parallel retries allowed to the backend cluster. If
   * not specified, the default is 1.
   *
   * Not supported when the backend service is referenced by a URL map that is
   * bound to target gRPC proxy that has validateForProxyless field set to true.
   *
   * @param int $maxRetries
   */
  public function setMaxRetries($maxRetries)
  {
    $this->maxRetries = $maxRetries;
  }
  /**
   * @return int
   */
  public function getMaxRetries()
  {
    return $this->maxRetries;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CircuitBreakers::class, 'Google_Service_Compute_CircuitBreakers');
