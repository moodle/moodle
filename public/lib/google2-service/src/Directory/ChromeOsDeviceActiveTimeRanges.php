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

namespace Google\Service\Directory;

class ChromeOsDeviceActiveTimeRanges extends \Google\Model
{
  /**
   * Duration of usage in milliseconds.
   *
   * @var int
   */
  public $activeTime;
  /**
   * Date of usage
   *
   * @var string
   */
  public $date;

  /**
   * Duration of usage in milliseconds.
   *
   * @param int $activeTime
   */
  public function setActiveTime($activeTime)
  {
    $this->activeTime = $activeTime;
  }
  /**
   * @return int
   */
  public function getActiveTime()
  {
    return $this->activeTime;
  }
  /**
   * Date of usage
   *
   * @param string $date
   */
  public function setDate($date)
  {
    $this->date = $date;
  }
  /**
   * @return string
   */
  public function getDate()
  {
    return $this->date;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ChromeOsDeviceActiveTimeRanges::class, 'Google_Service_Directory_ChromeOsDeviceActiveTimeRanges');
