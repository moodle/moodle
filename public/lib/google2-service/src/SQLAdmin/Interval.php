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

namespace Google\Service\SQLAdmin;

class Interval extends \Google\Model
{
  /**
   * Optional. Exclusive end of the interval. If specified, a Timestamp matching
   * this interval will have to be before the end.
   *
   * @var string
   */
  public $endTime;
  /**
   * Optional. Inclusive start of the interval. If specified, a Timestamp
   * matching this interval will have to be the same or after the start.
   *
   * @var string
   */
  public $startTime;

  /**
   * Optional. Exclusive end of the interval. If specified, a Timestamp matching
   * this interval will have to be before the end.
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
   * Optional. Inclusive start of the interval. If specified, a Timestamp
   * matching this interval will have to be the same or after the start.
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
class_alias(Interval::class, 'Google_Service_SQLAdmin_Interval');
