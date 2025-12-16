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

namespace Google\Service\ChromeManagement;

class GoogleChromeManagementV1CountChromeCrashEventsResponseCrashEventCount extends \Google\Model
{
  /**
   * Browser version this is counting.
   *
   * @var string
   */
  public $browserVersion;
  /**
   * Total count of crash events.
   *
   * @var string
   */
  public $count;
  protected $dateType = GoogleTypeDate::class;
  protected $dateDataType = '';

  /**
   * Browser version this is counting.
   *
   * @param string $browserVersion
   */
  public function setBrowserVersion($browserVersion)
  {
    $this->browserVersion = $browserVersion;
  }
  /**
   * @return string
   */
  public function getBrowserVersion()
  {
    return $this->browserVersion;
  }
  /**
   * Total count of crash events.
   *
   * @param string $count
   */
  public function setCount($count)
  {
    $this->count = $count;
  }
  /**
   * @return string
   */
  public function getCount()
  {
    return $this->count;
  }
  /**
   * Date of the crash event.
   *
   * @param GoogleTypeDate $date
   */
  public function setDate(GoogleTypeDate $date)
  {
    $this->date = $date;
  }
  /**
   * @return GoogleTypeDate
   */
  public function getDate()
  {
    return $this->date;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1CountChromeCrashEventsResponseCrashEventCount::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1CountChromeCrashEventsResponseCrashEventCount');
