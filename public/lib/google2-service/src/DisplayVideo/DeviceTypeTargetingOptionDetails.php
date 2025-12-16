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

namespace Google\Service\DisplayVideo;

class DeviceTypeTargetingOptionDetails extends \Google\Model
{
  /**
   * Default value when device type is not specified in this version. This enum
   * is a placeholder for default value and does not represent a real device
   * type option.
   */
  public const DEVICE_TYPE_DEVICE_TYPE_UNSPECIFIED = 'DEVICE_TYPE_UNSPECIFIED';
  /**
   * Computer.
   */
  public const DEVICE_TYPE_DEVICE_TYPE_COMPUTER = 'DEVICE_TYPE_COMPUTER';
  /**
   * Connected TV.
   */
  public const DEVICE_TYPE_DEVICE_TYPE_CONNECTED_TV = 'DEVICE_TYPE_CONNECTED_TV';
  /**
   * Smart phone.
   */
  public const DEVICE_TYPE_DEVICE_TYPE_SMART_PHONE = 'DEVICE_TYPE_SMART_PHONE';
  /**
   * Tablet.
   */
  public const DEVICE_TYPE_DEVICE_TYPE_TABLET = 'DEVICE_TYPE_TABLET';
  /**
   * Connected device.
   */
  public const DEVICE_TYPE_DEVICE_TYPE_CONNECTED_DEVICE = 'DEVICE_TYPE_CONNECTED_DEVICE';
  /**
   * Output only. The device type that is used to be targeted.
   *
   * @var string
   */
  public $deviceType;

  /**
   * Output only. The device type that is used to be targeted.
   *
   * Accepted values: DEVICE_TYPE_UNSPECIFIED, DEVICE_TYPE_COMPUTER,
   * DEVICE_TYPE_CONNECTED_TV, DEVICE_TYPE_SMART_PHONE, DEVICE_TYPE_TABLET,
   * DEVICE_TYPE_CONNECTED_DEVICE
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeviceTypeTargetingOptionDetails::class, 'Google_Service_DisplayVideo_DeviceTypeTargetingOptionDetails');
