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

namespace Google\Service\Backupdr;

class Trial extends \Google\Model
{
  /**
   * End reason not set.
   */
  public const END_REASON_END_REASON_UNSPECIFIED = 'END_REASON_UNSPECIFIED';
  /**
   * Trial is deliberately ended by the user to transition to paid usage.
   */
  public const END_REASON_MOVE_TO_PAID = 'MOVE_TO_PAID';
  /**
   * Trial is discontinued before expiration.
   */
  public const END_REASON_DISCONTINUED = 'DISCONTINUED';
  /**
   * State not set.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Trial is subscribed.
   */
  public const STATE_SUBSCRIBED = 'SUBSCRIBED';
  /**
   * Trial is unsubscribed before expiration.
   */
  public const STATE_UNSUBSCRIBED = 'UNSUBSCRIBED';
  /**
   * Trial is expired post 30 days of subscription.
   */
  public const STATE_EXPIRED = 'EXPIRED';
  /**
   * Trial is eligible for enablement.
   */
  public const STATE_ELIGIBLE = 'ELIGIBLE';
  /**
   * Trial is not eligible for enablement.
   */
  public const STATE_NOT_ELIGIBLE = 'NOT_ELIGIBLE';
  /**
   * Output only. The reason for ending the trial.
   *
   * @var string
   */
  public $endReason;
  /**
   * Output only. The time when the trial will expire.
   *
   * @var string
   */
  public $endTime;
  /**
   * Identifier. The resource name of the trial. Format:
   * projects/{project}/locations/{location}/trial
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The time when the trial was subscribed.
   *
   * @var string
   */
  public $startTime;
  /**
   * Output only. The state of the trial.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. The reason for ending the trial.
   *
   * Accepted values: END_REASON_UNSPECIFIED, MOVE_TO_PAID, DISCONTINUED
   *
   * @param self::END_REASON_* $endReason
   */
  public function setEndReason($endReason)
  {
    $this->endReason = $endReason;
  }
  /**
   * @return self::END_REASON_*
   */
  public function getEndReason()
  {
    return $this->endReason;
  }
  /**
   * Output only. The time when the trial will expire.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Identifier. The resource name of the trial. Format:
   * projects/{project}/locations/{location}/trial
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. The time when the trial was subscribed.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * Output only. The state of the trial.
   *
   * Accepted values: STATE_UNSPECIFIED, SUBSCRIBED, UNSUBSCRIBED, EXPIRED,
   * ELIGIBLE, NOT_ELIGIBLE
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
class_alias(Trial::class, 'Google_Service_Backupdr_Trial');
