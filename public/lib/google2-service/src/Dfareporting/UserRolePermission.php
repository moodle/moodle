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

class UserRolePermission extends \Google\Model
{
  /**
   * Not available by default.
   */
  public const AVAILABILITY_NOT_AVAILABLE_BY_DEFAULT = 'NOT_AVAILABLE_BY_DEFAULT';
  /**
   * Available by default to accounts only.
   */
  public const AVAILABILITY_ACCOUNT_BY_DEFAULT = 'ACCOUNT_BY_DEFAULT';
  /**
   * Available by default to both accounts and subaccounts.
   */
  public const AVAILABILITY_SUBACCOUNT_AND_ACCOUNT_BY_DEFAULT = 'SUBACCOUNT_AND_ACCOUNT_BY_DEFAULT';
  /**
   * Always available to accounts.
   */
  public const AVAILABILITY_ACCOUNT_ALWAYS = 'ACCOUNT_ALWAYS';
  /**
   * Always available to both accounts and subaccounts.
   */
  public const AVAILABILITY_SUBACCOUNT_AND_ACCOUNT_ALWAYS = 'SUBACCOUNT_AND_ACCOUNT_ALWAYS';
  /**
   * Available for user profile permissions only.
   */
  public const AVAILABILITY_USER_PROFILE_ONLY = 'USER_PROFILE_ONLY';
  /**
   * Levels of availability for a user role permission.
   *
   * @var string
   */
  public $availability;
  /**
   * ID of this user role permission.
   *
   * @var string
   */
  public $id;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#userRolePermission".
   *
   * @var string
   */
  public $kind;
  /**
   * Name of this user role permission.
   *
   * @var string
   */
  public $name;
  /**
   * ID of the permission group that this user role permission belongs to.
   *
   * @var string
   */
  public $permissionGroupId;

  /**
   * Levels of availability for a user role permission.
   *
   * Accepted values: NOT_AVAILABLE_BY_DEFAULT, ACCOUNT_BY_DEFAULT,
   * SUBACCOUNT_AND_ACCOUNT_BY_DEFAULT, ACCOUNT_ALWAYS,
   * SUBACCOUNT_AND_ACCOUNT_ALWAYS, USER_PROFILE_ONLY
   *
   * @param self::AVAILABILITY_* $availability
   */
  public function setAvailability($availability)
  {
    $this->availability = $availability;
  }
  /**
   * @return self::AVAILABILITY_*
   */
  public function getAvailability()
  {
    return $this->availability;
  }
  /**
   * ID of this user role permission.
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
   * "dfareporting#userRolePermission".
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
   * Name of this user role permission.
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
   * ID of the permission group that this user role permission belongs to.
   *
   * @param string $permissionGroupId
   */
  public function setPermissionGroupId($permissionGroupId)
  {
    $this->permissionGroupId = $permissionGroupId;
  }
  /**
   * @return string
   */
  public function getPermissionGroupId()
  {
    return $this->permissionGroupId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UserRolePermission::class, 'Google_Service_Dfareporting_UserRolePermission');
