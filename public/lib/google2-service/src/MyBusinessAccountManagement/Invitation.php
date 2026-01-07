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

namespace Google\Service\MyBusinessAccountManagement;

class Invitation extends \Google\Model
{
  /**
   * Not specified.
   */
  public const ROLE_ADMIN_ROLE_UNSPECIFIED = 'ADMIN_ROLE_UNSPECIFIED';
  /**
   * The admin has owner-level access and is the primary owner. (Displays as
   * 'Primary Owner' in UI).
   */
  public const ROLE_PRIMARY_OWNER = 'PRIMARY_OWNER';
  /**
   * The admin has owner-level access. (Displays as 'Owner' in UI).
   */
  public const ROLE_OWNER = 'OWNER';
  /**
   * The admin has managerial access.
   */
  public const ROLE_MANAGER = 'MANAGER';
  /**
   * The admin can manage social (Google+) pages. (Displays as 'Site Manager' in
   * UI). This API doesn't allow creating an account admin with a SITE_MANAGER
   * role.
   */
  public const ROLE_SITE_MANAGER = 'SITE_MANAGER';
  /**
   * Set when target type is unspecified.
   */
  public const TARGET_TYPE_TARGET_TYPE_UNSPECIFIED = 'TARGET_TYPE_UNSPECIFIED';
  /**
   * List invitations only for targets of type Account.
   */
  public const TARGET_TYPE_ACCOUNTS_ONLY = 'ACCOUNTS_ONLY';
  /**
   * List invitations only for targets of type Location.
   */
  public const TARGET_TYPE_LOCATIONS_ONLY = 'LOCATIONS_ONLY';
  /**
   * Required. The resource name for the invitation.
   * `accounts/{account_id}/invitations/{invitation_id}`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The invited role on the account.
   *
   * @var string
   */
  public $role;
  protected $targetAccountType = Account::class;
  protected $targetAccountDataType = '';
  protected $targetLocationType = TargetLocation::class;
  protected $targetLocationDataType = '';
  /**
   * Output only. Specifies which target types should appear in the response.
   *
   * @var string
   */
  public $targetType;

  /**
   * Required. The resource name for the invitation.
   * `accounts/{account_id}/invitations/{invitation_id}`.
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
   * Output only. The invited role on the account.
   *
   * Accepted values: ADMIN_ROLE_UNSPECIFIED, PRIMARY_OWNER, OWNER, MANAGER,
   * SITE_MANAGER
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
   * The sparsely populated account this invitation is for.
   *
   * @param Account $targetAccount
   */
  public function setTargetAccount(Account $targetAccount)
  {
    $this->targetAccount = $targetAccount;
  }
  /**
   * @return Account
   */
  public function getTargetAccount()
  {
    return $this->targetAccount;
  }
  /**
   * The target location this invitation is for.
   *
   * @param TargetLocation $targetLocation
   */
  public function setTargetLocation(TargetLocation $targetLocation)
  {
    $this->targetLocation = $targetLocation;
  }
  /**
   * @return TargetLocation
   */
  public function getTargetLocation()
  {
    return $this->targetLocation;
  }
  /**
   * Output only. Specifies which target types should appear in the response.
   *
   * Accepted values: TARGET_TYPE_UNSPECIFIED, ACCOUNTS_ONLY, LOCATIONS_ONLY
   *
   * @param self::TARGET_TYPE_* $targetType
   */
  public function setTargetType($targetType)
  {
    $this->targetType = $targetType;
  }
  /**
   * @return self::TARGET_TYPE_*
   */
  public function getTargetType()
  {
    return $this->targetType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Invitation::class, 'Google_Service_MyBusinessAccountManagement_Invitation');
