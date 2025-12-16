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

class Admin extends \Google\Model
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
   * Immutable. The name of the Account resource that this Admin refers to. Used
   * when calling locations.admins.create to invite a LocationGroup as an admin.
   * If both this field and `admin` are set on `CREATE` requests, this field
   * takes precedence and the email address in `admin` will be ignored. Format:
   * `accounts/{account}`.
   *
   * @var string
   */
  public $account;
  /**
   * Optional. The name of the admin. When making the initial invitation, this
   * is the invitee's email address. On `GET` calls, the user's email address is
   * returned if the invitation is still pending. Otherwise, it contains the
   * user's first and last names. This field is only needed to be set during
   * admin creation.
   *
   * @var string
   */
  public $admin;
  /**
   * Immutable. The resource name. For account admins, this is in the form:
   * `accounts/{account_id}/admins/{admin_id}` For location admins, this is in
   * the form: `locations/{location_id}/admins/{admin_id}` This field will be
   * ignored if set during admin creation.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Indicates whether this admin has a pending invitation for the
   * specified resource.
   *
   * @var bool
   */
  public $pendingInvitation;
  /**
   * Required. Specifies the role that this admin uses with the specified
   * Account or Location.
   *
   * @var string
   */
  public $role;

  /**
   * Immutable. The name of the Account resource that this Admin refers to. Used
   * when calling locations.admins.create to invite a LocationGroup as an admin.
   * If both this field and `admin` are set on `CREATE` requests, this field
   * takes precedence and the email address in `admin` will be ignored. Format:
   * `accounts/{account}`.
   *
   * @param string $account
   */
  public function setAccount($account)
  {
    $this->account = $account;
  }
  /**
   * @return string
   */
  public function getAccount()
  {
    return $this->account;
  }
  /**
   * Optional. The name of the admin. When making the initial invitation, this
   * is the invitee's email address. On `GET` calls, the user's email address is
   * returned if the invitation is still pending. Otherwise, it contains the
   * user's first and last names. This field is only needed to be set during
   * admin creation.
   *
   * @param string $admin
   */
  public function setAdmin($admin)
  {
    $this->admin = $admin;
  }
  /**
   * @return string
   */
  public function getAdmin()
  {
    return $this->admin;
  }
  /**
   * Immutable. The resource name. For account admins, this is in the form:
   * `accounts/{account_id}/admins/{admin_id}` For location admins, this is in
   * the form: `locations/{location_id}/admins/{admin_id}` This field will be
   * ignored if set during admin creation.
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
   * Output only. Indicates whether this admin has a pending invitation for the
   * specified resource.
   *
   * @param bool $pendingInvitation
   */
  public function setPendingInvitation($pendingInvitation)
  {
    $this->pendingInvitation = $pendingInvitation;
  }
  /**
   * @return bool
   */
  public function getPendingInvitation()
  {
    return $this->pendingInvitation;
  }
  /**
   * Required. Specifies the role that this admin uses with the specified
   * Account or Location.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Admin::class, 'Google_Service_MyBusinessAccountManagement_Admin');
