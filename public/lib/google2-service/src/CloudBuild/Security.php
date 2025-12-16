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

namespace Google\Service\CloudBuild;

class Security extends \Google\Model
{
  /**
   * Default to PRIVILEGED.
   */
  public const PRIVILEGE_MODE_PRIVILEGE_MODE_UNSPECIFIED = 'PRIVILEGE_MODE_UNSPECIFIED';
  /**
   * Privileged mode.
   */
  public const PRIVILEGE_MODE_PRIVILEGED = 'PRIVILEGED';
  /**
   * Unprivileged mode.
   */
  public const PRIVILEGE_MODE_UNPRIVILEGED = 'UNPRIVILEGED';
  /**
   * Optional. Privilege mode.
   *
   * @deprecated
   * @var string
   */
  public $privilegeMode;
  /**
   * IAM service account whose credentials will be used at runtime.
   *
   * @var string
   */
  public $serviceAccount;

  /**
   * Optional. Privilege mode.
   *
   * Accepted values: PRIVILEGE_MODE_UNSPECIFIED, PRIVILEGED, UNPRIVILEGED
   *
   * @deprecated
   * @param self::PRIVILEGE_MODE_* $privilegeMode
   */
  public function setPrivilegeMode($privilegeMode)
  {
    $this->privilegeMode = $privilegeMode;
  }
  /**
   * @deprecated
   * @return self::PRIVILEGE_MODE_*
   */
  public function getPrivilegeMode()
  {
    return $this->privilegeMode;
  }
  /**
   * IAM service account whose credentials will be used at runtime.
   *
   * @param string $serviceAccount
   */
  public function setServiceAccount($serviceAccount)
  {
    $this->serviceAccount = $serviceAccount;
  }
  /**
   * @return string
   */
  public function getServiceAccount()
  {
    return $this->serviceAccount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Security::class, 'Google_Service_CloudBuild_Security');
