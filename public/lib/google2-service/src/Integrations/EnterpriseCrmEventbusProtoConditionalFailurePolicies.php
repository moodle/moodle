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

namespace Google\Service\Integrations;

class EnterpriseCrmEventbusProtoConditionalFailurePolicies extends \Google\Collection
{
  protected $collection_key = 'failurePolicies';
  protected $defaultFailurePolicyType = EnterpriseCrmEventbusProtoFailurePolicy::class;
  protected $defaultFailurePolicyDataType = '';
  protected $failurePoliciesType = EnterpriseCrmEventbusProtoFailurePolicy::class;
  protected $failurePoliciesDataType = 'array';

  /**
   * The default failure policy to be applied if no conditional failure policy
   * matches
   *
   * @param EnterpriseCrmEventbusProtoFailurePolicy $defaultFailurePolicy
   */
  public function setDefaultFailurePolicy(EnterpriseCrmEventbusProtoFailurePolicy $defaultFailurePolicy)
  {
    $this->defaultFailurePolicy = $defaultFailurePolicy;
  }
  /**
   * @return EnterpriseCrmEventbusProtoFailurePolicy
   */
  public function getDefaultFailurePolicy()
  {
    return $this->defaultFailurePolicy;
  }
  /**
   * The list of failure policies that will be applied to the task in order.
   *
   * @param EnterpriseCrmEventbusProtoFailurePolicy[] $failurePolicies
   */
  public function setFailurePolicies($failurePolicies)
  {
    $this->failurePolicies = $failurePolicies;
  }
  /**
   * @return EnterpriseCrmEventbusProtoFailurePolicy[]
   */
  public function getFailurePolicies()
  {
    return $this->failurePolicies;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseCrmEventbusProtoConditionalFailurePolicies::class, 'Google_Service_Integrations_EnterpriseCrmEventbusProtoConditionalFailurePolicies');
