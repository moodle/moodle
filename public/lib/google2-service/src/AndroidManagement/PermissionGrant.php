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

class PermissionGrant extends \Google\Model
{
  /**
   * Policy not specified. If no policy is specified for a permission at any
   * level, then the PROMPT behavior is used by default.
   */
  public const POLICY_PERMISSION_POLICY_UNSPECIFIED = 'PERMISSION_POLICY_UNSPECIFIED';
  /**
   * Prompt the user to grant a permission.
   */
  public const POLICY_PROMPT = 'PROMPT';
  /**
   * Automatically grant a permission.On Android 12 and above, READ_SMS (https:/
   * /developer.android.com/reference/android/Manifest.permission#READ_SMS) and
   * following sensor-related permissions can only be granted on fully managed
   * devices: ACCESS_FINE_LOCATION (https://developer.android.com/reference/andr
   * oid/Manifest.permission#ACCESS_FINE_LOCATION) ACCESS_BACKGROUND_LOCATION (h
   * ttps://developer.android.com/reference/android/Manifest.permission#ACCESS_B
   * ACKGROUND_LOCATION) ACCESS_COARSE_LOCATION (https://developer.android.com/r
   * eference/android/Manifest.permission#ACCESS_COARSE_LOCATION) CAMERA (https:
   * //developer.android.com/reference/android/Manifest.permission#CAMERA)
   * RECORD_AUDIO (https://developer.android.com/reference/android/Manifest.perm
   * ission#RECORD_AUDIO) ACTIVITY_RECOGNITION (https://developer.android.com/re
   * ference/android/Manifest.permission#ACTIVITY_RECOGNITION) BODY_SENSORS (htt
   * ps://developer.android.com/reference/android/Manifest.permission#BODY_SENSO
   * RS)
   */
  public const POLICY_GRANT = 'GRANT';
  /**
   * Automatically deny a permission.
   */
  public const POLICY_DENY = 'DENY';
  /**
   * The Android permission or group, e.g. android.permission.READ_CALENDAR or
   * android.permission_group.CALENDAR.
   *
   * @var string
   */
  public $permission;
  /**
   * The policy for granting the permission.
   *
   * @var string
   */
  public $policy;

  /**
   * The Android permission or group, e.g. android.permission.READ_CALENDAR or
   * android.permission_group.CALENDAR.
   *
   * @param string $permission
   */
  public function setPermission($permission)
  {
    $this->permission = $permission;
  }
  /**
   * @return string
   */
  public function getPermission()
  {
    return $this->permission;
  }
  /**
   * The policy for granting the permission.
   *
   * Accepted values: PERMISSION_POLICY_UNSPECIFIED, PROMPT, GRANT, DENY
   *
   * @param self::POLICY_* $policy
   */
  public function setPolicy($policy)
  {
    $this->policy = $policy;
  }
  /**
   * @return self::POLICY_*
   */
  public function getPolicy()
  {
    return $this->policy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PermissionGrant::class, 'Google_Service_AndroidManagement_PermissionGrant');
