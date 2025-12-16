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

class GoogleCloudOrgpolicyV1ListPolicy extends \Google\Collection
{
  /**
   * Indicates that allowed_values or denied_values must be set.
   */
  public const ALL_VALUES_ALL_VALUES_UNSPECIFIED = 'ALL_VALUES_UNSPECIFIED';
  /**
   * A policy with this set allows all values.
   */
  public const ALL_VALUES_ALLOW = 'ALLOW';
  /**
   * A policy with this set denies all values.
   */
  public const ALL_VALUES_DENY = 'DENY';
  protected $collection_key = 'deniedValues';
  /**
   * The policy all_values state.
   *
   * @var string
   */
  public $allValues;
  /**
   * List of values allowed at this resource. Can only be set if `all_values` is
   * set to `ALL_VALUES_UNSPECIFIED`.
   *
   * @var string[]
   */
  public $allowedValues;
  /**
   * List of values denied at this resource. Can only be set if `all_values` is
   * set to `ALL_VALUES_UNSPECIFIED`.
   *
   * @var string[]
   */
  public $deniedValues;
  /**
   * Determines the inheritance behavior for this `Policy`. By default, a
   * `ListPolicy` set at a resource supersedes any `Policy` set anywhere up the
   * resource hierarchy. However, if `inherit_from_parent` is set to `true`,
   * then the values from the effective `Policy` of the parent resource are
   * inherited, meaning the values set in this `Policy` are added to the values
   * inherited up the hierarchy. Setting `Policy` hierarchies that inherit both
   * allowed values and denied values isn't recommended in most circumstances to
   * keep the configuration simple and understandable. However, it is possible
   * to set a `Policy` with `allowed_values` set that inherits a `Policy` with
   * `denied_values` set. In this case, the values that are allowed must be in
   * `allowed_values` and not present in `denied_values`. For example, suppose
   * you have a `Constraint` `constraints/serviceuser.services`, which has a
   * `constraint_type` of `list_constraint`, and with `constraint_default` set
   * to `ALLOW`. Suppose that at the Organization level, a `Policy` is applied
   * that restricts the allowed API activations to {`E1`, `E2`}. Then, if a
   * `Policy` is applied to a project below the Organization that has
   * `inherit_from_parent` set to `false` and field all_values set to DENY, then
   * an attempt to activate any API will be denied. The following examples
   * demonstrate different possible layerings for `projects/bar` parented by
   * `organizations/foo`: Example 1 (no inherited values): `organizations/foo`
   * has a `Policy` with values: {allowed_values: "E1" allowed_values:"E2"}
   * `projects/bar` has `inherit_from_parent` `false` and values:
   * {allowed_values: "E3" allowed_values: "E4"} The accepted values at
   * `organizations/foo` are `E1`, `E2`. The accepted values at `projects/bar`
   * are `E3`, and `E4`. Example 2 (inherited values): `organizations/foo` has a
   * `Policy` with values: {allowed_values: "E1" allowed_values:"E2"}
   * `projects/bar` has a `Policy` with values: {value: "E3" value: "E4"
   * inherit_from_parent: true} The accepted values at `organizations/foo` are
   * `E1`, `E2`. The accepted values at `projects/bar` are `E1`, `E2`, `E3`, and
   * `E4`. Example 3 (inheriting both allowed and denied values):
   * `organizations/foo` has a `Policy` with values: {allowed_values: "E1"
   * allowed_values: "E2"} `projects/bar` has a `Policy` with: {denied_values:
   * "E1"} The accepted values at `organizations/foo` are `E1`, `E2`. The value
   * accepted at `projects/bar` is `E2`. Example 4 (RestoreDefault):
   * `organizations/foo` has a `Policy` with values: {allowed_values: "E1"
   * allowed_values:"E2"} `projects/bar` has a `Policy` with values:
   * {RestoreDefault: {}} The accepted values at `organizations/foo` are `E1`,
   * `E2`. The accepted values at `projects/bar` are either all or none
   * depending on the value of `constraint_default` (if `ALLOW`, all; if `DENY`,
   * none). Example 5 (no policy inherits parent policy): `organizations/foo`
   * has no `Policy` set. `projects/bar` has no `Policy` set. The accepted
   * values at both levels are either all or none depending on the value of
   * `constraint_default` (if `ALLOW`, all; if `DENY`, none). Example 6
   * (ListConstraint allowing all): `organizations/foo` has a `Policy` with
   * values: {allowed_values: "E1" allowed_values: "E2"} `projects/bar` has a
   * `Policy` with: {all: ALLOW} The accepted values at `organizations/foo` are
   * `E1`, E2`. Any value is accepted at `projects/bar`. Example 7
   * (ListConstraint allowing none): `organizations/foo` has a `Policy` with
   * values: {allowed_values: "E1" allowed_values: "E2"} `projects/bar` has a
   * `Policy` with: {all: DENY} The accepted values at `organizations/foo` are
   * `E1`, E2`. No value is accepted at `projects/bar`. Example 10 (allowed and
   * denied subtrees of Resource Manager hierarchy): Given the following
   * resource hierarchy O1->{F1, F2}; F1->{P1}; F2->{P2, P3},
   * `organizations/foo` has a `Policy` with values: {allowed_values:
   * "under:organizations/O1"} `projects/bar` has a `Policy` with:
   * {allowed_values: "under:projects/P3"} {denied_values: "under:folders/F2"}
   * The accepted values at `organizations/foo` are `organizations/O1`,
   * `folders/F1`, `folders/F2`, `projects/P1`, `projects/P2`, `projects/P3`.
   * The accepted values at `projects/bar` are `organizations/O1`, `folders/F1`,
   * `projects/P1`.
   *
   * @var bool
   */
  public $inheritFromParent;
  /**
   * Optional. The Google Cloud Console will try to default to a configuration
   * that matches the value specified in this `Policy`. If `suggested_value` is
   * not set, it will inherit the value specified higher in the hierarchy,
   * unless `inherit_from_parent` is `false`.
   *
   * @var string
   */
  public $suggestedValue;

