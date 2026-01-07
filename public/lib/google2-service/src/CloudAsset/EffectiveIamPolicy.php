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

namespace Google\Service\CloudAsset;

class EffectiveIamPolicy extends \Google\Collection
{
  protected $collection_key = 'policies';
  /**
   * The [full_resource_name] (https://cloud.google.com/asset-
   * inventory/docs/resource-name-format) for which the policies are computed.
   * This is one of the BatchGetEffectiveIamPoliciesRequest.names the caller
   * provides in the request.
   *
   * @var string
   */
  public $fullResourceName;
  protected $policiesType = PolicyInfo::class;
  protected $policiesDataType = 'array';

  /**
   * The [full_resource_name] (https://cloud.google.com/asset-
   * inventory/docs/resource-name-format) for which the policies are computed.
   * This is one of the BatchGetEffectiveIamPoliciesRequest.names the caller
   * provides in the request.
   *
   * @param string $fullResourceName
   */
  public function setFullResourceName($fullResourceName)
  {
    $this->fullResourceName = $fullResourceName;
  }
  /**
   * @return string
   */
  public function getFullResourceName()
  {
    return $this->fullResourceName;
  }
  /**
   * The effective policies for the full_resource_name. These policies include
   * the policy set on the full_resource_name and those set on its parents and
   * ancestors up to the BatchGetEffectiveIamPoliciesRequest.scope. Note that
   * these policies are not filtered according to the resource type of the
   * full_resource_name. These policies are hierarchically ordered by
   * PolicyInfo.attached_resource starting from full_resource_name itself to its
   * parents and ancestors, such that policies[i]'s PolicyInfo.attached_resource
   * is the child of policies[i+1]'s PolicyInfo.attached_resource, if
   * policies[i+1] exists.
   *
   * @param PolicyInfo[] $policies
   */
  public function setPolicies($policies)
  {
    $this->policies = $policies;
  }
  /**
   * @return PolicyInfo[]
   */
  public function getPolicies()
  {
    return $this->policies;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EffectiveIamPolicy::class, 'Google_Service_CloudAsset_EffectiveIamPolicy');
