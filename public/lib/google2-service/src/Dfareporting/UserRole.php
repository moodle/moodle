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

namespace Google\Service\Dfareporting;

class UserRole extends \Google\Collection
{
  protected $collection_key = 'permissions';
  /**
   * Account ID of this user role. This is a read-only field that can be left
   * blank.
   *
   * @var string
   */
  public $accountId;
  /**
   * Whether this is a default user role. Default user roles are created by the
   * system for the account/subaccount and cannot be modified or deleted. Each
   * default user role comes with a basic set of preassigned permissions.
   *
   * @var bool
   */
  public $defaultUserRole;
  /**
   * ID of this user role. This is a read-only, auto-generated field.
   *
   * @var string
   */
  public $id;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#userRole".
   *
   * @var string
   */
  public $kind;
  /**
   * Name of this user role. This is a required field. Must be less than 256
   * characters long. If this user role is under a subaccount, the name must be
   * unique among sites of the same subaccount. Otherwise, this user role is a
   * top-level user role, and the name must be unique among top-level user roles
   * of the same account.
   *
   * @var string
   */
  public $name;
  /**
   * ID of the user role that this user role is based on or copied from. This is
   * a required field.
   *
   * @var string
   */
  public $parentUserRoleId;
  protected $permissionsType = UserRolePermission::class;
  protected $permissionsDataType = 'array';
  /**
   * Subaccount ID of this user role. This is a read-only field that can be left
   * blank.
   *
   * @var string
   */
  public $subaccountId;

  /**
   * Account ID of this user role. This is a read-only field that can be left
   * blank.
   *
   * @param string $accountId
   */
  public function setAccountId($accountId)
  {
    $this->accountId = $accountId;
  }
  /**
   * @return string
   */
  public function getAccountId()
  {
    return $this->accountId;
  }
  /**
   * Whether this is a default user role. Default user roles are created by the
   * system for the account/subaccount and cannot be modified or deleted. Each
   * default user role comes with a basic set of preassigned permissions.
   *
   * @param bool $defaultUserRole
   */
  public function setDefaultUserRole($defaultUserRole)
  {
    $this->defaultUserRole = $defaultUserRole;
  }
  /**
   * @return bool
   */
  public function getDefaultUserRole()
  {
    return $this->defaultUserRole;
  }
  /**
   * ID of this user role. This is a read-only, auto-generated field.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#userRole".
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
   * Name of this user role. This is a required field. Must be less than 256
   * characters long. If this user role is under a subaccount, the name must be
   * unique among sites of the same subaccount. Otherwise, this user role is a
   * top-level user role, and the name must be unique among top-level user roles
   * of the same account.
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
   * ID of the user role that this user role is based on or copied from. This is
   * a required field.
   *
   * @param string $parentUserRoleId
   */
  public function setParentUserRoleId($parentUserRoleId)
  {
    $this->parentUserRoleId = $parentUserRoleId;
  }
  /**
   * @return string
   */
  public function getParentUserRoleId()
  {
    return $this->parentUserRoleId;
  }
  /**
   * List of permissions associated with this user role.
   *
   * @param UserRolePermission[] $permissions
   */
  public function setPermissions($permissions)
  {
    $this->permissions = $permissions;
  }
  /**
   * @return UserRolePermission[]
   */
  public function getPermissions()
  {
    return $this->permissions;
  }
  /**
   * Subaccount ID of this user role. This is a read-only field that can be left
   * blank.
   *
   * @param string $subaccountId
   */
  public function setSubaccountId($subaccountId)
  {
    $this->subaccountId = $subaccountId;
  }
  /**
   * @return string
   */
  public function getSubaccountId()
  {
    return $this->subaccountId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UserRole::class, 'Google_Service_Dfareporting_UserRole');
