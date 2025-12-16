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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1betaRemoveDedicatedCrawlRateResponse extends \Google\Model
{
  /**
   * The state is unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The state is successful.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The state is failed.
   */
  public const STATE_FAILED = 'FAILED';
  protected $errorType = GoogleRpcStatus::class;
  protected $errorDataType = '';
  /**
   * Output only. The state of the response.
   *
   * @var string
   */
  public $state;

  /**
   * Errors from service when handling the request.
   *
   * @param GoogleRpcStatus $error
   */
  public function setError(GoogleRpcStatus $error)
  {
    $this->error = $error;
  }
  /**
   * @return GoogleRpcStatus
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * Output only. The state of the response.
   *
   * Accepted values: STATE_UNSPECIFIED, SUCCEEDED, FAILED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1betaRemoveDedicatedCrawlRateResponse::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1betaRemoveDedicatedCrawlRateResponse');
