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

class GoogleCloudPolicysimulatorV1OrgPolicyOverlay extends \Google\Collection
{
  protected $collection_key = 'policies';
  protected $customConstraintsType = GoogleCloudPolicysimulatorV1OrgPolicyOverlayCustomConstraintOverlay::class;
  protected $customConstraintsDataType = 'array';
  protected $policiesType = GoogleCloudPolicysimulatorV1OrgPolicyOverlayPolicyOverlay::class;
  protected $policiesDataType = 'array';

  /**
   * Optional. The OrgPolicy CustomConstraint changes to preview violations for.
   * Any existing CustomConstraints with the same name will be overridden in the
   * simulation. That is, violations will be determined as if all custom
   * constraints in the overlay were instantiated. Only a single
   * custom_constraint is supported in the overlay at a time. For evaluating
   * multiple constraints, multiple `GenerateOrgPolicyViolationsPreview`
   * requests are made, where each request evaluates a single constraint.
   *
   * @param GoogleCloudPolicysimulatorV1OrgPolicyOverlayCustomConstraintOverlay[] $customConstraints
   */
  public function setCustomConstraints($customConstraints)
  {
    $this->customConstraints = $customConstraints;
  }
  /**
   * @return GoogleCloudPolicysimulatorV1OrgPolicyOverlayCustomConstraintOverlay[]
   */
  public function getCustomConstraints()
  {
    return $this->customConstraints;
  }
  /**
   * Optional. The OrgPolicy changes to preview violations for. Any existing
   * OrgPolicies with the same name will be overridden in the simulation. That
   * is, violations will be determined as if all policies in the overlay were
   * created or updated.
   *
   * @param GoogleCloudPolicysimulatorV1OrgPolicyOverlayPolicyOverlay[] $policies
   */
  public function setPolicies($policies)
  {
    $this->policies = $policies;
  }
  /**
   * @return GoogleCloudPolicysimulatorV1OrgPolicyOverlayPolicyOverlay[]
   */
  public function getPolicies()
  {
    return $this->policies;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudPolicysimulatorV1OrgPolicyOverlay::class, 'Google_Service_PolicySimulator_GoogleCloudPolicysimulatorV1OrgPolicyOverlay');
