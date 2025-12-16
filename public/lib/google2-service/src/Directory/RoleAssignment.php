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

namespace Google\Service\Directory;

class RoleAssignment extends \Google\Model
{
  /**
   * An individual user within the domain.
   */
  public const ASSIGNEE_TYPE_user = 'user';
  /**
   * A group within the domain.
   */
  public const ASSIGNEE_TYPE_group = 'group';
  /**
   * The unique ID of the entity this role is assigned to—either the `user_id`
   * of a user, the `group_id` of a group, or the `uniqueId` of a service
   * account as defined in [Identity and Access Management (IAM)](https://cloud.
   * google.com/iam/docs/reference/rest/v1/projects.serviceAccounts).
   *
   * @var string
   */
  public $assignedTo;
  /**
   * Output only. The type of the assignee (`USER` or `GROUP`).
   *
   * @var string
   */
  public $assigneeType;
  /**
   * Optional. The condition associated with this role assignment. Note: Feature
   * is available to Enterprise Standard, Enterprise Plus, Google Workspace for
   * Education Plus and Cloud Identity Premium customers. A `RoleAssignment`
   * with the `condition` field set will only take effect when the resource
   * being accessed meets the condition. If `condition` is empty, the role
   * (`role_id`) is applied to the actor (`assigned_to`) at the scope
   * (`scope_type`) unconditionally. Currently, the following conditions are
   * supported: - To make the `RoleAssignment` only applicable to [Security
   * Groups](https://cloud.google.com/identity/docs/groups#group_types):
   * `api.getAttribute('cloudidentity.googleapis.com/groups.labels',
   * []).hasAny(['groups.security']) && resource.type ==
   * 'cloudidentity.googleapis.com/Group'` - To make the `RoleAssignment` not
   * applicable to [Security
   * Groups](https://cloud.google.com/identity/docs/groups#group_types):
   * `!api.getAttribute('cloudidentity.googleapis.com/groups.labels',
   * []).hasAny(['groups.security']) && resource.type ==
   * 'cloudidentity.googleapis.com/Group'` Currently, the condition strings have
   * to be verbatim and they only work with the following [pre-built
   * administrator roles](https://support.google.com/a/answer/2405986): - Groups
   * Editor - Groups Reader The condition follows [Cloud IAM condition
   * syntax](https://cloud.google.com/iam/docs/conditions-overview). - To make
   * the `RoleAssignment` not applicable to [Locked
   * Groups](https://cloud.google.com/identity/docs/groups#group_types):
   * `!api.getAttribute('cloudidentity.googleapis.com/groups.labels',
   * []).hasAny(['groups.locked']) && resource.type ==
   * 'cloudidentity.googleapis.com/Group'` This condition can also be used in
   * conjunction with a Security-related condition.
   *
   * @var string
   */
  public $condition;
  /**
   * ETag of the resource.
   *
   * @var string
   */
  public $etag;
  /**
   * The type of the API resource. This is always
   * `admin#directory#roleAssignment`.
   *
   * @var string
   */
  public $kind;
  /**
   * If the role is restricted to an organization unit, this contains the ID for
   * the organization unit the exercise of this role is restricted to.
   *
   * @var string
   */
  public $orgUnitId;
  /**
   * ID of this roleAssignment.
   *
   * @var string
   */
  public $roleAssignmentId;
  /**
   * The ID of the role that is assigned.
   *
   * @var string
   */
  public $roleId;
  /**
   * The scope in which this role is assigned.
   *
   * @var string
   */
  public $scopeType;

