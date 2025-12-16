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

namespace Google\Service\AndroidManagement;

class RequestDeviceInfoParams extends \Google\Model
{
  /**
   * This value is disallowed.
   */
  public const DEVICE_INFO_DEVICE_INFO_UNSPECIFIED = 'DEVICE_INFO_UNSPECIFIED';
  /**
   * Request the identifier for eSIM. The user will be asked to approve the
   * disclosure of the information before the result can be returned. If the
   * user doesn't approve the disclosure, USER_DECLINED will be returned. This
   * is supported only for personally owned devices with work profiles and
   * Android versions 13 and above.
   */
  public const DEVICE_INFO_EID = 'EID';
  /**
   * Required. Type of device information to be requested.
   *
   * @var string
   */
  public $deviceInfo;

  /**
   * Required. Type of device information to be requested.
   *
   * Accepted values: DEVICE_INFO_UNSPECIFIED, EID
   *
   * @param self::DEVICE_INFO_* $deviceInfo
   */
  public function setDeviceInfo($deviceInfo)
  {
    $this->deviceInfo = $deviceInfo;
  }
  /**
   * @return self::DEVICE_INFO_*
   */
  public function getDeviceInfo()
  {
    return $this->deviceInfo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RequestDeviceInfoParams::class, 'Google_Service_AndroidManagement_RequestDeviceInfoParams');
