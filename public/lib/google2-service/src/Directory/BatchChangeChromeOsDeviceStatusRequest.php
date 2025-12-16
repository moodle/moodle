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

namespace Google\Service\Directory;

class BatchChangeChromeOsDeviceStatusRequest extends \Google\Collection
{
  /**
   * Default value. Value is unused.
   */
  public const CHANGE_CHROME_OS_DEVICE_STATUS_ACTION_CHANGE_CHROME_OS_DEVICE_STATUS_ACTION_UNSPECIFIED = 'CHANGE_CHROME_OS_DEVICE_STATUS_ACTION_UNSPECIFIED';
  /**
   * Deprovisions a ChromeOS device. If you have ChromeOS devices that are no
   * longer being used in your organization, you should deprovision them so that
   * you’re no longer managing them. Deprovisioning the device removes all
   * policies that were on the device as well as device-level printers and the
   * ability to use the device as a kiosk. Depending on the upgrade that’s
   * associated with the device this action might release the license back into
   * the license pool; which allows you to use the license on a different
   * device.
   */
  public const CHANGE_CHROME_OS_DEVICE_STATUS_ACTION_CHANGE_CHROME_OS_DEVICE_STATUS_ACTION_DEPROVISION = 'CHANGE_CHROME_OS_DEVICE_STATUS_ACTION_DEPROVISION';
  /**
   * Disables a ChromeOS device. Use this action if a user loses their device or
   * it’s stolen, this makes it such that the device is still managed, so it
   * will still receive policies, but no one can use it. Depending on the
   * upgrade that’s associated with the device this action might release the
   * license back into the license pool; which allows you to use the license on
   * a different device.
   */
  public const CHANGE_CHROME_OS_DEVICE_STATUS_ACTION_CHANGE_CHROME_OS_DEVICE_STATUS_ACTION_DISABLE = 'CHANGE_CHROME_OS_DEVICE_STATUS_ACTION_DISABLE';
  /**
   * Reenables a ChromeOS device to be used after being disabled. Reenables the
   * device once it's no longer lost or it's been recovered. This allows the
   * device to be used again. Depending on the upgrade associated with the
   * device this might consume one license from the license pool, meaning that
   * if there aren't enough licenses available the operation will fail.
   */
  public const CHANGE_CHROME_OS_DEVICE_STATUS_ACTION_CHANGE_CHROME_OS_DEVICE_STATUS_ACTION_REENABLE = 'CHANGE_CHROME_OS_DEVICE_STATUS_ACTION_REENABLE';
  /**
   * The deprovision reason is unknown.
   */
  public const DEPROVISION_REASON_DEPROVISION_REASON_UNSPECIFIED = 'DEPROVISION_REASON_UNSPECIFIED';
  /**
   * Same model replacement. You have return materials authorization (RMA) or
   * you are replacing a malfunctioning device under warranty with the same
   * device model.
   */
  public const DEPROVISION_REASON_DEPROVISION_REASON_SAME_MODEL_REPLACEMENT = 'DEPROVISION_REASON_SAME_MODEL_REPLACEMENT';
  /**
   * The device was upgraded.
   *
   * @deprecated
   */
  public const DEPROVISION_REASON_DEPROVISION_REASON_UPGRADE = 'DEPROVISION_REASON_UPGRADE';
  /**
   * The device's domain was changed.
   *
   * @deprecated
   */
  public const DEPROVISION_REASON_DEPROVISION_REASON_DOMAIN_MOVE = 'DEPROVISION_REASON_DOMAIN_MOVE';
  /**
   * Service expired for the device.
   *
   * @deprecated
   */
  public const DEPROVISION_REASON_DEPROVISION_REASON_SERVICE_EXPIRATION = 'DEPROVISION_REASON_SERVICE_EXPIRATION';
  /**
   * The device was deprovisioned for a legacy reason that is no longer
   * supported.
   *
   * @deprecated
   */
  public const DEPROVISION_REASON_DEPROVISION_REASON_OTHER = 'DEPROVISION_REASON_OTHER';
  /**
   * Different model replacement. You are replacing this device with an upgraded
   * or newer device model.
   */
  public const DEPROVISION_REASON_DEPROVISION_REASON_DIFFERENT_MODEL_REPLACEMENT = 'DEPROVISION_REASON_DIFFERENT_MODEL_REPLACEMENT';
  /**
   * Retiring from fleet. You are donating, discarding, or otherwise removing
   * the device from use.
   */
  public const DEPROVISION_REASON_DEPROVISION_REASON_RETIRING_DEVICE = 'DEPROVISION_REASON_RETIRING_DEVICE';
  /**
   * ChromeOS Flex upgrade transfer. This is a ChromeOS Flex device that you are
   * replacing with a Chromebook within a year.
   */
  public const DEPROVISION_REASON_DEPROVISION_REASON_UPGRADE_TRANSFER = 'DEPROVISION_REASON_UPGRADE_TRANSFER';
  /**
   * A reason was not required. For example, the licenses were returned to the
   * customer's license pool.
   */
  public const DEPROVISION_REASON_DEPROVISION_REASON_NOT_REQUIRED = 'DEPROVISION_REASON_NOT_REQUIRED';
  /**
   * The device was deprovisioned by the Repair Service Center. Can only be set
   * by Repair Service Center during RMA.
   */
  public const DEPROVISION_REASON_DEPROVISION_REASON_REPAIR_CENTER = 'DEPROVISION_REASON_REPAIR_CENTER';
  protected $collection_key = 'deviceIds';
  /**
   * Required. The action to take on the ChromeOS device in order to change its
   * status.
   *
   * @var string
   */
  public $changeChromeOsDeviceStatusAction;
  /**
   * Optional. The reason behind a device deprovision. Must be provided if
   * 'changeChromeOsDeviceStatusAction' is set to
   * 'CHANGE_CHROME_OS_DEVICE_STATUS_ACTION_DEPROVISION'. Otherwise, omit this
   * field.
   *
   * @var string
   */
  public $deprovisionReason;
  /**
   * Required. List of the IDs of the ChromeOS devices to change. Maximum 50.
   *
   * @var string[]
   */
  public $deviceIds;

