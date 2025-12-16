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

class SpecificNonComplianceContext extends \Google\Model
{
  protected $defaultApplicationContextType = DefaultApplicationContext::class;
  protected $defaultApplicationContextDataType = '';
  protected $oncWifiContextType = OncWifiContext::class;
  protected $oncWifiContextDataType = '';
  protected $passwordPoliciesContextType = PasswordPoliciesContext::class;
  protected $passwordPoliciesContextDataType = '';

  /**
   * Output only. Additional context for non-compliance related to default
   * application settings. See DEFAULT_APPLICATION_SETTING_FAILED_FOR_SCOPE.
   *
   * @param DefaultApplicationContext $defaultApplicationContext
   */
  public function setDefaultApplicationContext(DefaultApplicationContext $defaultApplicationContext)
  {
    $this->defaultApplicationContext = $defaultApplicationContext;
  }
  /**
   * @return DefaultApplicationContext
   */
  public function getDefaultApplicationContext()
  {
    return $this->defaultApplicationContext;
  }
  /**
   * Additional context for non-compliance related to Wi-Fi configuration. See
   * ONC_WIFI_INVALID_VALUE and ONC_WIFI_API_LEVEL
   *
   * @param OncWifiContext $oncWifiContext
   */
  public function setOncWifiContext(OncWifiContext $oncWifiContext)
  {
    $this->oncWifiContext = $oncWifiContext;
  }
  /**
   * @return OncWifiContext
   */
  public function getOncWifiContext()
  {
    return $this->oncWifiContext;
  }
  /**
   * Additional context for non-compliance related to password policies. See
   * PASSWORD_POLICIES_PASSWORD_EXPIRED and
   * PASSWORD_POLICIES_PASSWORD_NOT_SUFFICIENT.
   *
   * @param PasswordPoliciesContext $passwordPoliciesContext
   */
  public function setPasswordPoliciesContext(PasswordPoliciesContext $passwordPoliciesContext)
  {
    $this->passwordPoliciesContext = $passwordPoliciesContext;
  }
  /**
   * @return PasswordPoliciesContext
   */
  public function getPasswordPoliciesContext()
  {
    return $this->passwordPoliciesContext;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SpecificNonComplianceContext::class, 'Google_Service_AndroidManagement_SpecificNonComplianceContext');
