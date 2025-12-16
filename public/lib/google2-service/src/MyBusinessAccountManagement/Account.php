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

class Account extends \Google\Model
{
  /**
   * Not specified.
   */
  public const PERMISSION_LEVEL_PERMISSION_LEVEL_UNSPECIFIED = 'PERMISSION_LEVEL_UNSPECIFIED';
  /**
   * The user has owner level permission.
   */
  public const PERMISSION_LEVEL_OWNER_LEVEL = 'OWNER_LEVEL';
  /**
   * The user has member level permission.
   */
  public const PERMISSION_LEVEL_MEMBER_LEVEL = 'MEMBER_LEVEL';
  /**
   * Not specified.
   */
  public const ROLE_ACCOUNT_ROLE_UNSPECIFIED = 'ACCOUNT_ROLE_UNSPECIFIED';
  /**
   * The user is the primary owner this account.
   */
  public const ROLE_PRIMARY_OWNER = 'PRIMARY_OWNER';
  /**
   * The user owner of the account.
   */
  public const ROLE_OWNER = 'OWNER';
  /**
   * The user can manage this account.
   */
  public const ROLE_MANAGER = 'MANAGER';
  /**
   * The user can manage a limited set of features for the account.
   */
  public const ROLE_SITE_MANAGER = 'SITE_MANAGER';
  /**
   * Not specified.
   */
  public const TYPE_ACCOUNT_TYPE_UNSPECIFIED = 'ACCOUNT_TYPE_UNSPECIFIED';
  /**
   * An end-user account.
   */
  public const TYPE_PERSONAL = 'PERSONAL';
  /**
   * A group of Locations. For more information, see the [help center article]
   * (https://support.google.com/business/answer/6085326)
   */
  public const TYPE_LOCATION_GROUP = 'LOCATION_GROUP';
  /**
   * A User Group for segregating organization staff in groups. For more
   * information, see the [help center
   * article](https://support.google.com/business/answer/7655731)
   */
  public const TYPE_USER_GROUP = 'USER_GROUP';
  /**
   * An organization representing a company. For more information, see the [help
   * center article](https://support.google.com/business/answer/7663063)
   */
  public const TYPE_ORGANIZATION = 'ORGANIZATION';
  /**
   * Not specified.
   */
  public const VERIFICATION_STATE_VERIFICATION_STATE_UNSPECIFIED = 'VERIFICATION_STATE_UNSPECIFIED';
  /**
   * Verified account.
   */
  public const VERIFICATION_STATE_VERIFIED = 'VERIFIED';
  /**
   * Account that is not verified, and verification has not been requested.
   */
  public const VERIFICATION_STATE_UNVERIFIED = 'UNVERIFIED';
  /**
   * Account that is not verified, but verification has been requested.
   */
  public const VERIFICATION_STATE_VERIFICATION_REQUESTED = 'VERIFICATION_REQUESTED';
  /**
   * Not Specified
   */
  public const VETTED_STATE_VETTED_STATE_UNSPECIFIED = 'VETTED_STATE_UNSPECIFIED';
  /**
   * The account is not vetted by Google.
   */
  public const VETTED_STATE_NOT_VETTED = 'NOT_VETTED';
  /**
   * The account is vetted by Google and in a valid state. An account is
   * automatically vetted if it has direct access to a vetted group account.
   */
  public const VETTED_STATE_VETTED = 'VETTED';
  /**
   * The account is vetted but in an invalid state. The account will behave like
   * an unvetted account.
   */
  public const VETTED_STATE_INVALID = 'INVALID';
  /**
   * Required. The name of the account. For an account of type `PERSONAL`, this
   * is the first and last name of the user account.
   *
   * @var string
   */
  public $accountName;
  /**
   * Output only. Account reference number if provisioned.
   *
   * @var string
   */
  public $accountNumber;
  /**
   * Immutable. The resource name, in the format `accounts/{account_id}`.
   *
   * @var string
   */
  public $name;
  protected $organizationInfoType = OrganizationInfo::class;
  protected $organizationInfoDataType = '';
  /**
   * Output only. Specifies the permission level the user has for this account.
   *
   * @var string
   */
  public $permissionLevel;
  /**
   * Required. Input only. The resource name of the account which will be the
   * primary owner of the account being created. It should be of the form
   * `accounts/{account_id}`.
   *
   * @var string
   */
  public $primaryOwner;
  /**
   * Output only. Specifies the AccountRole of this account.
   *
   * @var string
   */
  public $role;
  /**
   * Required. Contains the type of account. Accounts of type PERSONAL and
   * ORGANIZATION cannot be created using this API.
   *
   * @var string
   */
  public $type;
  /**
   * Output only. If verified, future locations that are created are
   * automatically connected to Google Maps, and have Google+ pages created,
   * without requiring moderation.
   *
   * @var string
   */
  public $verificationState;
  /**
   * Output only. Indicates whether the account is vetted by Google. A vetted
   * account is able to verify locations via the VETTED_PARTNER method.
   *
   * @var string
   */
  public $vettedState;

