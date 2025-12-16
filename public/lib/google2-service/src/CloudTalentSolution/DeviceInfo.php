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

namespace Google\Service\CloudTalentSolution;

class DeviceInfo extends \Google\Model
{
  /**
   * The device type isn't specified.
   */
  public const DEVICE_TYPE_DEVICE_TYPE_UNSPECIFIED = 'DEVICE_TYPE_UNSPECIFIED';
  /**
   * A desktop web browser, such as, Chrome, Firefox, Safari, or Internet
   * Explorer)
   */
  public const DEVICE_TYPE_WEB = 'WEB';
  /**
   * A mobile device web browser, such as a phone or tablet with a Chrome
   * browser.
   */
  public const DEVICE_TYPE_MOBILE_WEB = 'MOBILE_WEB';
  /**
   * An Android device native application.
   */
  public const DEVICE_TYPE_ANDROID = 'ANDROID';
  /**
   * An iOS device native application.
   */
  public const DEVICE_TYPE_IOS = 'IOS';
  /**
   * A bot, as opposed to a device operated by human beings, such as a web
   * crawler.
   */
  public const DEVICE_TYPE_BOT = 'BOT';
  /**
   * Other devices types.
   */
  public const DEVICE_TYPE_OTHER = 'OTHER';
  /**
   * Type of the device.
   *
   * @var string
   */
  public $deviceType;
  /**
   * A device-specific ID. The ID must be a unique identifier that distinguishes
   * the device from other devices.
   *
   * @var string
   */
  public $id;

  /**
   * Type of the device.
   *
   * Accepted values: DEVICE_TYPE_UNSPECIFIED, WEB, MOBILE_WEB, ANDROID, IOS,
   * BOT, OTHER
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
   * A device-specific ID. The ID must be a unique identifier that distinguishes
   * the device from other devices.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeviceInfo::class, 'Google_Service_CloudTalentSolution_DeviceInfo');
