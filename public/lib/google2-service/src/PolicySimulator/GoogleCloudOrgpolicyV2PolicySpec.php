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

class GoogleCloudOrgpolicyV2PolicySpec extends \Google\Collection
{
  protected $collection_key = 'rules';
  /**
   * An opaque tag indicating the current version of the policySpec, used for
   * concurrency control. This field is ignored if used in a `CreatePolicy`
   * request. When the policy is returned from either a `GetPolicy` or a
   * `ListPolicies` request, this `etag` indicates the version of the current
   * policySpec to use when executing a read-modify-write loop. When the policy
   * is returned from a `GetEffectivePolicy` request, the `etag` will be unset.
   *
   * @var string
   */
  public $etag;
  /**
   * Determines the inheritance behavior for this policy. If
   * `inherit_from_parent` is true, policy rules set higher up in the hierarchy
   * (up to the closest root) are inherited and present in the effective policy.
   * If it is false, then no rules are inherited, and this policy becomes the
   * new root for evaluation. This field can be set only for policies which
   * configure list constraints.
   *
   * @var bool
   */
  public $inheritFromParent;
  /**
   * Ignores policies set above this resource and restores the
   * `constraint_default` enforcement behavior of the specific constraint at
   * this resource. This field can be set in policies for either list or boolean
   * constraints. If set, `rules` must be empty and `inherit_from_parent` must
   * be set to false.
   *
   * @var bool
   */
  public $reset;
  protected $rulesType = GoogleCloudOrgpolicyV2PolicySpecPolicyRule::class;
  protected $rulesDataType = 'array';
  /**
   * Output only. The time stamp this was previously updated. This represents
   * the last time a call to `CreatePolicy` or `UpdatePolicy` was made for that
   * policy.
   *
   * @var string
   */
  public $updateTime;

  /**
   * An opaque tag indicating the current version of the policySpec, used for
   * concurrency control. This field is ignored if used in a `CreatePolicy`
   * request. When the policy is returned from either a `GetPolicy` or a
   * `ListPolicies` request, this `etag` indicates the version of the current
   * policySpec to use when executing a read-modify-write loop. When the policy
   * is returned from a `GetEffectivePolicy` request, the `etag` will be unset.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Determines the inheritance behavior for this policy. If
   * `inherit_from_parent` is true, policy rules set higher up in the hierarchy
   * (up to the closest root) are inherited and present in the effective policy.
   * If it is false, then no rules are inherited, and this policy becomes the
   * new root for evaluation. This field can be set only for policies which
   * configure list constraints.
   *
   * @param bool $inheritFromParent
   */
  public function setInheritFromParent($inheritFromParent)
  {
    $this->inheritFromParent = $inheritFromParent;
  }
  /**
   * @return bool
   */
  public function getInheritFromParent()
  {
    return $this->inheritFromParent;
  }
  /**
   * Ignores policies set above this resource and restores the
   * `constraint_default` enforcement behavior of the specific constraint at
   * this resource. This field can be set in policies for either list or boolean
   * constraints. If set, `rules` must be empty and `inherit_from_parent` must
   * be set to false.
   *
   * @param bool $reset
   */
  public function setReset($reset)
  {
    $this->reset = $reset;
  }
  /**
   * @return bool
   */
  public function getReset()
  {
    return $this->reset;
  }
  /**
   * In policies for boolean constraints, the following requirements apply: -
   * There must be one and only one policy rule where condition is unset. -
   * Boolean policy rules with conditions must set `enforced` to the opposite of
   * the policy rule without a condition. - During policy evaluation, policy
   * rules with conditions that are true for a target resource take precedence.
   *
   * @param GoogleCloudOrgpolicyV2PolicySpecPolicyRule[] $rules
   */
  public function setRules($rules)
  {
    $this->rules = $rules;
  }
  /**
   * @return GoogleCloudOrgpolicyV2PolicySpecPolicyRule[]
   */
  public function getRules()
  {
    return $this->rules;
  }
  /**
   * Output only. The time stamp this was previously updated. This represents
   * the last time a call to `CreatePolicy` or `UpdatePolicy` was made for that
   * policy.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudOrgpolicyV2PolicySpec::class, 'Google_Service_PolicySimulator_GoogleCloudOrgpolicyV2PolicySpec');
