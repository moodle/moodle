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

namespace Google\Service\Dfareporting;

class CustomViewabilityMetricConfiguration extends \Google\Model
{
  /**
   * Whether the video must be audible to count an impression.
   *
   * @var bool
   */
  public $audible;
  /**
   * The time in milliseconds the video must play for the Custom Viewability
   * Metric to count an impression. If both this and timePercent are specified,
   * the earlier of the two will be used.
   *
   * @var int
   */
  public $timeMillis;
  /**
   * The percentage of video that must play for the Custom Viewability Metric to
   * count an impression. If both this and timeMillis are specified, the earlier
   * of the two will be used.
   *
   * @var int
   */
  public $timePercent;
  /**
   * The percentage of video that must be on screen for the Custom Viewability
   * Metric to count an impression.
   *
   * @var int
   */
  public $viewabilityPercent;

  /**
   * Whether the video must be audible to count an impression.
   *
   * @param bool $audible
   */
  public function setAudible($audible)
  {
    $this->audible = $audible;
  }
  /**
   * @return bool
   */
  public function getAudible()
  {
    return $this->audible;
  }
  /**
   * The time in milliseconds the video must play for the Custom Viewability
   * Metric to count an impression. If both this and timePercent are specified,
   * the earlier of the two will be used.
   *
   * @param int $timeMillis
   */
  public function setTimeMillis($timeMillis)
  {
    $this->timeMillis = $timeMillis;
  }
  /**
   * @return int
   */
  public function getTimeMillis()
  {
    return $this->timeMillis;
  }
  /**
   * The percentage of video that must play for the Custom Viewability Metric to
   * count an impression. If both this and timeMillis are specified, the earlier
   * of the two will be used.
   *
   * @param int $timePercent
   */
  public function setTimePercent($timePercent)
  {
    $this->timePercent = $timePercent;
  }
  /**
   * @return int
   */
  public function getTimePercent()
  {
    return $this->timePercent;
  }
  /**
   * The percentage of video that must be on screen for the Custom Viewability
   * Metric to count an impression.
   *
   * @param int $viewabilityPercent
   */
  public function setViewabilityPercent($viewabilityPercent)
  {
    $this->viewabilityPercent = $viewabilityPercent;
  }
  /**
   * @return int
   */
  public function getViewabilityPercent()
  {
    return $this->viewabilityPercent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomViewabilityMetricConfiguration::class, 'Google_Service_Dfareporting_CustomViewabilityMetricConfiguration');
