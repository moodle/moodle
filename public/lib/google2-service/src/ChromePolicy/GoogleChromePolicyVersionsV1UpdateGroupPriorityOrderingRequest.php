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

namespace Google\Service\ChromePolicy;

class GoogleChromePolicyVersionsV1UpdateGroupPriorityOrderingRequest extends \Google\Collection
{
  protected $collection_key = 'groupIds';
  /**
   * Required. The group IDs, in desired priority ordering.
   *
   * @var string[]
   */
  public $groupIds;
  /**
   * The namespace of the policy type for the request.
   *
   * @var string
   */
  public $policyNamespace;
  /**
   * The schema name of the policy for the request.
   *
   * @var string
   */
  public $policySchema;
  protected $policyTargetKeyType = GoogleChromePolicyVersionsV1PolicyTargetKey::class;
  protected $policyTargetKeyDataType = '';

  /**
   * Required. The group IDs, in desired priority ordering.
   *
   * @param string[] $groupIds
   */
  public function setGroupIds($groupIds)
  {
    $this->groupIds = $groupIds;
  }
  /**
   * @return string[]
   */
  public function getGroupIds()
  {
    return $this->groupIds;
  }
  /**
   * The namespace of the policy type for the request.
   *
   * @param string $policyNamespace
   */
  public function setPolicyNamespace($policyNamespace)
  {
    $this->policyNamespace = $policyNamespace;
  }
  /**
   * @return string
   */
  public function getPolicyNamespace()
  {
    return $this->policyNamespace;
  }
  /**
   * The schema name of the policy for the request.
   *
   * @param string $policySchema
   */
  public function setPolicySchema($policySchema)
  {
    $this->policySchema = $policySchema;
  }
  /**
   * @return string
   */
  public function getPolicySchema()
  {
    return $this->policySchema;
  }
  /**
   * Required. The key of the target for which we want to update the group
   * priority ordering. The target resource must point to an app.
   *
   * @param GoogleChromePolicyVersionsV1PolicyTargetKey $policyTargetKey
   */
  public function setPolicyTargetKey(GoogleChromePolicyVersionsV1PolicyTargetKey $policyTargetKey)
  {
    $this->policyTargetKey = $policyTargetKey;
  }
  /**
   * @return GoogleChromePolicyVersionsV1PolicyTargetKey
   */
  public function getPolicyTargetKey()
  {
    return $this->policyTargetKey;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromePolicyVersionsV1UpdateGroupPriorityOrderingRequest::class, 'Google_Service_ChromePolicy_GoogleChromePolicyVersionsV1UpdateGroupPriorityOrderingRequest');
