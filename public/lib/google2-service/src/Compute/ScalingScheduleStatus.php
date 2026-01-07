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

class ScalingScheduleStatus extends \Google\Model
{
  /**
   * The current autoscaling recommendation is influenced by this scaling
   * schedule.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * This scaling schedule has been disabled by the user.
   */
  public const STATE_DISABLED = 'DISABLED';
  /**
   * This scaling schedule will never become active again.
   */
  public const STATE_OBSOLETE = 'OBSOLETE';
  /**
   * The current autoscaling recommendation is not influenced by this scaling
   * schedule.
   */
  public const STATE_READY = 'READY';
  /**
   * [Output Only] The last time the scaling schedule became active. Note: this
   * is a timestamp when a schedule actually became active, not when it was
   * planned to do so. The timestamp is in RFC3339 text format.
   *
   * @var string
   */
  public $lastStartTime;
  /**
   * [Output Only] The next time the scaling schedule is to become active. Note:
   * this is a timestamp when a schedule is planned to run, but the actual time
   * might be slightly different. The timestamp is in RFC3339 text format.
   *
   * @var string
   */
  public $nextStartTime;
  /**
   * [Output Only] The current state of a scaling schedule.
   *
   * @var string
   */
  public $state;

  /**
   * [Output Only] The last time the scaling schedule became active. Note: this
   * is a timestamp when a schedule actually became active, not when it was
   * planned to do so. The timestamp is in RFC3339 text format.
   *
   * @param string $lastStartTime
   */
  public function setLastStartTime($lastStartTime)
  {
    $this->lastStartTime = $lastStartTime;
  }
  /**
   * @return string
   */
  public function getLastStartTime()
  {
    return $this->lastStartTime;
  }
  /**
   * [Output Only] The next time the scaling schedule is to become active. Note:
   * this is a timestamp when a schedule is planned to run, but the actual time
   * might be slightly different. The timestamp is in RFC3339 text format.
   *
   * @param string $nextStartTime
   */
  public function setNextStartTime($nextStartTime)
  {
    $this->nextStartTime = $nextStartTime;
  }
  /**
   * @return string
   */
  public function getNextStartTime()
  {
    return $this->nextStartTime;
  }
  /**
   * [Output Only] The current state of a scaling schedule.
   *
   * Accepted values: ACTIVE, DISABLED, OBSOLETE, READY
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
class_alias(ScalingScheduleStatus::class, 'Google_Service_Compute_ScalingScheduleStatus');