  /**
   * Required. The action to take on the ChromeOS device in order to change its
   * status.
   *
   * Accepted values: CHANGE_CHROME_OS_DEVICE_STATUS_ACTION_UNSPECIFIED,
   * CHANGE_CHROME_OS_DEVICE_STATUS_ACTION_DEPROVISION,
   * CHANGE_CHROME_OS_DEVICE_STATUS_ACTION_DISABLE,
   * CHANGE_CHROME_OS_DEVICE_STATUS_ACTION_REENABLE
   *
   * @param self::CHANGE_CHROME_OS_DEVICE_STATUS_ACTION_* $changeChromeOsDeviceStatusAction
   */
  public function setChangeChromeOsDeviceStatusAction($changeChromeOsDeviceStatusAction)
  {
    $this->changeChromeOsDeviceStatusAction = $changeChromeOsDeviceStatusAction;
  }
  /**
   * @return self::CHANGE_CHROME_OS_DEVICE_STATUS_ACTION_*
   */
  public function getChangeChromeOsDeviceStatusAction()
  {
    return $this->changeChromeOsDeviceStatusAction;
  }
  /**
   * Optional. The reason behind a device deprovision. Must be provided if
   * 'changeChromeOsDeviceStatusAction' is set to
   * 'CHANGE_CHROME_OS_DEVICE_STATUS_ACTION_DEPROVISION'. Otherwise, omit this
   * field.
   *
   * Accepted values: DEPROVISION_REASON_UNSPECIFIED,
   * DEPROVISION_REASON_SAME_MODEL_REPLACEMENT, DEPROVISION_REASON_UPGRADE,
   * DEPROVISION_REASON_DOMAIN_MOVE, DEPROVISION_REASON_SERVICE_EXPIRATION,
   * DEPROVISION_REASON_OTHER, DEPROVISION_REASON_DIFFERENT_MODEL_REPLACEMENT,
   * DEPROVISION_REASON_RETIRING_DEVICE, DEPROVISION_REASON_UPGRADE_TRANSFER,
   * DEPROVISION_REASON_NOT_REQUIRED, DEPROVISION_REASON_REPAIR_CENTER
   *
   * @param self::DEPROVISION_REASON_* $deprovisionReason
   */
  public function setDeprovisionReason($deprovisionReason)
  {
    $this->deprovisionReason = $deprovisionReason;
  }
  /**
   * @return self::DEPROVISION_REASON_*
   */
  public function getDeprovisionReason()
  {
    return $this->deprovisionReason;
  }
  /**
   * Required. List of the IDs of the ChromeOS devices to change. Maximum 50.
   *
   * @param string[] $deviceIds
   */
  public function setDeviceIds($deviceIds)
  {
    $this->deviceIds = $deviceIds;
  }
  /**
   * @return string[]
   */
  public function getDeviceIds()
  {
    return $this->deviceIds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BatchChangeChromeOsDeviceStatusRequest::class, 'Google_Service_Directory_BatchChangeChromeOsDeviceStatusRequest');
