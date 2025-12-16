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

class ResourcePolicyDailyCycle extends \Google\Model
{
  /**
   * Defines a schedule with units measured in days. The value determines how
   * many days pass between the start of each cycle.
   *
   * @var int
   */
  public $daysInCycle;
  /**
   * Output only. [Output only] A predetermined duration for the window,
   * automatically chosen to be the smallest possible in the given scenario.
   *
   * @var string
   */
  public $duration;
  /**
   * Start time of the window. This must be in UTC format that resolves to one
   * of 00:00, 04:00, 08:00,12:00, 16:00, or 20:00. For example, both 13:00-5
   * and 08:00 are valid.
   *
   * @var string
   */
  public $startTime;

  /**
   * Defines a schedule with units measured in days. The value determines how
   * many days pass between the start of each cycle.
   *
   * @param int $daysInCycle
   */
  public function setDaysInCycle($daysInCycle)
  {
    $this->daysInCycle = $daysInCycle;
  }
  /**
   * @return int
   */
  public function getDaysInCycle()
  {
    return $this->daysInCycle;
  }
  /**
   * Output only. [Output only] A predetermined duration for the window,
   * automatically chosen to be the smallest possible in the given scenario.
   *
   * @param string $duration
   */
  public function setDuration($duration)
  {
    $this->duration = $duration;
  }
  /**
   * @return string
   */
  public function getDuration()
  {
    return $this->duration;
  }
  /**
   * Start time of the window. This must be in UTC format that resolves to one
   * of 00:00, 04:00, 08:00,12:00, 16:00, or 20:00. For example, both 13:00-5
   * and 08:00 are valid.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResourcePolicyDailyCycle::class, 'Google_Service_Compute_ResourcePolicyDailyCycle');
