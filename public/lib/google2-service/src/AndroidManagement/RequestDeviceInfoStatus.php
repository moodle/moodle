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

class RequestDeviceInfoStatus extends \Google\Model
{
  /**
   * Unspecified. This value is not used.
   */
  public const STATUS_STATUS_UNSPECIFIED = 'STATUS_UNSPECIFIED';
  /**
   * Device information has been successfully delivered.
   */
  public const STATUS_SUCCEEDED = 'SUCCEEDED';
  /**
   * The user has not completed the actions required to share device
   * information.
   */
  public const STATUS_PENDING_USER_ACTION = 'PENDING_USER_ACTION';
  /**
   * The user declined sharing device information.
   */
  public const STATUS_USER_DECLINED = 'USER_DECLINED';
  /**
   * The requested device info is not supported on this device, e.g. eSIM is not
   * supported on the device.
   */
  public const STATUS_UNSUPPORTED = 'UNSUPPORTED';
  protected $eidInfoType = EidInfo::class;
  protected $eidInfoDataType = '';
  /**
   * Output only. Status of a REQUEST_DEVICE_INFO command.
   *
   * @var string
   */
  public $status;

  /**
   * Information related to the EIDs of the device.
   *
   * @param EidInfo $eidInfo
   */
  public function setEidInfo(EidInfo $eidInfo)
  {
    $this->eidInfo = $eidInfo;
  }
  /**
   * @return EidInfo
   */
  public function getEidInfo()
  {
    return $this->eidInfo;
  }
  /**
   * Output only. Status of a REQUEST_DEVICE_INFO command.
   *
   * Accepted values: STATUS_UNSPECIFIED, SUCCEEDED, PENDING_USER_ACTION,
   * USER_DECLINED, UNSUPPORTED
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RequestDeviceInfoStatus::class, 'Google_Service_AndroidManagement_RequestDeviceInfoStatus');
