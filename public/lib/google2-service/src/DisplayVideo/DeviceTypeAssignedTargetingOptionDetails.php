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

class DeviceTypeAssignedTargetingOptionDetails extends \Google\Model
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
   * Required. The display name of the device type.
   *
   * @var string
   */
  public $deviceType;
  /**
   * Output only. Bid multiplier allows you to show your ads more or less
   * frequently based on the device type. It will apply a multiplier on the
   * original bid price. When this field is 0, it indicates this field is not
   * applicable instead of multiplying 0 on the original bid price. For example,
   * if the bid price without multiplier is $10.0 and the multiplier is 1.5 for
   * Tablet, the resulting bid price for Tablet will be $15.0. Only applicable
   * to YouTube and Partners line items.
   *
   * @var 
   */
  public $youtubeAndPartnersBidMultiplier;

  /**
   * Required. The display name of the device type.
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
  public function setYoutubeAndPartnersBidMultiplier($youtubeAndPartnersBidMultiplier)
  {
    $this->youtubeAndPartnersBidMultiplier = $youtubeAndPartnersBidMultiplier;
  }
  public function getYoutubeAndPartnersBidMultiplier()
  {
    return $this->youtubeAndPartnersBidMultiplier;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeviceTypeAssignedTargetingOptionDetails::class, 'Google_Service_DisplayVideo_DeviceTypeAssignedTargetingOptionDetails');
