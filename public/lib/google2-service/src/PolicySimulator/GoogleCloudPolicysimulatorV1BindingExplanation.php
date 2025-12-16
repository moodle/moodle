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

class GoogleCloudPolicysimulatorV1BindingExplanation extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const ACCESS_ACCESS_STATE_UNSPECIFIED = 'ACCESS_STATE_UNSPECIFIED';
  /**
   * The principal has the permission.
   */
  public const ACCESS_GRANTED = 'GRANTED';
  /**
   * The principal does not have the permission.
   */
  public const ACCESS_NOT_GRANTED = 'NOT_GRANTED';
  /**
   * The principal has the permission only if a condition expression evaluates
   * to `true`.
   */
  public const ACCESS_UNKNOWN_CONDITIONAL = 'UNKNOWN_CONDITIONAL';
  /**
   * The user who created the Replay does not have access to all of the policies
   * that Policy Simulator needs to evaluate.
   */
  public const ACCESS_UNKNOWN_INFO_DENIED = 'UNKNOWN_INFO_DENIED';
  /**
   * Default value. This value is unused.
   */
  public const RELEVANCE_HEURISTIC_RELEVANCE_UNSPECIFIED = 'HEURISTIC_RELEVANCE_UNSPECIFIED';
  /**
   * The data point has a limited effect on the result. Changing the data point
   * is unlikely to affect the overall determination.
   */
  public const RELEVANCE_NORMAL = 'NORMAL';
  /**
   * The data point has a strong effect on the result. Changing the data point
   * is likely to affect the overall determination.
   */
  public const RELEVANCE_HIGH = 'HIGH';
  /**
   * Default value. This value is unused.
   */
  public const ROLE_PERMISSION_ROLE_PERMISSION_UNSPECIFIED = 'ROLE_PERMISSION_UNSPECIFIED';
  /**
   * The permission is included in the role.
   */
  public const ROLE_PERMISSION_ROLE_PERMISSION_INCLUDED = 'ROLE_PERMISSION_INCLUDED';
  /**
   * The permission is not included in the role.
   */
  public const ROLE_PERMISSION_ROLE_PERMISSION_NOT_INCLUDED = 'ROLE_PERMISSION_NOT_INCLUDED';
  /**
   * The user who created the Replay is not allowed to access the binding.
   */
  public const ROLE_PERMISSION_ROLE_PERMISSION_UNKNOWN_INFO_DENIED = 'ROLE_PERMISSION_UNKNOWN_INFO_DENIED';
  /**
   * Default value. This value is unused.
   */
  public const ROLE_PERMISSION_RELEVANCE_HEURISTIC_RELEVANCE_UNSPECIFIED = 'HEURISTIC_RELEVANCE_UNSPECIFIED';
  /**
   * The data point has a limited effect on the result. Changing the data point
   * is unlikely to affect the overall determination.
   */
  public const ROLE_PERMISSION_RELEVANCE_NORMAL = 'NORMAL';
  /**
   * The data point has a strong effect on the result. Changing the data point
   * is likely to affect the overall determination.
   */
  public const ROLE_PERMISSION_RELEVANCE_HIGH = 'HIGH';
  /**
   * Required. Indicates whether _this binding_ provides the specified
   * permission to the specified principal for the specified resource. This
   * field does _not_ indicate whether the principal actually has the permission
   * for the resource. There might be another binding that overrides this
   * binding. To determine whether the principal actually has the permission,
   * use the `access` field in the TroubleshootIamPolicyResponse.
   *
   * @var string
   */
  public $access;
  protected $conditionType = GoogleTypeExpr::class;
  protected $conditionDataType = '';
  protected $membershipsType = GoogleCloudPolicysimulatorV1BindingExplanationAnnotatedMembership::class;
  protected $membershipsDataType = 'map';
  /**
   * The relevance of this binding to the overall determination for the entire
   * policy.
   *
   * @var string
   */
  public $relevance;
  /**
   * The role that this binding grants. For example,
   * `roles/compute.serviceAgent`. For a complete list of predefined IAM roles,
   * as well as the permissions in each role, see
   * https://cloud.google.com/iam/help/roles/reference.
   *
   * @var string
   */
  public $role;
  /**
   * Indicates whether the role granted by this binding contains the specified
   * permission.
   *
   * @var string
   */
  public $rolePermission;
  /**
   * The relevance of the permission's existence, or nonexistence, in the role
   * to the overall determination for the entire policy.
   *
   * @var string
   */
  public $rolePermissionRelevance;

  /**
   * Required. Indicates whether _this binding_ provides the specified
   * permission to the specified principal for the specified resource. This
   * field does _not_ indicate whether the principal actually has the permission
   * for the resource. There might be another binding that overrides this
   * binding. To determine whether the principal actually has the permission,
   * use the `access` field in the TroubleshootIamPolicyResponse.
   *
   * Accepted values: ACCESS_STATE_UNSPECIFIED, GRANTED, NOT_GRANTED,
   * UNKNOWN_CONDITIONAL, UNKNOWN_INFO_DENIED
   *
   * @param self::ACCESS_* $access
   */
  public function setAccess($access)
  {
    $this->access = $access;
  }
  /**
   * @return self::ACCESS_*
   */
  public function getAccess()
  {
    return $this->access;
  }
  /**
   * A condition expression that prevents this binding from granting access
   * unless the expression evaluates to `true`. To learn about IAM Conditions,
   * see https://cloud.google.com/iam/docs/conditions-overview.
   *
   * @param GoogleTypeExpr $condition
   */
  public function setCondition(GoogleTypeExpr $condition)
  {
    $this->condition = $condition;
  }
  /**
   * @return GoogleTypeExpr
   */
  public function getCondition()
  {
    return $this->condition;
  }
  /**
   * Indicates whether each principal in the binding includes the principal
   * specified in the request, either directly or indirectly. Each key
   * identifies a principal in the binding, and each value indicates whether the
   * principal in the binding includes the principal in the request. For
   * example, suppose that a binding includes the following principals: *
   * `user:alice@example.com` * `group:product-eng@example.com` The principal in
   * the replayed access tuple is `user:bob@example.com`. This user is a
   * principal of the group `group:product-eng@example.com`. For the first
   * principal in the binding, the key is `user:alice@example.com`, and the
   * `membership` field in the value is set to `MEMBERSHIP_NOT_INCLUDED`. For
   * the second principal in the binding, the key is `group:product-
   * eng@example.com`, and the `membership` field in the value is set to
   * `MEMBERSHIP_INCLUDED`.
   *
   * @param GoogleCloudPolicysimulatorV1BindingExplanationAnnotatedMembership[] $memberships
   */
  public function setMemberships($memberships)
  {
    $this->memberships = $memberships;
  }
  /**
   * @return GoogleCloudPolicysimulatorV1BindingExplanationAnnotatedMembership[]
   */
  public function getMemberships()
  {
    return $this->memberships;
  }
  /**
   * The relevance of this binding to the overall determination for the entire
   * policy.
   *
   * Accepted values: HEURISTIC_RELEVANCE_UNSPECIFIED, NORMAL, HIGH
   *
   * @param self::RELEVANCE_* $relevance
   */
  public function setRelevance($relevance)
  {
    $this->relevance = $relevance;
  }
  /**
   * @return self::RELEVANCE_*
   */
  public function getRelevance()
  {
    return $this->relevance;
  }
  /**
   * The role that this binding grants. For example,
   * `roles/compute.serviceAgent`. For a complete list of predefined IAM roles,
   * as well as the permissions in each role, see
   * https://cloud.google.com/iam/help/roles/reference.
   *
   * @param string $role
   */
  public function setRole($role)
  {
    $this->role = $role;
  }
  /**
   * @return string
   */
  public function getRole()
  {
    return $this->role;
  }
  /**
   * Indicates whether the role granted by this binding contains the specified
   * permission.
   *
   * Accepted values: ROLE_PERMISSION_UNSPECIFIED, ROLE_PERMISSION_INCLUDED,
   * ROLE_PERMISSION_NOT_INCLUDED, ROLE_PERMISSION_UNKNOWN_INFO_DENIED
   *
   * @param self::ROLE_PERMISSION_* $rolePermission
   */
  public function setRolePermission($rolePermission)
  {
    $this->rolePermission = $rolePermission;
  }
  /**
   * @return self::ROLE_PERMISSION_*
   */
  public function getRolePermission()
  {
    return $this->rolePermission;
  }
  /**
   * The relevance of the permission's existence, or nonexistence, in the role
   * to the overall determination for the entire policy.
   *
   * Accepted values: HEURISTIC_RELEVANCE_UNSPECIFIED, NORMAL, HIGH
   *
   * @param self::ROLE_PERMISSION_RELEVANCE_* $rolePermissionRelevance
   */
  public function setRolePermissionRelevance($rolePermissionRelevance)
  {
    $this->rolePermissionRelevance = $rolePermissionRelevance;
  }
  /**
   * @return self::ROLE_PERMISSION_RELEVANCE_*
   */
  public function getRolePermissionRelevance()
  {
    return $this->rolePermissionRelevance;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudPolicysimulatorV1BindingExplanation::class, 'Google_Service_PolicySimulator_GoogleCloudPolicysimulatorV1BindingExplanation');
