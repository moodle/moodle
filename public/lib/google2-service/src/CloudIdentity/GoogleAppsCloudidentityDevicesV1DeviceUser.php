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

namespace Google\Service\CloudIdentity;

class GoogleAppsCloudidentityDevicesV1DeviceUser extends \Google\Model
{
  /**
   * Compromised state of Device User account is unknown or unspecified.
   */
  public const COMPROMISED_STATE_COMPROMISED_STATE_UNSPECIFIED = 'COMPROMISED_STATE_UNSPECIFIED';
  /**
   * Device User Account is compromised.
   */
  public const COMPROMISED_STATE_COMPROMISED = 'COMPROMISED';
  /**
   * Device User Account is not compromised.
   */
  public const COMPROMISED_STATE_NOT_COMPROMISED = 'NOT_COMPROMISED';
  /**
   * Default value. This value is unused.
   */
  public const MANAGEMENT_STATE_MANAGEMENT_STATE_UNSPECIFIED = 'MANAGEMENT_STATE_UNSPECIFIED';
  /**
   * This user's data and profile is being removed from the device.
   */
  public const MANAGEMENT_STATE_WIPING = 'WIPING';
  /**
   * This user's data and profile is removed from the device.
   */
  public const MANAGEMENT_STATE_WIPED = 'WIPED';
  /**
   * User is approved to access data on the device.
   */
  public const MANAGEMENT_STATE_APPROVED = 'APPROVED';
  /**
   * User is blocked from accessing data on the device.
   */
  public const MANAGEMENT_STATE_BLOCKED = 'BLOCKED';
  /**
   * User is awaiting approval.
   */
  public const MANAGEMENT_STATE_PENDING_APPROVAL = 'PENDING_APPROVAL';
  /**
   * User is unenrolled from Advanced Windows Management, but the Windows
   * account is still intact.
   */
  public const MANAGEMENT_STATE_UNENROLLED = 'UNENROLLED';
  /**
   * Password state not set.
   */
  public const PASSWORD_STATE_PASSWORD_STATE_UNSPECIFIED = 'PASSWORD_STATE_UNSPECIFIED';
  /**
   * Password set in object.
   */
  public const PASSWORD_STATE_PASSWORD_SET = 'PASSWORD_SET';
  /**
   * Password not set in object.
   */
  public const PASSWORD_STATE_PASSWORD_NOT_SET = 'PASSWORD_NOT_SET';
  /**
   * Compromised State of the DeviceUser object
   *
   * @var string
   */
  public $compromisedState;
  /**
   * When the user first signed in to the device
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. Most recent time when user registered with this service.
   *
   * @var string
   */
  public $firstSyncTime;
  /**
   * Output only. Default locale used on device, in IETF BCP-47 format.
   *
   * @var string
   */
  public $languageCode;
  /**
   * Output only. Last time when user synced with policies.
   *
   * @var string
   */
  public $lastSyncTime;
  /**
   * Output only. Management state of the user on the device.
   *
   * @var string
   */
  public $managementState;
  /**
   * Output only. [Resource
   * name](https://cloud.google.com/apis/design/resource_names) of the
   * DeviceUser in format: `devices/{device}/deviceUsers/{device_user}`, where
   * `device_user` uniquely identifies a user's use of a device.
   *
   * @var string
   */
  public $name;
  /**
   * Password state of the DeviceUser object
   *
   * @var string
   */
  public $passwordState;
  /**
   * Output only. User agent on the device for this specific user
   *
   * @var string
   */
  public $userAgent;
  /**
   * Email address of the user registered on the device.
   *
   * @var string
   */
  public $userEmail;

  /**
   * Compromised State of the DeviceUser object
   *
   * Accepted values: COMPROMISED_STATE_UNSPECIFIED, COMPROMISED,
   * NOT_COMPROMISED
   *
   * @param self::COMPROMISED_STATE_* $compromisedState
   */
  public function setCompromisedState($compromisedState)
  {
    $this->compromisedState = $compromisedState;
  }
  /**
   * @return self::COMPROMISED_STATE_*
   */
  public function getCompromisedState()
  {
    return $this->compromisedState;
  }
  /**
   * When the user first signed in to the device
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Output only. Most recent time when user registered with this service.
   *
   * @param string $firstSyncTime
   */
  public function setFirstSyncTime($firstSyncTime)
  {
    $this->firstSyncTime = $firstSyncTime;
  }
  /**
   * @return string
   */
  public function getFirstSyncTime()
  {
    return $this->firstSyncTime;
  }
  /**
   * Output only. Default locale used on device, in IETF BCP-47 format.
   *
   * @param string $languageCode
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
  /**
   * Output only. Last time when user synced with policies.
   *
   * @param string $lastSyncTime
   */
  public function setLastSyncTime($lastSyncTime)
  {
    $this->lastSyncTime = $lastSyncTime;
  }
  /**
   * @return string
   */
  public function getLastSyncTime()
  {
    return $this->lastSyncTime;
  }
  /**
   * Output only. Management state of the user on the device.
   *
   * Accepted values: MANAGEMENT_STATE_UNSPECIFIED, WIPING, WIPED, APPROVED,
   * BLOCKED, PENDING_APPROVAL, UNENROLLED
   *
   * @param self::MANAGEMENT_STATE_* $managementState
   */
  public function setManagementState($managementState)
  {
    $this->managementState = $managementState;
  }
  /**
   * @return self::MANAGEMENT_STATE_*
   */
  public function getManagementState()
  {
    return $this->managementState;
  }
  /**
   * Output only. [Resource
   * name](https://cloud.google.com/apis/design/resource_names) of the
   * DeviceUser in format: `devices/{device}/deviceUsers/{device_user}`, where
   * `device_user` uniquely identifies a user's use of a device.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Password state of the DeviceUser object
   *
   * Accepted values: PASSWORD_STATE_UNSPECIFIED, PASSWORD_SET, PASSWORD_NOT_SET
   *
   * @param self::PASSWORD_STATE_* $passwordState
   */
  public function setPasswordState($passwordState)
  {
    $this->passwordState = $passwordState;
  }
  /**
   * @return self::PASSWORD_STATE_*
   */
  public function getPasswordState()
  {
    return $this->passwordState;
  }
  /**
   * Output only. User agent on the device for this specific user
   *
   * @param string $userAgent
   */
  public function setUserAgent($userAgent)
  {
    $this->userAgent = $userAgent;
  }
  /**
   * @return string
   */
  public function getUserAgent()
  {
    return $this->userAgent;
  }
  /**
   * Email address of the user registered on the device.
   *
   * @param string $userEmail
   */
  public function setUserEmail($userEmail)
  {
    $this->userEmail = $userEmail;
  }
  /**
   * @return string
   */
  public function getUserEmail()
  {
    return $this->userEmail;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsCloudidentityDevicesV1DeviceUser::class, 'Google_Service_CloudIdentity_GoogleAppsCloudidentityDevicesV1DeviceUser');
