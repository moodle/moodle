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

namespace Google\Service\Keep;

class Permission extends \Google\Model
{
  /**
   * An undefined role.
   */
  public const ROLE_ROLE_UNSPECIFIED = 'ROLE_UNSPECIFIED';
  /**
   * A role granting full access. This role cannot be added or removed. Defined
   * by the creator of the note.
   */
  public const ROLE_OWNER = 'OWNER';
  /**
   * A role granting the ability to contribute content and modify note
   * permissions.
   */
  public const ROLE_WRITER = 'WRITER';
  /**
   * Output only. Whether this member has been deleted. If the member is
   * recovered, this value is set to false and the recovered member retains the
   * role on the note.
   *
   * @var bool
   */
  public $deleted;
  /**
   * The email associated with the member. If set on create, the `email` field
   * in the `User` or `Group` message must either be empty or match this field.
   * On read, may be unset if the member does not have an associated email.
   *
   * @var string
   */
  public $email;
  protected $familyType = Family::class;
  protected $familyDataType = '';
  protected $groupType = Group::class;
  protected $groupDataType = '';
  /**
   * Output only. The resource name.
   *
   * @var string
   */
  public $name;
  /**
   * The role granted by this permission. The role determines the entity’s
   * ability to read, write, and share notes.
   *
   * @var string
   */
  public $role;
  protected $userType = User::class;
  protected $userDataType = '';

  /**
   * Output only. Whether this member has been deleted. If the member is
   * recovered, this value is set to false and the recovered member retains the
   * role on the note.
   *
   * @param bool $deleted
   */
  public function setDeleted($deleted)
  {
    $this->deleted = $deleted;
  }
  /**
   * @return bool
   */
  public function getDeleted()
  {
    return $this->deleted;
  }
  /**
   * The email associated with the member. If set on create, the `email` field
   * in the `User` or `Group` message must either be empty or match this field.
   * On read, may be unset if the member does not have an associated email.
   *
   * @param string $email
   */
  public function setEmail($email)
  {
    $this->email = $email;
  }
  /**
   * @return string
   */
  public function getEmail()
  {
    return $this->email;
  }
  /**
   * Output only. The Google Family to which this role applies.
   *
   * @param Family $family
   */
  public function setFamily(Family $family)
  {
    $this->family = $family;
  }
  /**
   * @return Family
   */
  public function getFamily()
  {
    return $this->family;
  }
  /**
   * Output only. The group to which this role applies.
   *
   * @param Group $group
   */
  public function setGroup(Group $group)
  {
    $this->group = $group;
  }
  /**
   * @return Group
   */
  public function getGroup()
  {
    return $this->group;
  }
  /**
   * Output only. The resource name.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * The role granted by this permission. The role determines the entity’s
   * ability to read, write, and share notes.
   *
   * Accepted values: ROLE_UNSPECIFIED, OWNER, WRITER
   *
   * @param self::ROLE_* $role
   */
  public function setRole($role)
  {
    $this->role = $role;
  }
  /**
   * @return self::ROLE_*
   */
  public function getRole()
  {
    return $this->role;
  }
  /**
   * Output only. The user to whom this role applies.
   *
   * @param User $user
   */
  public function setUser(User $user)
  {
    $this->user = $user;
  }
  /**
   * @return User
   */
  public function getUser()
  {
    return $this->user;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Permission::class, 'Google_Service_Keep_Permission');
