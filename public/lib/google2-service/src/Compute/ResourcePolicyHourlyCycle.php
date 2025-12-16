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

class ResourcePolicyHourlyCycle extends \Google\Model
{
  /**
   * Output only. [Output only] Duration of the time window, automatically
   * chosen to be smallest possible in the given scenario.
   *
   * @var string
   */
  public $duration;
  /**
   * Defines a schedule with units measured in hours. The value determines how
   * many hours pass between the start of each cycle.
   *
   * @var int
   */
  public $hoursInCycle;
  /**
   * Time within the window to start the operations. It must be in format
   * "HH:MM", where HH : [00-23] and MM : [00-00] GMT.
   *
   * @var string
   */
  public $startTime;

  /**
   * Output only. [Output only] Duration of the time window, automatically
   * chosen to be smallest possible in the given scenario.
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
   * Defines a schedule with units measured in hours. The value determines how
   * many hours pass between the start of each cycle.
   *
   * @param int $hoursInCycle
   */
  public function setHoursInCycle($hoursInCycle)
  {
    $this->hoursInCycle = $hoursInCycle;
  }
  /**
   * @return int
   */
  public function getHoursInCycle()
  {
    return $this->hoursInCycle;
  }
  /**
   * Time within the window to start the operations. It must be in format
   * "HH:MM", where HH : [00-23] and MM : [00-00] GMT.
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
class_alias(ResourcePolicyHourlyCycle::class, 'Google_Service_Compute_ResourcePolicyHourlyCycle');