  /**
   * Required. The name of the account. For an account of type `PERSONAL`, this
   * is the first and last name of the user account.
   *
   * @param string $accountName
   */
  public function setAccountName($accountName)
  {
    $this->accountName = $accountName;
  }
  /**
   * @return string
   */
  public function getAccountName()
  {
    return $this->accountName;
  }
  /**
   * Output only. Account reference number if provisioned.
   *
   * @param string $accountNumber
   */
  public function setAccountNumber($accountNumber)
  {
    $this->accountNumber = $accountNumber;
  }
  /**
   * @return string
   */
  public function getAccountNumber()
  {
    return $this->accountNumber;
  }
  /**
   * Immutable. The resource name, in the format `accounts/{account_id}`.
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
   * Output only. Additional info for an organization. This is populated only
   * for an organization account.
   *
   * @param OrganizationInfo $organizationInfo
   */
  public function setOrganizationInfo(OrganizationInfo $organizationInfo)
  {
    $this->organizationInfo = $organizationInfo;
  }
  /**
   * @return OrganizationInfo
   */
  public function getOrganizationInfo()
  {
    return $this->organizationInfo;
  }
  /**
   * Output only. Specifies the permission level the user has for this account.
   *
   * Accepted values: PERMISSION_LEVEL_UNSPECIFIED, OWNER_LEVEL, MEMBER_LEVEL
   *
   * @param self::PERMISSION_LEVEL_* $permissionLevel
   */
  public function setPermissionLevel($permissionLevel)
  {
    $this->permissionLevel = $permissionLevel;
  }
  /**
   * @return self::PERMISSION_LEVEL_*
   */
  public function getPermissionLevel()
  {
    return $this->permissionLevel;
  }
  /**
   * Required. Input only. The resource name of the account which will be the
   * primary owner of the account being created. It should be of the form
   * `accounts/{account_id}`.
   *
   * @param string $primaryOwner
   */
  public function setPrimaryOwner($primaryOwner)
  {
    $this->primaryOwner = $primaryOwner;
  }
  /**
   * @return string
   */
  public function getPrimaryOwner()
  {
    return $this->primaryOwner;
  }
  /**
   * Output only. Specifies the AccountRole of this account.
   *
   * Accepted values: ACCOUNT_ROLE_UNSPECIFIED, PRIMARY_OWNER, OWNER, MANAGER,
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
   * Required. Contains the type of account. Accounts of type PERSONAL and
   * ORGANIZATION cannot be created using this API.
   *
   * Accepted values: ACCOUNT_TYPE_UNSPECIFIED, PERSONAL, LOCATION_GROUP,
   * USER_GROUP, ORGANIZATION
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Output only. If verified, future locations that are created are
   * automatically connected to Google Maps, and have Google+ pages created,
   * without requiring moderation.
   *
   * Accepted values: VERIFICATION_STATE_UNSPECIFIED, VERIFIED, UNVERIFIED,
   * VERIFICATION_REQUESTED
   *
   * @param self::VERIFICATION_STATE_* $verificationState
   */
  public function setVerificationState($verificationState)
  {
    $this->verificationState = $verificationState;
  }
  /**
   * @return self::VERIFICATION_STATE_*
   */
  public function getVerificationState()
  {
    return $this->verificationState;
  }
  /**
   * Output only. Indicates whether the account is vetted by Google. A vetted
   * account is able to verify locations via the VETTED_PARTNER method.
   *
   * Accepted values: VETTED_STATE_UNSPECIFIED, NOT_VETTED, VETTED, INVALID
   *
   * @param self::VETTED_STATE_* $vettedState
   */
  public function setVettedState($vettedState)
  {
    $this->vettedState = $vettedState;
  }
  /**
   * @return self::VETTED_STATE_*
   */
  public function getVettedState()
  {
    return $this->vettedState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Account::class, 'Google_Service_MyBusinessAccountManagement_Account');
