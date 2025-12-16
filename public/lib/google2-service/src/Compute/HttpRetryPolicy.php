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

class HttpRetryPolicy extends \Google\Collection
{
  protected $collection_key = 'retryConditions';
  /**
   * Specifies the allowed number retries. This number must be > 0. If not
   * specified, defaults to 1.
   *
   * @var string
   */
  public $numRetries;
  protected $perTryTimeoutType = Duration::class;
  protected $perTryTimeoutDataType = '';
  /**
   * Specifies one or more conditions when this retry policy applies. Valid
   * values are:        - 5xx: retry is attempted if the instance or endpoint
   * responds with any 5xx response code, or if the instance or    endpoint does
   * not respond at all. For example, disconnects, reset, read    timeout,
   * connection failure, and refused streams.    - gateway-error: Similar to
   * 5xx, but only    applies to response codes 502, 503 or504.    - connect-
   * failure: a retry is attempted on failures    connecting to the instance or
   * endpoint. For example, connection    timeouts.    - retriable-4xx: a retry
   * is attempted if the instance    or endpoint responds with a 4xx response
   * code.    The only error that you can retry is error code 409.    - refused-
   * stream: a retry is attempted if the instance    or endpoint resets the
   * stream with a REFUSED_STREAM error    code. This reset type indicates that
   * it is safe to retry.    - cancelled: a retry is attempted if the gRPC
   * status    code in the response header is set to cancelled.    - deadline-
   * exceeded: a retry is attempted if the gRPC    status code in the response
   * header is set todeadline-exceeded.    - internal: a retry is attempted if
   * the gRPC    status code in the response header is set tointernal.    -
   * resource-exhausted: a retry is attempted if the gRPC    status code in the
   * response header is set toresource-exhausted.    - unavailable: a retry is
   * attempted if the gRPC    status code in the response header is set
   * tounavailable.
   *
   * Only the following codes are supported when the URL map is bound to target
   * gRPC proxy that has validateForProxyless field set to true.        -
   * cancelled    - deadline-exceeded    - internal    - resource-exhausted    -
   * unavailable
   *
   * @var string[]
   */
  public $retryConditions;

  /**
   * Specifies the allowed number retries. This number must be > 0. If not
   * specified, defaults to 1.
   *
   * @param string $numRetries
   */
  public function setNumRetries($numRetries)
  {
    $this->numRetries = $numRetries;
  }
  /**
   * @return string
   */
  public function getNumRetries()
  {
    return $this->numRetries;
  }
  /**
   * Specifies a non-zero timeout per retry attempt.
   *
   * If not specified, will use the timeout set in theHttpRouteAction field. If
   * timeout in the HttpRouteAction field is not set, this field uses the
   * largest timeout among all backend services associated with the route.
   *
   * Not supported when the URL map is bound to a target gRPC proxy that has the
   * validateForProxyless field set to true.
   *
   * @param Duration $perTryTimeout
   */
  public function setPerTryTimeout(Duration $perTryTimeout)
  {
    $this->perTryTimeout = $perTryTimeout;
  }
  /**
   * @return Duration
   */
  public function getPerTryTimeout()
  {
    return $this->perTryTimeout;
  }
  /**
   * Specifies one or more conditions when this retry policy applies. Valid
   * values are:        - 5xx: retry is attempted if the instance or endpoint
   * responds with any 5xx response code, or if the instance or    endpoint does
   * not respond at all. For example, disconnects, reset, read    timeout,
   * connection failure, and refused streams.    - gateway-error: Similar to
   * 5xx, but only    applies to response codes 502, 503 or504.    - connect-
   * failure: a retry is attempted on failures    connecting to the instance or
   * endpoint. For example, connection    timeouts.    - retriable-4xx: a retry
   * is attempted if the instance    or endpoint responds with a 4xx response
   * code.    The only error that you can retry is error code 409.    - refused-
   * stream: a retry is attempted if the instance    or endpoint resets the
   * stream with a REFUSED_STREAM error    code. This reset type indicates that
   * it is safe to retry.    - cancelled: a retry is attempted if the gRPC
   * status    code in the response header is set to cancelled.    - deadline-
   * exceeded: a retry is attempted if the gRPC    status code in the response
   * header is set todeadline-exceeded.    - internal: a retry is attempted if
   * the gRPC    status code in the response header is set tointernal.    -
   * resource-exhausted: a retry is attempted if the gRPC    status code in the
   * response header is set toresource-exhausted.    - unavailable: a retry is
   * attempted if the gRPC    status code in the response header is set
   * tounavailable.
   *
   * Only the following codes are supported when the URL map is bound to target
   * gRPC proxy that has validateForProxyless field set to true.        -
   * cancelled    - deadline-exceeded    - internal    - resource-exhausted    -
   * unavailable
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
class_alias(HttpRetryPolicy::class, 'Google_Service_Compute_HttpRetryPolicy');
