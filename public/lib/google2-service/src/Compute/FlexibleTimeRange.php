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

class FlexibleTimeRange extends \Google\Model
{
  /**
   * @var string
   */
  public $maxDuration;
  /**
   * @var string
   */
  public $minDuration;
  /**
   * @var string
   */
  public $startTimeNotEarlierThan;
  /**
   * @var string
   */
  public $startTimeNotLaterThan;

  /**
   * @param string $maxDuration
   */
  public function setMaxDuration($maxDuration)
  {
    $this->maxDuration = $maxDuration;
  }
  /**
   * @return string
   */
  public function getMaxDuration()
  {
    return $this->maxDuration;
  }
  /**
   * @param string $minDuration
   */
  public function setMinDuration($minDuration)
  {
    $this->minDuration = $minDuration;
  }
  /**
   * @return string
   */
  public function getMinDuration()
  {
    return $this->minDuration;
  }
  /**
   * @param string $startTimeNotEarlierThan
   */
  public function setStartTimeNotEarlierThan($startTimeNotEarlierThan)
  {
    $this->startTimeNotEarlierThan = $startTimeNotEarlierThan;
  }
  /**
   * @return string
   */
  public function getStartTimeNotEarlierThan()
  {
    return $this->startTimeNotEarlierThan;
  }
  /**
   * @param string $startTimeNotLaterThan
   */
  public function setStartTimeNotLaterThan($startTimeNotLaterThan)
  {
    $this->startTimeNotLaterThan = $startTimeNotLaterThan;
  }
  /**
   * @return string
   */
  public function getStartTimeNotLaterThan()
  {
    return $this->startTimeNotLaterThan;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FlexibleTimeRange::class, 'Google_Service_Compute_FlexibleTimeRange');
