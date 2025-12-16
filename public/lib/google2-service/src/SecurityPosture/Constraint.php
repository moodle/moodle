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

namespace Google\Service\SecurityPosture;

class Constraint extends \Google\Model
{
  protected $orgPolicyConstraintType = OrgPolicyConstraint::class;
  protected $orgPolicyConstraintDataType = '';
  protected $orgPolicyConstraintCustomType = OrgPolicyConstraintCustom::class;
  protected $orgPolicyConstraintCustomDataType = '';
  protected $securityHealthAnalyticsCustomModuleType = SecurityHealthAnalyticsCustomModule::class;
  protected $securityHealthAnalyticsCustomModuleDataType = '';
  protected $securityHealthAnalyticsModuleType = SecurityHealthAnalyticsModule::class;
  protected $securityHealthAnalyticsModuleDataType = '';

  /**
   * Optional. A predefined organization policy constraint.
   *
   * @param OrgPolicyConstraint $orgPolicyConstraint
   */
  public function setOrgPolicyConstraint(OrgPolicyConstraint $orgPolicyConstraint)
  {
    $this->orgPolicyConstraint = $orgPolicyConstraint;
  }
  /**
   * @return OrgPolicyConstraint
   */
  public function getOrgPolicyConstraint()
  {
    return $this->orgPolicyConstraint;
  }
  /**
   * Optional. A custom organization policy constraint.
   *
   * @param OrgPolicyConstraintCustom $orgPolicyConstraintCustom
   */
  public function setOrgPolicyConstraintCustom(OrgPolicyConstraintCustom $orgPolicyConstraintCustom)
  {
    $this->orgPolicyConstraintCustom = $orgPolicyConstraintCustom;
  }
  /**
   * @return OrgPolicyConstraintCustom
   */
  public function getOrgPolicyConstraintCustom()
  {
    return $this->orgPolicyConstraintCustom;
  }
  /**
   * Optional. A custom module for Security Health Analytics.
   *
   * @param SecurityHealthAnalyticsCustomModule $securityHealthAnalyticsCustomModule
   */
  public function setSecurityHealthAnalyticsCustomModule(SecurityHealthAnalyticsCustomModule $securityHealthAnalyticsCustomModule)
  {
    $this->securityHealthAnalyticsCustomModule = $securityHealthAnalyticsCustomModule;
  }
  /**
   * @return SecurityHealthAnalyticsCustomModule
   */
  public function getSecurityHealthAnalyticsCustomModule()
  {
    return $this->securityHealthAnalyticsCustomModule;
  }
  /**
   * Optional. A built-in detector for Security Health Analytics.
   *
   * @param SecurityHealthAnalyticsModule $securityHealthAnalyticsModule
   */
  public function setSecurityHealthAnalyticsModule(SecurityHealthAnalyticsModule $securityHealthAnalyticsModule)
  {
    $this->securityHealthAnalyticsModule = $securityHealthAnalyticsModule;
  }
  /**
   * @return SecurityHealthAnalyticsModule
   */
  public function getSecurityHealthAnalyticsModule()
  {
    return $this->securityHealthAnalyticsModule;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Constraint::class, 'Google_Service_SecurityPosture_Constraint');
