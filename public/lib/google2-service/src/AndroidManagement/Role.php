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

class Role extends \Google\Model
{
  /**
   * The role type is unspecified. This value must not be used.
   */
  public const ROLE_TYPE_ROLE_TYPE_UNSPECIFIED = 'ROLE_TYPE_UNSPECIFIED';
  /**
   * The role type for companion apps. This role enables the app as a companion
   * app with the capability of interacting with Android Device Policy offline.
   * This is the recommended way to configure an app as a companion app. For
   * legacy way, see extensionConfig.On Android 14 and above, the app with this
   * role is exempted from power and background execution restrictions,
   * suspension and hibernation. On Android 11 and above, the user control is
   * disallowed for the app with this role. userControlSettings cannot be set to
   * USER_CONTROL_ALLOWED for the app with this role.Android Device Policy
   * notifies the companion app of any local command status updates if the app
   * has a service with . See Integrate with the AMAPI SDK
   * (https://developers.google.com/android/management/sdk-integration) guide
   * for more details on the requirements for the service.
   */
  public const ROLE_TYPE_COMPANION_APP = 'COMPANION_APP';
  /**
   * The role type for kiosk apps. An app can have this role only if it has
   * installType set to REQUIRED_FOR_SETUP or CUSTOM. Before adding this role to
   * an app with CUSTOM install type, the app must already be installed on the
   * device.The app having this role type is set as the preferred home intent
   * and allowlisted for lock task mode. When there is an app with this role
   * type, status bar will be automatically disabled.This is preferable to
   * setting installType to KIOSK.On Android 11 and above, the user control is
   * disallowed but userControlSettings can be set to USER_CONTROL_ALLOWED to
   * allow user control for the app with this role.
   */
  public const ROLE_TYPE_KIOSK = 'KIOSK';
  /**
   * The role type for Mobile Threat Defense (MTD) / Endpoint Detection &
   * Response (EDR) apps.On Android 14 and above, the app with this role is
   * exempted from power and background execution restrictions, suspension and
   * hibernation. On Android 11 and above, the user control is disallowed and
   * userControlSettings cannot be set to USER_CONTROL_ALLOWED for the app with
   * this role.
   */
  public const ROLE_TYPE_MOBILE_THREAT_DEFENSE_ENDPOINT_DETECTION_RESPONSE = 'MOBILE_THREAT_DEFENSE_ENDPOINT_DETECTION_RESPONSE';
  /**
   * The role type for system health monitoring apps.On Android 14 and above,
   * the app with this role is exempted from power and background execution
   * restrictions, suspension and hibernation. On Android 11 and above, the user
   * control is disallowed and userControlSettings cannot be set to
   * USER_CONTROL_ALLOWED for the app with this role.
   */
  public const ROLE_TYPE_SYSTEM_HEALTH_MONITORING = 'SYSTEM_HEALTH_MONITORING';
  /**
   * Required. The type of the role an app can have.
   *
   * @var string
   */
  public $roleType;

  /**
   * Required. The type of the role an app can have.
   *
   * Accepted values: ROLE_TYPE_UNSPECIFIED, COMPANION_APP, KIOSK,
   * MOBILE_THREAT_DEFENSE_ENDPOINT_DETECTION_RESPONSE, SYSTEM_HEALTH_MONITORING
   *
   * @param self::ROLE_TYPE_* $roleType
   */
  public function setRoleType($roleType)
  {
    $this->roleType = $roleType;
  }
  /**
   * @return self::ROLE_TYPE_*
   */
  public function getRoleType()
  {
    return $this->roleType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Role::class, 'Google_Service_AndroidManagement_Role');
