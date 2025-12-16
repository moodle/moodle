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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1AssetSecurityStatus extends \Google\Model
{
  /**
   * State unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Security policy has been successfully applied to the attached resource.
   */
  public const STATE_READY = 'READY';
  /**
   * Security policy is in the process of being applied to the attached
   * resource.
   */
  public const STATE_APPLYING = 'APPLYING';
  /**
   * Security policy could not be applied to the attached resource due to
   * errors.
   */
  public const STATE_ERROR = 'ERROR';
  /**
   * Additional information about the current state.
   *
   * @var string
   */
  public $message;
  /**
   * The current state of the security policy applied to the attached resource.
   *
   * @var string
   */
  public $state;
  /**
   * Last update time of the status.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Additional information about the current state.
   *
   * @param string $message
   */
  public function setMessage($message)
  {
    $this->message = $message;
  }
  /**
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }
  /**
   * The current state of the security policy applied to the attached resource.
   *
   * Accepted values: STATE_UNSPECIFIED, READY, APPLYING, ERROR
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
  /**
   * Last update time of the status.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1AssetSecurityStatus::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1AssetSecurityStatus');
