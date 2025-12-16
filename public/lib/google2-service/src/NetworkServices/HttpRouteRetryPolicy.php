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

namespace Google\Service\NetworkServices;

class HttpRouteRetryPolicy extends \Google\Collection
{
  protected $collection_key = 'retryConditions';
  /**
   * Specifies the allowed number of retries. This number must be > 0. If not
   * specified, default to 1.
   *
   * @var int
   */
  public $numRetries;
  /**
   * Specifies a non-zero timeout per retry attempt.
   *
   * @var string
   */
  public $perTryTimeout;
  /**
   * Specifies one or more conditions when this retry policy applies. Valid
   * values are: 5xx: Proxy will attempt a retry if the destination service
   * responds with any 5xx response code, of if the destination service does not
   * respond at all, example: disconnect, reset, read timeout, connection
   * failure and refused streams. gateway-error: Similar to 5xx, but only
   * applies to response codes 502, 503, 504. reset: Proxy will attempt a retry
   * if the destination service does not respond at all (disconnect/reset/read
   * timeout) connect-failure: Proxy will retry on failures connecting to
   * destination for example due to connection timeouts. retriable-4xx: Proxy
   * will retry fro retriable 4xx response codes. Currently the only retriable
   * error supported is 409. refused-stream: Proxy will retry if the destination
   * resets the stream with a REFUSED_STREAM error code. This reset type
   * indicates that it is safe to retry.
   *
   * @var string[]
   */
  public $retryConditions;

  /**
   * Specifies the allowed number of retries. This number must be > 0. If not
   * specified, default to 1.
   *
   * @param int $numRetries
   */
  public function setNumRetries($numRetries)
  {
    $this->numRetries = $numRetries;
  }
  /**
   * @return int
   */
  public function getNumRetries()
  {
    return $this->numRetries;
  }
  /**
   * Specifies a non-zero timeout per retry attempt.
   *
   * @param string $perTryTimeout
   */
  public function setPerTryTimeout($perTryTimeout)
  {
    $this->perTryTimeout = $perTryTimeout;
  }
  /**
   * @return string
   */
  public function getPerTryTimeout()
  {
    return $this->perTryTimeout;
  }
  /**
   * Specifies one or more conditions when this retry policy applies. Valid
   * values are: 5xx: Proxy will attempt a retry if the destination service
   * responds with any 5xx response code, of if the destination service does not
   * respond at all, example: disconnect, reset, read timeout, connection
   * failure and refused streams. gateway-error: Similar to 5xx, but only
   * applies to response codes 502, 503, 504. reset: Proxy will attempt a retry
   * if the destination service does not respond at all (disconnect/reset/read
   * timeout) connect-failure: Proxy will retry on failures connecting to
   * destination for example due to connection timeouts. retriable-4xx: Proxy
   * will retry fro retriable 4xx response codes. Currently the only retriable
   * error supported is 409. refused-stream: Proxy will retry if the destination
   * resets the stream with a REFUSED_STREAM error code. This reset type
   * indicates that it is safe to retry.
   *
   * @param string[] $retryConditions
   */
  public function setRetryConditions($retryConditions)
  {
    $this->retryConditions = $retryConditions;
  }
  /**
   * @return string[]
   */
  public function getRetryConditions()
  {
    return $this->retryConditions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HttpRouteRetryPolicy::class, 'Google_Service_NetworkServices_HttpRouteRetryPolicy');
