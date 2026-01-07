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

namespace Google\Service\Assuredworkloads;

class GoogleCloudAssuredworkloadsV1OrgPolicyUpdate extends \Google\Model
{
  protected $appliedPolicyType = GoogleCloudAssuredworkloadsV1OrgPolicy::class;
  protected $appliedPolicyDataType = '';
  protected $suggestedPolicyType = GoogleCloudAssuredworkloadsV1OrgPolicy::class;
  protected $suggestedPolicyDataType = '';

  /**
   * The org policy currently applied on the assured workload resource.
   *
   * @param GoogleCloudAssuredworkloadsV1OrgPolicy $appliedPolicy
   */
  public function setAppliedPolicy(GoogleCloudAssuredworkloadsV1OrgPolicy $appliedPolicy)
  {
    $this->appliedPolicy = $appliedPolicy;
  }
  /**
   * @return GoogleCloudAssuredworkloadsV1OrgPolicy
   */
  public function getAppliedPolicy()
  {
    return $this->appliedPolicy;
  }
  /**
   * The suggested org policy that replaces the applied policy.
   *
   * @param GoogleCloudAssuredworkloadsV1OrgPolicy $suggestedPolicy
   */
  public function setSuggestedPolicy(GoogleCloudAssuredworkloadsV1OrgPolicy $suggestedPolicy)
  {
    $this->suggestedPolicy = $suggestedPolicy;
  }
  /**
   * @return GoogleCloudAssuredworkloadsV1OrgPolicy
   */
  public function getSuggestedPolicy()
  {
    return $this->suggestedPolicy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAssuredworkloadsV1OrgPolicyUpdate::class, 'Google_Service_Assuredworkloads_GoogleCloudAssuredworkloadsV1OrgPolicyUpdate');
