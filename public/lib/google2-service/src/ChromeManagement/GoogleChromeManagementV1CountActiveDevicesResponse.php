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

class GoogleChromeManagementV1CountActiveDevicesResponse extends \Google\Model
{
  /**
   * Number of active devices in the 7 days leading up to the date specified in
   * the request.
   *
   * @var string
   */
  public $sevenDaysCount;
  /**
   * Number of active devices in the 30 days leading up to the date specified in
   * the request.
   *
   * @var string
   */
  public $thirtyDaysCount;

  /**
   * Number of active devices in the 7 days leading up to the date specified in
   * the request.
   *
   * @param string $sevenDaysCount
   */
  public function setSevenDaysCount($sevenDaysCount)
  {
    $this->sevenDaysCount = $sevenDaysCount;
  }
  /**
   * @return string
   */
  public function getSevenDaysCount()
  {
    return $this->sevenDaysCount;
  }
  /**
   * Number of active devices in the 30 days leading up to the date specified in
   * the request.
   *
   * @param string $thirtyDaysCount
   */
  public function setThirtyDaysCount($thirtyDaysCount)
  {
    $this->thirtyDaysCount = $thirtyDaysCount;
  }
  /**
   * @return string
   */
  public function getThirtyDaysCount()
  {
    return $this->thirtyDaysCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1CountActiveDevicesResponse::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1CountActiveDevicesResponse');
