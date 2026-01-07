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

class GoogleChromePolicyVersionsV1ModifyOrgUnitPolicyRequest extends \Google\Model
{
  protected $policyTargetKeyType = GoogleChromePolicyVersionsV1PolicyTargetKey::class;
  protected $policyTargetKeyDataType = '';
  protected $policyValueType = GoogleChromePolicyVersionsV1PolicyValue::class;
  protected $policyValueDataType = '';
  /**
   * Required. Policy fields to update. Only fields in this mask will be
   * updated; other fields in `policy_value` will be ignored (even if they have
   * values). If a field is in this list it must have a value in 'policy_value'.
   *
   * @var string
   */
  public $updateMask;

  /**
   * Required. The key of the target for which we want to modify a policy. The
   * target resource must point to an Org Unit.
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
  /**
   * The new value for the policy.
   *
   * @param GoogleChromePolicyVersionsV1PolicyValue $policyValue
   */
  public function setPolicyValue(GoogleChromePolicyVersionsV1PolicyValue $policyValue)
  {
    $this->policyValue = $policyValue;
  }
  /**
   * @return GoogleChromePolicyVersionsV1PolicyValue
   */
  public function getPolicyValue()
  {
    return $this->policyValue;
  }
  /**
   * Required. Policy fields to update. Only fields in this mask will be
   * updated; other fields in `policy_value` will be ignored (even if they have
   * values). If a field is in this list it must have a value in 'policy_value'.
   *
   * @param string $updateMask
   */
  public function setUpdateMask($updateMask)
  {
    $this->updateMask = $updateMask;
  }
  /**
   * @return string
   */
  public function getUpdateMask()
  {
    return $this->updateMask;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromePolicyVersionsV1ModifyOrgUnitPolicyRequest::class, 'Google_Service_ChromePolicy_GoogleChromePolicyVersionsV1ModifyOrgUnitPolicyRequest');
