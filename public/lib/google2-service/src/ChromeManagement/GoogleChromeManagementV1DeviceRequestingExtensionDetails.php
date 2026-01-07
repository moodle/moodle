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

class GoogleChromeManagementV1DeviceRequestingExtensionDetails extends \Google\Model
{
  /**
   * The name of a device that has requested the extension.
   *
   * @var string
   */
  public $deviceName;
  /**
   * Request justification as entered by the user.
   *
   * @var string
   */
  public $justification;

  /**
   * The name of a device that has requested the extension.
   *
   * @param string $deviceName
   */
  public function setDeviceName($deviceName)
  {
    $this->deviceName = $deviceName;
  }
  /**
   * @return string
   */
  public function getDeviceName()
  {
    return $this->deviceName;
  }
  /**
   * Request justification as entered by the user.
   *
   * @param string $justification
   */
  public function setJustification($justification)
  {
    $this->justification = $justification;
  }
  /**
   * @return string
   */
  public function getJustification()
  {
    return $this->justification;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1DeviceRequestingExtensionDetails::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1DeviceRequestingExtensionDetails');
