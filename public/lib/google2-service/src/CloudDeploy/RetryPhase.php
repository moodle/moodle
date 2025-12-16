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

class RetryPhase extends \Google\Collection
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
  protected $collection_key = 'attempts';
  protected $attemptsType = RetryAttempt::class;
  protected $attemptsDataType = 'array';
  /**
   * Output only. The pattern of how the wait time of the retry attempt is
   * calculated.
   *
   * @var string
   */
  public $backoffMode;
  /**
   * Output only. The number of attempts that have been made.
   *
   * @var string
   */
  public $totalAttempts;

  /**
   * Output only. Detail of a retry action.
   *
   * @param RetryAttempt[] $attempts
   */
  public function setAttempts($attempts)
  {
    $this->attempts = $attempts;
  }
  /**
   * @return RetryAttempt[]
   */
  public function getAttempts()
  {
    return $this->attempts;
  }
  /**
   * Output only. The pattern of how the wait time of the retry attempt is
   * calculated.
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
   * Output only. The number of attempts that have been made.
   *
   * @param string $totalAttempts
   */
  public function setTotalAttempts($totalAttempts)
  {
    $this->totalAttempts = $totalAttempts;
  }
  /**
   * @return string
   */
  public function getTotalAttempts()
  {
    return $this->totalAttempts;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RetryPhase::class, 'Google_Service_CloudDeploy_RetryPhase');
