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

class OrgPolicyConstraintCustom extends \Google\Collection
{
  protected $collection_key = 'policyRules';
  protected $customConstraintType = GoogleCloudSecuritypostureV1CustomConstraint::class;
  protected $customConstraintDataType = '';
  protected $policyRulesType = GoogleCloudSecuritypostureV1PolicyRule::class;
  protected $policyRulesDataType = 'array';

  /**
   * Required. Metadata for the constraint.
   *
   * @param GoogleCloudSecuritypostureV1CustomConstraint $customConstraint
   */
  public function setCustomConstraint(GoogleCloudSecuritypostureV1CustomConstraint $customConstraint)
  {
    $this->customConstraint = $customConstraint;
  }
  /**
   * @return GoogleCloudSecuritypostureV1CustomConstraint
   */
  public function getCustomConstraint()
  {
    return $this->customConstraint;
  }
  /**
   * Required. The rules enforced by the constraint.
   *
   * @param GoogleCloudSecuritypostureV1PolicyRule[] $policyRules
   */
  public function setPolicyRules($policyRules)
  {
    $this->policyRules = $policyRules;
  }
  /**
   * @return GoogleCloudSecuritypostureV1PolicyRule[]
   */
  public function getPolicyRules()
  {
    return $this->policyRules;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrgPolicyConstraintCustom::class, 'Google_Service_SecurityPosture_OrgPolicyConstraintCustom');