  /**
   * The unique ID of the entity this role is assigned to—either the `user_id`
   * of a user, the `group_id` of a group, or the `uniqueId` of a service
   * account as defined in [Identity and Access Management (IAM)](https://cloud.
   * google.com/iam/docs/reference/rest/v1/projects.serviceAccounts).
   *
   * @param string $assignedTo
   */
  public function setAssignedTo($assignedTo)
  {
    $this->assignedTo = $assignedTo;
  }
  /**
   * @return string
   */
  public function getAssignedTo()
  {
    return $this->assignedTo;
  }
  /**
   * Output only. The type of the assignee (`USER` or `GROUP`).
   *
   * Accepted values: user, group
   *
   * @param self::ASSIGNEE_TYPE_* $assigneeType
   */
  public function setAssigneeType($assigneeType)
  {
    $this->assigneeType = $assigneeType;
  }
  /**
   * @return self::ASSIGNEE_TYPE_*
   */
  public function getAssigneeType()
  {
    return $this->assigneeType;
  }
  /**
   * Optional. The condition associated with this role assignment. Note: Feature
   * is available to Enterprise Standard, Enterprise Plus, Google Workspace for
   * Education Plus and Cloud Identity Premium customers. A `RoleAssignment`
   * with the `condition` field set will only take effect when the resource
   * being accessed meets the condition. If `condition` is empty, the role
   * (`role_id`) is applied to the actor (`assigned_to`) at the scope
   * (`scope_type`) unconditionally. Currently, the following conditions are
   * supported: - To make the `RoleAssignment` only applicable to [Security
   * Groups](https://cloud.google.com/identity/docs/groups#group_types):
   * `api.getAttribute('cloudidentity.googleapis.com/groups.labels',
   * []).hasAny(['groups.security']) && resource.type ==
   * 'cloudidentity.googleapis.com/Group'` - To make the `RoleAssignment` not
   * applicable to [Security
   * Groups](https://cloud.google.com/identity/docs/groups#group_types):
   * `!api.getAttribute('cloudidentity.googleapis.com/groups.labels',
   * []).hasAny(['groups.security']) && resource.type ==
   * 'cloudidentity.googleapis.com/Group'` Currently, the condition strings have
   * to be verbatim and they only work with the following [pre-built
   * administrator roles](https://support.google.com/a/answer/2405986): - Groups
   * Editor - Groups Reader The condition follows [Cloud IAM condition
   * syntax](https://cloud.google.com/iam/docs/conditions-overview). - To make
   * the `RoleAssignment` not applicable to [Locked
   * Groups](https://cloud.google.com/identity/docs/groups#group_types):
   * `!api.getAttribute('cloudidentity.googleapis.com/groups.labels',
   * []).hasAny(['groups.locked']) && resource.type ==
   * 'cloudidentity.googleapis.com/Group'` This condition can also be used in
   * conjunction with a Security-related condition.
   *
   * @param string $condition
   */
  public function setCondition($condition)
  {
    $this->condition = $condition;
  }
  /**
   * @return string
   */
  public function getCondition()
  {
    return $this->condition;
  }
  /**
   * ETag of the resource.
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
   * The type of the API resource. This is always
   * `admin#directory#roleAssignment`.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * If the role is restricted to an organization unit, this contains the ID for
   * the organization unit the exercise of this role is restricted to.
   *
   * @param string $orgUnitId
   */
  public function setOrgUnitId($orgUnitId)
  {
    $this->orgUnitId = $orgUnitId;
  }
  /**
   * @return string
   */
  public function getOrgUnitId()
  {
    return $this->orgUnitId;
  }
  /**
   * ID of this roleAssignment.
   *
   * @param string $roleAssignmentId
   */
  public function setRoleAssignmentId($roleAssignmentId)
  {
    $this->roleAssignmentId = $roleAssignmentId;
  }
  /**
   * @return string
   */
  public function getRoleAssignmentId()
  {
    return $this->roleAssignmentId;
  }
  /**
   * The ID of the role that is assigned.
   *
   * @param string $roleId
   */
  public function setRoleId($roleId)
  {
    $this->roleId = $roleId;
  }
  /**
   * @return string
   */
  public function getRoleId()
  {
    return $this->roleId;
  }
  /**
   * The scope in which this role is assigned.
   *
   * @param string $scopeType
   */
  public function setScopeType($scopeType)
  {
    $this->scopeType = $scopeType;
  }
  /**
   * @return string
   */
  public function getScopeType()
  {
    return $this->scopeType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RoleAssignment::class, 'Google_Service_Directory_RoleAssignment');
