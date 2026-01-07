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

namespace Google\Service\CloudIdentity;

class MembershipRelation extends \Google\Collection
{
  protected $collection_key = 'roles';
  /**
   * An extended description to help users determine the purpose of a `Group`.
   *
   * @var string
   */
  public $description;
  /**
   * The display name of the `Group`.
   *
   * @var string
   */
  public $displayName;
  /**
   * The [resource name](https://cloud.google.com/apis/design/resource_names) of
   * the `Group`. Shall be of the form `groups/{group_id}`.
   *
   * @var string
   */
  public $group;
  protected $groupKeyType = EntityKey::class;
  protected $groupKeyDataType = '';
  /**
   * One or more label entries that apply to the Group. Currently supported
   * labels contain a key with an empty value.
   *
   * @var string[]
   */
  public $labels;
  /**
   * The [resource name](https://cloud.google.com/apis/design/resource_names) of
   * the `Membership`. Shall be of the form
   * `groups/{group_id}/memberships/{membership_id}`.
   *
   * @var string
   */
  public $membership;
  protected $rolesType = MembershipRole::class;
  protected $rolesDataType = 'array';

  /**
   * An extended description to help users determine the purpose of a `Group`.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * The display name of the `Group`.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * The [resource name](https://cloud.google.com/apis/design/resource_names) of
   * the `Group`. Shall be of the form `groups/{group_id}`.
   *
   * @param string $group
   */
  public function setGroup($group)
  {
    $this->group = $group;
  }
  /**
   * @return string
   */
  public function getGroup()
  {
    return $this->group;
  }
  /**
   * The `EntityKey` of the `Group`.
   *
   * @param EntityKey $groupKey
   */
  public function setGroupKey(EntityKey $groupKey)
  {
    $this->groupKey = $groupKey;
  }
  /**
   * @return EntityKey
   */
  public function getGroupKey()
  {
    return $this->groupKey;
  }
  /**
   * One or more label entries that apply to the Group. Currently supported
   * labels contain a key with an empty value.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * The [resource name](https://cloud.google.com/apis/design/resource_names) of
   * the `Membership`. Shall be of the form
   * `groups/{group_id}/memberships/{membership_id}`.
   *
   * @param string $membership
   */
  public function setMembership($membership)
  {
    $this->membership = $membership;
  }
  /**
   * @return string
   */
  public function getMembership()
  {
    return $this->membership;
  }
  /**
   * The `MembershipRole`s that apply to the `Membership`.
   *
   * @param MembershipRole[] $roles
   */
  public function setRoles($roles)
  {
    $this->roles = $roles;
  }
  /**
   * @return MembershipRole[]
   */
  public function getRoles()
  {
    return $this->roles;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MembershipRelation::class, 'Google_Service_CloudIdentity_MembershipRelation');
