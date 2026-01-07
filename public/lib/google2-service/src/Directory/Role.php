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

class Role extends \Google\Collection
{
  protected $collection_key = 'rolePrivileges';
  /**
   * ETag of the resource.
   *
   * @var string
   */
  public $etag;
  /**
   * Returns `true` if the role is a super admin role.
   *
   * @var bool
   */
  public $isSuperAdminRole;
  /**
   * Returns `true` if this is a pre-defined system role.
   *
   * @var bool
   */
  public $isSystemRole;
  /**
   * The type of the API resource. This is always `admin#directory#role`.
   *
   * @var string
   */
  public $kind;
  /**
   * A short description of the role.
   *
   * @var string
   */
  public $roleDescription;
  /**
   * ID of the role.
   *
   * @var string
   */
  public $roleId;
  /**
   * Name of the role.
   *
   * @var string
   */
  public $roleName;
  protected $rolePrivilegesType = RoleRolePrivileges::class;
  protected $rolePrivilegesDataType = 'array';

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
   * Returns `true` if the role is a super admin role.
   *
   * @param bool $isSuperAdminRole
   */
  public function setIsSuperAdminRole($isSuperAdminRole)
  {
    $this->isSuperAdminRole = $isSuperAdminRole;
  }
  /**
   * @return bool
   */
  public function getIsSuperAdminRole()
  {
    return $this->isSuperAdminRole;
  }
  /**
   * Returns `true` if this is a pre-defined system role.
   *
   * @param bool $isSystemRole
   */
  public function setIsSystemRole($isSystemRole)
  {
    $this->isSystemRole = $isSystemRole;
  }
  /**
   * @return bool
   */
  public function getIsSystemRole()
  {
    return $this->isSystemRole;
  }
  /**
   * The type of the API resource. This is always `admin#directory#role`.
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
   * A short description of the role.
   *
   * @param string $roleDescription
   */
  public function setRoleDescription($roleDescription)
  {
    $this->roleDescription = $roleDescription;
  }
  /**
   * @return string
   */
  public function getRoleDescription()
  {
    return $this->roleDescription;
  }
  /**
   * ID of the role.
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
   * Name of the role.
   *
   * @param string $roleName
   */
  public function setRoleName($roleName)
  {
    $this->roleName = $roleName;
  }
  /**
   * @return string
   */
  public function getRoleName()
  {
    return $this->roleName;
  }
  /**
   * The set of privileges that are granted to this role.
   *
   * @param RoleRolePrivileges[] $rolePrivileges
   */
  public function setRolePrivileges($rolePrivileges)
  {
    $this->rolePrivileges = $rolePrivileges;
  }
  /**
   * @return RoleRolePrivileges[]
   */
  public function getRolePrivileges()
  {
    return $this->rolePrivileges;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Role::class, 'Google_Service_Directory_Role');
