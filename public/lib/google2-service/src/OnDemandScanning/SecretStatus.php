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

namespace Google\Service\OnDemandScanning;

class SecretStatus extends \Google\Model
{
  /**
   * Unspecified
   */
  public const STATUS_STATUS_UNSPECIFIED = 'STATUS_UNSPECIFIED';
  /**
   * The status of the secret is unknown.
   */
  public const STATUS_UNKNOWN = 'UNKNOWN';
  /**
   * The secret is valid.
   */
  public const STATUS_VALID = 'VALID';
  /**
   * The secret is invalid.
   */
  public const STATUS_INVALID = 'INVALID';
  /**
   * Optional. Optional message about the status code.
   *
   * @var string
   */
  public $message;
  /**
   * Optional. The status of the secret.
   *
   * @var string
   */
  public $status;
  /**
   * Optional. The time the secret status was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. Optional message about the status code.
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
   * Optional. The status of the secret.
   *
   * Accepted values: STATUS_UNSPECIFIED, UNKNOWN, VALID, INVALID
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Optional. The time the secret status was last updated.
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
class_alias(SecretStatus::class, 'Google_Service_OnDemandScanning_SecretStatus');
