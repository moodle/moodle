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

class HttpFaultAbort extends \Google\Model
{
  /**
   * The HTTP status code used to abort the request.
   *
   * The value must be from 200 to 599 inclusive.
   *
   * For gRPC protocol, the gRPC status code is mapped to HTTP status code
   * according to this  mapping table. HTTP status 200 is mapped to gRPC status
   * UNKNOWN. Injecting an OK status is currently not supported by Traffic
   * Director.
   *
   * @var string
   */
  public $httpStatus;
  /**
   * The percentage of traffic for connections, operations, or requests that is
   * aborted as part of fault injection.
   *
   * The value must be from 0.0 to 100.0 inclusive.
   *
   * @var 
   */
  public $percentage;

  /**
   * The HTTP status code used to abort the request.
   *
   * The value must be from 200 to 599 inclusive.
   *
   * For gRPC protocol, the gRPC status code is mapped to HTTP status code
   * according to this  mapping table. HTTP status 200 is mapped to gRPC status
   * UNKNOWN. Injecting an OK status is currently not supported by Traffic
   * Director.
   *
   * @param string $httpStatus
   */
  public function setHttpStatus($httpStatus)
  {
    $this->httpStatus = $httpStatus;
  }
  /**
   * @return string
   */
  public function getHttpStatus()
  {
    return $this->httpStatus;
  }
  public function setPercentage($percentage)
  {
    $this->percentage = $percentage;
  }
  public function getPercentage()
  {
    return $this->percentage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HttpFaultAbort::class, 'Google_Service_Compute_HttpFaultAbort');
