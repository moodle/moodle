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

class GoogleChromeManagementVersionsV1DeviceInfo extends \Google\Model
{
  /**
   * Represents an unspecified device type.
   */
  public const DEVICE_TYPE_DEVICE_TYPE_UNSPECIFIED = 'DEVICE_TYPE_UNSPECIFIED';
  /**
   * Represents a Chrome browser device.
   */
  public const DEVICE_TYPE_CHROME_BROWSER = 'CHROME_BROWSER';
  /**
   * Output only. Device ID that identifies the affiliated device on which the
   * profile exists. If the device type is CHROME_BROWSER, then this represents
   * a unique Directory API ID of the device that can be used in Admin SDK
   * Browsers API.
   *
   * @var string
   */
  public $affiliatedDeviceId;
  /**
   * Output only. Type of the device on which the profile exists.
   *
   * @var string
   */
  public $deviceType;
  /**
   * Output only. Hostname of the device on which the profile exists.
   *
   * @var string
   */
  public $hostname;
  /**
   * Output only. Machine name of the device on which the profile exists. On
   * platforms which do not report the machine name (currently iOS and Android)
   * this is instead set to the browser's device_id - but note that this is a
   * different device_id than the |affiliated_device_id|.
   *
   * @var string
   */
  public $machine;

  /**
   * Output only. Device ID that identifies the affiliated device on which the
   * profile exists. If the device type is CHROME_BROWSER, then this represents
   * a unique Directory API ID of the device that can be used in Admin SDK
   * Browsers API.
   *
   * @param string $affiliatedDeviceId
   */
  public function setAffiliatedDeviceId($affiliatedDeviceId)
  {
    $this->affiliatedDeviceId = $affiliatedDeviceId;
  }
  /**
   * @return string
   */
  public function getAffiliatedDeviceId()
  {
    return $this->affiliatedDeviceId;
  }
  /**
   * Output only. Type of the device on which the profile exists.
   *
   * Accepted values: DEVICE_TYPE_UNSPECIFIED, CHROME_BROWSER
   *
   * @param self::DEVICE_TYPE_* $deviceType
   */
  public function setDeviceType($deviceType)
  {
    $this->deviceType = $deviceType;
  }
  /**
   * @return self::DEVICE_TYPE_*
   */
  public function getDeviceType()
  {
    return $this->deviceType;
  }
  /**
   * Output only. Hostname of the device on which the profile exists.
   *
   * @param string $hostname
   */
  public function setHostname($hostname)
  {
    $this->hostname = $hostname;
  }
  /**
   * @return string
   */
  public function getHostname()
  {
    return $this->hostname;
  }
  /**
   * Output only. Machine name of the device on which the profile exists. On
   * platforms which do not report the machine name (currently iOS and Android)
   * this is instead set to the browser's device_id - but note that this is a
   * different device_id than the |affiliated_device_id|.
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
class_alias(GoogleChromeManagementVersionsV1DeviceInfo::class, 'Google_Service_ChromeManagement_GoogleChromeManagementVersionsV1DeviceInfo');
