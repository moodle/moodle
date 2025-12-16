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

namespace Google\Service\CloudDeploy;

class Retry extends \Google\Model
{
  /**
   * No WaitMode is specified.
   */
  public const BACKOFF_MODE_BACKOFF_MODE_UNSPECIFIED = 'BACKOFF_MODE_UNSPECIFIED';
  /**
   * Increases the wait time linearly.
   */
  public const BACKOFF_MODE_BACKOFF_MODE_LINEAR = 'BACKOFF_MODE_LINEAR';
  /**
   * Increases the wait time exponentially.
   */
  public const BACKOFF_MODE_BACKOFF_MODE_EXPONENTIAL = 'BACKOFF_MODE_EXPONENTIAL';
  /**
   * Required. Total number of retries. Retry is skipped if set to 0; The
   * minimum value is 1, and the maximum value is 10.
   *
   * @var string
   */
  public $attempts;
  /**
   * Optional. The pattern of how wait time will be increased. Default is
   * linear. Backoff mode will be ignored if `wait` is 0.
   *
   * @var string
   */
  public $backoffMode;
  /**
   * Optional. How long to wait for the first retry. Default is 0, and the
   * maximum value is 14d.
   *
   * @var string
   */
  public $wait;

  /**
   * Required. Total number of retries. Retry is skipped if set to 0; The
   * minimum value is 1, and the maximum value is 10.
   *
   * @param string $attempts
   */
  public function setAttempts($attempts)
  {
    $this->attempts = $attempts;
  }
  /**
   * @return string
   */
  public function getAttempts()
  {
    return $this->attempts;
  }
  /**
   * Optional. The pattern of how wait time will be increased. Default is
   * linear. Backoff mode will be ignored if `wait` is 0.
   *
   * Accepted values: BACKOFF_MODE_UNSPECIFIED, BACKOFF_MODE_LINEAR,
   * BACKOFF_MODE_EXPONENTIAL
   *
   * @param self::BACKOFF_MODE_* $backoffMode
   */
  public function setBackoffMode($backoffMode)
  {
    $this->backoffMode = $backoffMode;
  }
  /**
   * @return self::BACKOFF_MODE_*
   */
  public function getBackoffMode()
  {
    return $this->backoffMode;
  }
  /**
   * Optional. How long to wait for the first retry. Default is 0, and the
   * maximum value is 14d.
   *
   * @param string $wait
   */
  public function setWait($wait)
  {
    $this->wait = $wait;
  }
  /**
   * @return string
   */
  public function getWait()
  {
    return $this->wait;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Retry::class, 'Google_Service_CloudDeploy_Retry');
