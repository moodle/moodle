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

class GoogleCloudPolicysimulatorV1OrgPolicyOverlayPolicyOverlay extends \Google\Model
{
  protected $policyType = GoogleCloudOrgpolicyV2Policy::class;
  protected $policyDataType = '';
  /**
   * Optional. The parent of the policy we are attaching to. Example:
   * "projects/123456"
   *
   * @var string
   */
  public $policyParent;

  /**
   * Optional. The new or updated OrgPolicy.
   *
   * @param GoogleCloudOrgpolicyV2Policy $policy
   */
  public function setPolicy(GoogleCloudOrgpolicyV2Policy $policy)
  {
    $this->policy = $policy;
  }
  /**
   * @return GoogleCloudOrgpolicyV2Policy
   */
  public function getPolicy()
  {
    return $this->policy;
  }
  /**
   * Optional. The parent of the policy we are attaching to. Example:
   * "projects/123456"
   *
   * @param string $policyParent
   */
  public function setPolicyParent($policyParent)
  {
    $this->policyParent = $policyParent;
  }
  /**
   * @return string
   */
  public function getPolicyParent()
  {
    return $this->policyParent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudPolicysimulatorV1OrgPolicyOverlayPolicyOverlay::class, 'Google_Service_PolicySimulator_GoogleCloudPolicysimulatorV1OrgPolicyOverlayPolicyOverlay');