  /**
   * The policy all_values state.
   *
   * Accepted values: ALL_VALUES_UNSPECIFIED, ALLOW, DENY
   *
   * @param self::ALL_VALUES_* $allValues
   */
  public function setAllValues($allValues)
  {
    $this->allValues = $allValues;
  }
  /**
   * @return self::ALL_VALUES_*
   */
  public function getAllValues()
  {
    return $this->allValues;
  }
  /**
   * List of values allowed at this resource. Can only be set if `all_values` is
   * set to `ALL_VALUES_UNSPECIFIED`.
   *
   * @param string[] $allowedValues
   */
  public function setAllowedValues($allowedValues)
  {
    $this->allowedValues = $allowedValues;
  }
  /**
   * @return string[]
   */
  public function getAllowedValues()
  {
    return $this->allowedValues;
  }
  /**
   * List of values denied at this resource. Can only be set if `all_values` is
   * set to `ALL_VALUES_UNSPECIFIED`.
   *
   * @param string[] $deniedValues
   */
  public function setDeniedValues($deniedValues)
  {
    $this->deniedValues = $deniedValues;
  }
  /**
   * @return string[]
   */
  public function getDeniedValues()
  {
    return $this->deniedValues;
  }
  /**
   * Determines the inheritance behavior for this `Policy`. By default, a
   * `ListPolicy` set at a resource supersedes any `Policy` set anywhere up the
   * resource hierarchy. However, if `inherit_from_parent` is set to `true`,
   * then the values from the effective `Policy` of the parent resource are
   * inherited, meaning the values set in this `Policy` are added to the values
   * inherited up the hierarchy. Setting `Policy` hierarchies that inherit both
   * allowed values and denied values isn't recommended in most circumstances to
   * keep the configuration simple and understandable. However, it is possible
   * to set a `Policy` with `allowed_values` set that inherits a `Policy` with
   * `denied_values` set. In this case, the values that are allowed must be in
   * `allowed_values` and not present in `denied_values`. For example, suppose
   * you have a `Constraint` `constraints/serviceuser.services`, which has a
   * `constraint_type` of `list_constraint`, and with `constraint_default` set
   * to `ALLOW`. Suppose that at the Organization level, a `Policy` is applied
   * that restricts the allowed API activations to {`E1`, `E2`}. Then, if a
   * `Policy` is applied to a project below the Organization that has
   * `inherit_from_parent` set to `false` and field all_values set to DENY, then
   * an attempt to activate any API will be denied. The following examples
   * demonstrate different possible layerings for `projects/bar` parented by
   * `organizations/foo`: Example 1 (no inherited values): `organizations/foo`
   * has a `Policy` with values: {allowed_values: "E1" allowed_values:"E2"}
   * `projects/bar` has `inherit_from_parent` `false` and values:
   * {allowed_values: "E3" allowed_values: "E4"} The accepted values at
   * `organizations/foo` are `E1`, `E2`. The accepted values at `projects/bar`
   * are `E3`, and `E4`. Example 2 (inherited values): `organizations/foo` has a
   * `Policy` with values: {allowed_values: "E1" allowed_values:"E2"}
   * `projects/bar` has a `Policy` with values: {value: "E3" value: "E4"
   * inherit_from_parent: true} The accepted values at `organizations/foo` are
   * `E1`, `E2`. The accepted values at `projects/bar` are `E1`, `E2`, `E3`, and
   * `E4`. Example 3 (inheriting both allowed and denied values):
   * `organizations/foo` has a `Policy` with values: {allowed_values: "E1"
   * allowed_values: "E2"} `projects/bar` has a `Policy` with: {denied_values:
   * "E1"} The accepted values at `organizations/foo` are `E1`, `E2`. The value
   * accepted at `projects/bar` is `E2`. Example 4 (RestoreDefault):
   * `organizations/foo` has a `Policy` with values: {allowed_values: "E1"
   * allowed_values:"E2"} `projects/bar` has a `Policy` with values:
   * {RestoreDefault: {}} The accepted values at `organizations/foo` are `E1`,
   * `E2`. The accepted values at `projects/bar` are either all or none
   * depending on the value of `constraint_default` (if `ALLOW`, all; if `DENY`,
   * none). Example 5 (no policy inherits parent policy): `organizations/foo`
   * has no `Policy` set. `projects/bar` has no `Policy` set. The accepted
   * values at both levels are either all or none depending on the value of
   * `constraint_default` (if `ALLOW`, all; if `DENY`, none). Example 6
   * (ListConstraint allowing all): `organizations/foo` has a `Policy` with
   * values: {allowed_values: "E1" allowed_values: "E2"} `projects/bar` has a
   * `Policy` with: {all: ALLOW} The accepted values at `organizations/foo` are
   * `E1`, E2`. Any value is accepted at `projects/bar`. Example 7
   * (ListConstraint allowing none): `organizations/foo` has a `Policy` with
   * values: {allowed_values: "E1" allowed_values: "E2"} `projects/bar` has a
   * `Policy` with: {all: DENY} The accepted values at `organizations/foo` are
   * `E1`, E2`. No value is accepted at `projects/bar`. Example 10 (allowed and
   * denied subtrees of Resource Manager hierarchy): Given the following
   * resource hierarchy O1->{F1, F2}; F1->{P1}; F2->{P2, P3},
   * `organizations/foo` has a `Policy` with values: {allowed_values:
   * "under:organizations/O1"} `projects/bar` has a `Policy` with:
   * {allowed_values: "under:projects/P3"} {denied_values: "under:folders/F2"}
   * The accepted values at `organizations/foo` are `organizations/O1`,
   * `folders/F1`, `folders/F2`, `projects/P1`, `projects/P2`, `projects/P3`.
   * The accepted values at `projects/bar` are `organizations/O1`, `folders/F1`,
   * `projects/P1`.
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
   * Optional. The Google Cloud Console will try to default to a configuration
   * that matches the value specified in this `Policy`. If `suggested_value` is
   * not set, it will inherit the value specified higher in the hierarchy,
   * unless `inherit_from_parent` is `false`.
   *
   * @param string $suggestedValue
   */
  public function setSuggestedValue($suggestedValue)
  {
    $this->suggestedValue = $suggestedValue;
  }
  /**
   * @return string
   */
  public function getSuggestedValue()
  {
    return $this->suggestedValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudOrgpolicyV1ListPolicy::class, 'Google_Service_CloudAsset_GoogleCloudOrgpolicyV1ListPolicy');
