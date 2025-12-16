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

namespace Google\Service\AndroidEnterprise;

class User extends \Google\Model
{
  public const ACCOUNT_TYPE_deviceAccount = 'deviceAccount';
  public const ACCOUNT_TYPE_userAccount = 'userAccount';
  public const MANAGEMENT_TYPE_googleManaged = 'googleManaged';
  public const MANAGEMENT_TYPE_emmManaged = 'emmManaged';
  /**
   * A unique identifier you create for this user, such as "user342" or
   * "asset#44418". Do not use personally identifiable information (PII) for
   * this property. Must always be set for EMM-managed users. Not set for
   * Google-managed users.
   *
   * @var string
   */
  public $accountIdentifier;
  /**
   * The type of account that this user represents. A userAccount can be
   * installed on multiple devices, but a deviceAccount is specific to a single
   * device. An EMM-managed user (emmManaged) can be either type (userAccount,
   * deviceAccount), but a Google-managed user (googleManaged) is always a
   * userAccount.
   *
   * @var string
   */
  public $accountType;
  /**
   * The name that will appear in user interfaces. Setting this property is
   * optional when creating EMM-managed users. If you do set this property, use
   * something generic about the organization (such as "Example, Inc.") or your
   * name (as EMM). Not used for Google-managed user accounts. @mutable
   * androidenterprise.users.update
   *
   * @var string
   */
  public $displayName;
  /**
   * The unique ID for the user.
   *
   * @var string
   */
  public $id;
  /**
   * The entity that manages the user. With googleManaged users, the source of
   * truth is Google so EMMs have to make sure a Google Account exists for the
   * user. With emmManaged users, the EMM is in charge.
   *
   * @var string
   */
  public $managementType;
  /**
   * The user's primary email address, for example, "jsmith@example.com". Will
   * always be set for Google managed users and not set for EMM managed users.
   *
   * @var string
   */
  public $primaryEmail;

  /**
   * A unique identifier you create for this user, such as "user342" or
   * "asset#44418". Do not use personally identifiable information (PII) for
   * this property. Must always be set for EMM-managed users. Not set for
   * Google-managed users.
   *
   * @param string $accountIdentifier
   */
  public function setAccountIdentifier($accountIdentifier)
  {
    $this->accountIdentifier = $accountIdentifier;
  }
  /**
   * @return string
   */
  public function getAccountIdentifier()
  {
    return $this->accountIdentifier;
  }
  /**
   * The type of account that this user represents. A userAccount can be
   * installed on multiple devices, but a deviceAccount is specific to a single
   * device. An EMM-managed user (emmManaged) can be either type (userAccount,
   * deviceAccount), but a Google-managed user (googleManaged) is always a
   * userAccount.
   *
   * Accepted values: deviceAccount, userAccount
   *
   * @param self::ACCOUNT_TYPE_* $accountType
   */
  public function setAccountType($accountType)
  {
    $this->accountType = $accountType;
  }
  /**
   * @return self::ACCOUNT_TYPE_*
   */
  public function getAccountType()
  {
    return $this->accountType;
  }
  /**
   * The name that will appear in user interfaces. Setting this property is
   * optional when creating EMM-managed users. If you do set this property, use
   * something generic about the organization (such as "Example, Inc.") or your
   * name (as EMM). Not used for Google-managed user accounts. @mutable
   * androidenterprise.users.update
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
   * The unique ID for the user.
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
   * The entity that manages the user. With googleManaged users, the source of
   * truth is Google so EMMs have to make sure a Google Account exists for the
   * user. With emmManaged users, the EMM is in charge.
   *
   * Accepted values: googleManaged, emmManaged
   *
   * @param self::MANAGEMENT_TYPE_* $managementType
   */
  public function setManagementType($managementType)
  {
    $this->managementType = $managementType;
  }
  /**
   * @return self::MANAGEMENT_TYPE_*
   */
  public function getManagementType()
  {
    return $this->managementType;
  }
  /**
   * The user's primary email address, for example, "jsmith@example.com". Will
   * always be set for Google managed users and not set for EMM managed users.
   *
   * @param string $primaryEmail
   */
  public function setPrimaryEmail($primaryEmail)
  {
    $this->primaryEmail = $primaryEmail;
  }
  /**
   * @return string
   */
  public function getPrimaryEmail()
  {
    return $this->primaryEmail;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(User::class, 'Google_Service_AndroidEnterprise_User');
