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

class GoogleChromeManagementV1Device extends \Google\Model
{
  /**
   * Output only. The ID of the device that reported this Chrome browser
   * information.
   *
   * @var string
   */
  public $deviceId;
  /**
   * Output only. The name of the machine within its local network.
   *
   * @var string
   */
  public $machine;

  /**
   * Output only. The ID of the device that reported this Chrome browser
   * information.
   *
   * @param string $deviceId
   */
  public function setDeviceId($deviceId)
  {
    $this->deviceId = $deviceId;
  }
  /**
   * @return string
   */
  public function getDeviceId()
  {
    return $this->deviceId;
  }
  /**
   * Output only. The name of the machine within its local network.
   *
   * @param string $machine
   */
  public function setMachine($machine)
  {
    $this->machine = $machine;
  }
  /**
   * @return string
   */
  public function getMachine()
  {
    return $this->machine;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1Device::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1Device');
