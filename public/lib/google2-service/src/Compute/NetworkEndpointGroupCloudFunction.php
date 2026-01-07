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

class NetworkEndpointGroupCloudFunction extends \Google\Model
{
  /**
   * A user-defined name of the Cloud Function.
   *
   * The function name is case-sensitive and must be 1-63 characters long.
   *
   * Example value: func1.
   *
   * @var string
   */
  public $function;
  /**
   * An URL mask is one of the main components of the Cloud Function.
   *
   * A template to parse function field from a request URL. URL mask allows for
   * routing to multiple Cloud Functions without having to create multiple
   * Network Endpoint Groups and backend services.
   *
   * For example, request URLs mydomain.com/function1 andmydomain.com/function2
   * can be backed by the same Serverless NEG with URL mask /. The URL mask will
   * parse them to { function = "function1" } and{ function = "function2" }
   * respectively.
   *
   * @var string
   */
  public $urlMask;

  /**
   * A user-defined name of the Cloud Function.
   *
   * The function name is case-sensitive and must be 1-63 characters long.
   *
   * Example value: func1.
   *
   * @param string $function
   */
  public function setFunction($function)
  {
    $this->function = $function;
  }
  /**
   * @return string
   */
  public function getFunction()
  {
    return $this->function;
  }
  /**
   * An URL mask is one of the main components of the Cloud Function.
   *
   * A template to parse function field from a request URL. URL mask allows for
   * routing to multiple Cloud Functions without having to create multiple
   * Network Endpoint Groups and backend services.
   *
   * For example, request URLs mydomain.com/function1 andmydomain.com/function2
   * can be backed by the same Serverless NEG with URL mask /. The URL mask will
   * parse them to { function = "function1" } and{ function = "function2" }
   * respectively.
   *
   * @param string $urlMask
   */
  public function setUrlMask($urlMask)
  {
    $this->urlMask = $urlMask;
  }
  /**
   * @return string
   */
  public function getUrlMask()
  {
    return $this->urlMask;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NetworkEndpointGroupCloudFunction::class, 'Google_Service_Compute_NetworkEndpointGroupCloudFunction');
