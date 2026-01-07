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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1alphaUserLicense extends \Google\Model
{
  /**
   * Default value.
   */
  public const LICENSE_ASSIGNMENT_STATE_LICENSE_ASSIGNMENT_STATE_UNSPECIFIED = 'LICENSE_ASSIGNMENT_STATE_UNSPECIFIED';
  /**
   * License assigned to the user.
   */
  public const LICENSE_ASSIGNMENT_STATE_ASSIGNED = 'ASSIGNED';
  /**
   * No license assigned to the user. Deprecated, translated to NO_LICENSE.
   */
  public const LICENSE_ASSIGNMENT_STATE_UNASSIGNED = 'UNASSIGNED';
  /**
   * No license assigned to the user.
   */
  public const LICENSE_ASSIGNMENT_STATE_NO_LICENSE = 'NO_LICENSE';
  /**
   * User attempted to login but no license assigned to the user. This state is
   * only used for no user first time login attempt but cannot get license
   * assigned. Users already logged in but cannot get license assigned will be
   * assigned NO_LICENSE state(License could be unassigned by admin).
   */
  public const LICENSE_ASSIGNMENT_STATE_NO_LICENSE_ATTEMPTED_LOGIN = 'NO_LICENSE_ATTEMPTED_LOGIN';
  /**
   * User is blocked from assigning a license.
   */
  public const LICENSE_ASSIGNMENT_STATE_BLOCKED = 'BLOCKED';
  /**
   * Output only. User created timestamp.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. User last logged in time. If the user has not logged in yet,
   * this field will be empty.
   *
   * @var string
   */
  public $lastLoginTime;
  /**
   * Output only. License assignment state of the user. If the user is assigned
   * with a license config, the user login will be assigned with the license; If
   * the user's license assignment state is unassigned or unspecified, no
   * license config will be associated to the user;
   *
   * @var string
   */
  public $licenseAssignmentState;
  /**
   * Optional. The full resource name of the Subscription(LicenseConfig)
   * assigned to the user.
   *
   * @var string
   */
  public $licenseConfig;
  /**
   * Output only. User update timestamp.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Required. Immutable. The user principal of the User, could be email address
   * or other prinical identifier. This field is immutable. Admin assign
   * licenses based on the user principal.
   *
   * @var string
   */
  public $userPrincipal;
  /**
   * Optional. The user profile. We user user full name(First name + Last name)
   * as user profile.
   *
   * @var string
   */
  public $userProfile;

  /**
   * Output only. User created timestamp.
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
   * Output only. User last logged in time. If the user has not logged in yet,
   * this field will be empty.
   *
   * @param string $lastLoginTime
   */
  public function setLastLoginTime($lastLoginTime)
  {
    $this->lastLoginTime = $lastLoginTime;
  }
  /**
   * @return string
   */
  public function getLastLoginTime()
  {
    return $this->lastLoginTime;
  }
  /**
   * Output only. License assignment state of the user. If the user is assigned
   * with a license config, the user login will be assigned with the license; If
   * the user's license assignment state is unassigned or unspecified, no
   * license config will be associated to the user;
   *
   * Accepted values: LICENSE_ASSIGNMENT_STATE_UNSPECIFIED, ASSIGNED,
   * UNASSIGNED, NO_LICENSE, NO_LICENSE_ATTEMPTED_LOGIN, BLOCKED
   *
   * @param self::LICENSE_ASSIGNMENT_STATE_* $licenseAssignmentState
   */
  public function setLicenseAssignmentState($licenseAssignmentState)
  {
    $this->licenseAssignmentState = $licenseAssignmentState;
  }
  /**
   * @return self::LICENSE_ASSIGNMENT_STATE_*
   */
  public function getLicenseAssignmentState()
  {
    return $this->licenseAssignmentState;
  }
  /**
   * Optional. The full resource name of the Subscription(LicenseConfig)
   * assigned to the user.
   *
   * @param string $licenseConfig
   */
  public function setLicenseConfig($licenseConfig)
  {
    $this->licenseConfig = $licenseConfig;
  }
  /**
   * @return string
   */
  public function getLicenseConfig()
  {
    return $this->licenseConfig;
  }
  /**
   * Output only. User update timestamp.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
  /**
   * Required. Immutable. The user principal of the User, could be email address
   * or other prinical identifier. This field is immutable. Admin assign
   * licenses based on the user principal.
   *
   * @param string $userPrincipal
   */
  public function setUserPrincipal($userPrincipal)
  {
    $this->userPrincipal = $userPrincipal;
  }
  /**
   * @return string
   */
  public function getUserPrincipal()
  {
    return $this->userPrincipal;
  }
  /**
   * Optional. The user profile. We user user full name(First name + Last name)
   * as user profile.
   *
   * @param string $userProfile
   */
  public function setUserProfile($userProfile)
  {
    $this->userProfile = $userProfile;
  }
  /**
   * @return string
   */
  public function getUserProfile()
  {
    return $this->userProfile;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1alphaUserLicense::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaUserLicense');
