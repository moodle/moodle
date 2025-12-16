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

class CustomErrorResponsePolicyCustomErrorResponseRule extends \Google\Collection
{
  protected $collection_key = 'matchResponseCodes';
  /**
   * Valid values include:              - A number between 400 and 599: For
   * example      401 or 503, in which case the load balancer      applies the
   * policy if the error code exactly matches this value.      - 5xx: Load
   * Balancer will apply the policy if the      backend service responds with
   * any response code in the range of      500 to 599.     - 4xx: Load
   * Balancer will apply the policy if the backend service responds with any
   * response code in the range of 400 to      499.
   *
   * Values must be unique within matchResponseCodes and across
   * allerrorResponseRules ofCustomErrorResponsePolicy.
   *
   * @var string[]
   */
  public $matchResponseCodes;
  /**
   * The HTTP status code returned with the response containing the custom error
   * content. If overrideResponseCode is not supplied, the same response code
   * returned by the original backend bucket or backend service is returned to
   * the client.
   *
   * @var int
   */
  public $overrideResponseCode;
  /**
   * The full path to a file within backendBucket . For
   * example:/errors/defaultError.html
   *
   * path must start with a leading slash. path cannot have trailing slashes.
   *
   * If the file is not available in backendBucket  or the load balancer cannot
   * reach the BackendBucket, a simpleNot Found Error is returned to the client.
   *
   * The value must be from 1 to 1024 characters
   *
   * @var string
   */
  public $path;

  /**
   * Valid values include:              - A number between 400 and 599: For
   * example      401 or 503, in which case the load balancer      applies the
   * policy if the error code exactly matches this value.      - 5xx: Load
   * Balancer will apply the policy if the      backend service responds with
   * any response code in the range of      500 to 599.     - 4xx: Load
   * Balancer will apply the policy if the backend service responds with any
   * response code in the range of 400 to      499.
   *
   * Values must be unique within matchResponseCodes and across
   * allerrorResponseRules ofCustomErrorResponsePolicy.
   *
   * @param string[] $matchResponseCodes
   */
  public function setMatchResponseCodes($matchResponseCodes)
  {
    $this->matchResponseCodes = $matchResponseCodes;
  }
  /**
   * @return string[]
   */
  public function getMatchResponseCodes()
  {
    return $this->matchResponseCodes;
  }
  /**
   * The HTTP status code returned with the response containing the custom error
   * content. If overrideResponseCode is not supplied, the same response code
   * returned by the original backend bucket or backend service is returned to
   * the client.
   *
   * @param int $overrideResponseCode
   */
  public function setOverrideResponseCode($overrideResponseCode)
  {
    $this->overrideResponseCode = $overrideResponseCode;
  }
  /**
   * @return int
   */
  public function getOverrideResponseCode()
  {
    return $this->overrideResponseCode;
  }
  /**
   * The full path to a file within backendBucket . For
   * example:/errors/defaultError.html
   *
   * path must start with a leading slash. path cannot have trailing slashes.
   *
   * If the file is not available in backendBucket  or the load balancer cannot
   * reach the BackendBucket, a simpleNot Found Error is returned to the client.
   *
   * The value must be from 1 to 1024 characters
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomErrorResponsePolicyCustomErrorResponseRule::class, 'Google_Service_Compute_CustomErrorResponsePolicyCustomErrorResponseRule');
