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

class GrpcRouteRetryPolicy extends \Google\Collection
{
  protected $collection_key = 'retryConditions';
  /**
   * Specifies the allowed number of retries. This number must be > 0. If not
   * specified, default to 1.
   *
   * @var string
   */
  public $numRetries;
  /**
   * - connect-failure: Router will retry on failures connecting to Backend
   * Services, for example due to connection timeouts. - refused-stream: Router
   * will retry if the backend service resets the stream with a REFUSED_STREAM
   * error code. This reset type indicates that it is safe to retry. -
   * cancelled: Router will retry if the gRPC status code in the response header
   * is set to cancelled - deadline-exceeded: Router will retry if the gRPC
   * status code in the response header is set to deadline-exceeded - resource-
   * exhausted: Router will retry if the gRPC status code in the response header
   * is set to resource-exhausted - unavailable: Router will retry if the gRPC
   * status code in the response header is set to unavailable
   *
   * @var string[]
   */
  public $retryConditions;

  /**
   * Specifies the allowed number of retries. This number must be > 0. If not
   * specified, default to 1.
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
   * - connect-failure: Router will retry on failures connecting to Backend
   * Services, for example due to connection timeouts. - refused-stream: Router
   * will retry if the backend service resets the stream with a REFUSED_STREAM
   * error code. This reset type indicates that it is safe to retry. -
   * cancelled: Router will retry if the gRPC status code in the response header
   * is set to cancelled - deadline-exceeded: Router will retry if the gRPC
   * status code in the response header is set to deadline-exceeded - resource-
   * exhausted: Router will retry if the gRPC status code in the response header
   * is set to resource-exhausted - unavailable: Router will retry if the gRPC
   * status code in the response header is set to unavailable
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
class_alias(GrpcRouteRetryPolicy::class, 'Google_Service_NetworkServices_GrpcRouteRetryPolicy');
