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

namespace Google\Service\PolicySimulator;

class GoogleCloudPolicysimulatorV1betaOrgPolicyOverlayCustomConstraintOverlay extends \Google\Model
{
  protected $customConstraintType = GoogleCloudOrgpolicyV2CustomConstraint::class;
  protected $customConstraintDataType = '';
  /**
   * Optional. Resource the constraint is attached to. Example:
   * "organization/987654"
   *
   * @var string
   */
  public $customConstraintParent;

  /**
   * Optional. The new or updated custom constraint.
   *
   * @param GoogleCloudOrgpolicyV2CustomConstraint $customConstraint
   */
  public function setCustomConstraint(GoogleCloudOrgpolicyV2CustomConstraint $customConstraint)
  {
    $this->customConstraint = $customConstraint;
  }
  /**
   * @return GoogleCloudOrgpolicyV2CustomConstraint
   */
  public function getCustomConstraint()
  {
    return $this->customConstraint;
  }
  /**
   * Optional. Resource the constraint is attached to. Example:
   * "organization/987654"
   *
   * @param string $customConstraintParent
   */
  public function setCustomConstraintParent($customConstraintParent)
  {
    $this->customConstraintParent = $customConstraintParent;
  }
  /**
   * @return string
   */
  public function getCustomConstraintParent()
  {
    return $this->customConstraintParent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudPolicysimulatorV1betaOrgPolicyOverlayCustomConstraintOverlay::class, 'Google_Service_PolicySimulator_GoogleCloudPolicysimulatorV1betaOrgPolicyOverlayCustomConstraintOverlay');
