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

namespace Google\Service\CloudSearch;

class EnterpriseTopazSidekickGap extends \Google\Model
{
  /**
   * Localized time string in the format: 1 hour 15 minutes
   *
   * @var string
   */
  public $displayRemainingTime;
  /**
   * Localized time string in the format:(Locale CZ) 8:30 odp.
   *
   * @var string
   */
  public $endTime;
  /**
   * @var string
   */
  public $endTimeMs;
  /**
   * @var string
   */
  public $remainingTime;
  /**
   * Localized time string in the format:(Locale CZ) 8:30 odp.
   *
   * @var string
   */
  public $startTime;
  /**
   * @var string
   */
  public $startTimeMs;

  /**
   * Localized time string in the format: 1 hour 15 minutes
   *
   * @param string $displayRemainingTime
   */
  public function setDisplayRemainingTime($displayRemainingTime)
  {
    $this->displayRemainingTime = $displayRemainingTime;
  }
  /**
   * @return string
   */
  public function getDisplayRemainingTime()
  {
    return $this->displayRemainingTime;
  }
  /**
   * Localized time string in the format:(Locale CZ) 8:30 odp.
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
   * @param string $endTimeMs
   */
  public function setEndTimeMs($endTimeMs)
  {
    $this->endTimeMs = $endTimeMs;
  }
  /**
   * @return string
   */
  public function getEndTimeMs()
  {
    return $this->endTimeMs;
  }
  /**
   * @param string $remainingTime
   */
  public function setRemainingTime($remainingTime)
  {
    $this->remainingTime = $remainingTime;
  }
  /**
   * @return string
   */
  public function getRemainingTime()
  {
    return $this->remainingTime;
  }
  /**
   * Localized time string in the format:(Locale CZ) 8:30 odp.
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
   * @param string $startTimeMs
   */
  public function setStartTimeMs($startTimeMs)
  {
    $this->startTimeMs = $startTimeMs;
  }
  /**
   * @return string
   */
  public function getStartTimeMs()
  {
    return $this->startTimeMs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseTopazSidekickGap::class, 'Google_Service_CloudSearch_EnterpriseTopazSidekickGap');
