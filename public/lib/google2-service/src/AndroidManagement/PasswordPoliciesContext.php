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

class PasswordPoliciesContext extends \Google\Model
{
  /**
   * The scope is unspecified. The password requirements are applied to the work
   * profile for work profile devices and the whole device for fully managed or
   * dedicated devices.
   */
  public const PASSWORD_POLICY_SCOPE_SCOPE_UNSPECIFIED = 'SCOPE_UNSPECIFIED';
  /**
   * The password requirements are only applied to the device.
   */
  public const PASSWORD_POLICY_SCOPE_SCOPE_DEVICE = 'SCOPE_DEVICE';
  /**
   * The password requirements are only applied to the work profile.
   */
  public const PASSWORD_POLICY_SCOPE_SCOPE_PROFILE = 'SCOPE_PROFILE';
  /**
   * The scope of non-compliant password.
   *
   * @var string
   */
  public $passwordPolicyScope;

  /**
   * The scope of non-compliant password.
   *
   * Accepted values: SCOPE_UNSPECIFIED, SCOPE_DEVICE, SCOPE_PROFILE
   *
   * @param self::PASSWORD_POLICY_SCOPE_* $passwordPolicyScope
   */
  public function setPasswordPolicyScope($passwordPolicyScope)
  {
    $this->passwordPolicyScope = $passwordPolicyScope;
  }
  /**
   * @return self::PASSWORD_POLICY_SCOPE_*
   */
  public function getPasswordPolicyScope()
  {
    return $this->passwordPolicyScope;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PasswordPoliciesContext::class, 'Google_Service_AndroidManagement_PasswordPoliciesContext');
