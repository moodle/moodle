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

class GoogleChromeManagementV1TouchScreenInfo extends \Google\Collection
{
  protected $collection_key = 'devices';
  protected $devicesType = GoogleChromeManagementV1TouchScreenDevice::class;
  protected $devicesDataType = 'array';
  /**
   * Output only. Touchpad library name used by the input stack.
   *
   * @var string
   */
  public $touchpadLibrary;

  /**
   * Output only. List of the internal touch screen devices.
   *
   * @param GoogleChromeManagementV1TouchScreenDevice[] $devices
   */
  public function setDevices($devices)
  {
    $this->devices = $devices;
  }
  /**
   * @return GoogleChromeManagementV1TouchScreenDevice[]
   */
  public function getDevices()
  {
    return $this->devices;
  }
  /**
   * Output only. Touchpad library name used by the input stack.
   *
   * @param string $touchpadLibrary
   */
  public function setTouchpadLibrary($touchpadLibrary)
  {
    $this->touchpadLibrary = $touchpadLibrary;
  }
  /**
   * @return string
   */
  public function getTouchpadLibrary()
  {
    return $this->touchpadLibrary;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1TouchScreenInfo::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1TouchScreenInfo');
