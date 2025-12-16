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

namespace Google\Service\SQLAdmin;

class User extends \Google\Collection
{
  /**
   * The default value.
   */
  public const DUAL_PASSWORD_TYPE_DUAL_PASSWORD_TYPE_UNSPECIFIED = 'DUAL_PASSWORD_TYPE_UNSPECIFIED';
  /**
   * Do not update the user's dual password status.
   */
  public const DUAL_PASSWORD_TYPE_NO_MODIFY_DUAL_PASSWORD = 'NO_MODIFY_DUAL_PASSWORD';
  /**
   * No dual password usable for connecting using this user.
   */
  public const DUAL_PASSWORD_TYPE_NO_DUAL_PASSWORD = 'NO_DUAL_PASSWORD';
  /**
   * Dual password usable for connecting using this user.
   */
  public const DUAL_PASSWORD_TYPE_DUAL_PASSWORD = 'DUAL_PASSWORD';
  /**
   * The default value for users that are not of type CLOUD_IAM_GROUP. Only
   * CLOUD_IAM_GROUP users will be inactive or active. Users with an IamStatus
   * of IAM_STATUS_UNSPECIFIED will not display whether they are active or
   * inactive as that is not applicable to them.
   */
  public const IAM_STATUS_IAM_STATUS_UNSPECIFIED = 'IAM_STATUS_UNSPECIFIED';
  /**
   * INACTIVE indicates a group is not available for IAM database
   * authentication.
   */
  public const IAM_STATUS_INACTIVE = 'INACTIVE';
  /**
   * ACTIVE indicates a group is available for IAM database authentication.
   */
  public const IAM_STATUS_ACTIVE = 'ACTIVE';
  /**
   * The database's built-in user type.
   */
  public const TYPE_BUILT_IN = 'BUILT_IN';
  /**
   * Cloud IAM user.
   */
  public const TYPE_CLOUD_IAM_USER = 'CLOUD_IAM_USER';
  /**
   * Cloud IAM service account.
   */
  public const TYPE_CLOUD_IAM_SERVICE_ACCOUNT = 'CLOUD_IAM_SERVICE_ACCOUNT';
  /**
   * Cloud IAM group. Not used for login.
   */
  public const TYPE_CLOUD_IAM_GROUP = 'CLOUD_IAM_GROUP';
  /**
   * Read-only. Login for a user that belongs to the Cloud IAM group.
   */
  public const TYPE_CLOUD_IAM_GROUP_USER = 'CLOUD_IAM_GROUP_USER';
  /**
   * Read-only. Login for a service account that belongs to the Cloud IAM group.
   */
  public const TYPE_CLOUD_IAM_GROUP_SERVICE_ACCOUNT = 'CLOUD_IAM_GROUP_SERVICE_ACCOUNT';
  /**
   * Microsoft Entra ID user.
   */
  public const TYPE_ENTRAID_USER = 'ENTRAID_USER';
  protected $collection_key = 'databaseRoles';
  /**
   * Optional. Role memberships of the user
   *
   * @var string[]
   */
  public $databaseRoles;
  /**
   * Dual password status for the user.
   *
   * @var string
   */
  public $dualPasswordType;
  /**
   * This field is deprecated and will be removed from a future version of the
   * API.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. The host from which the user can connect. For `insert`
   * operations, host defaults to an empty string. For `update` operations, host
   * is specified as part of the request URL. The host name cannot be updated
   * after insertion. For a MySQL instance, it's required; for a PostgreSQL or
   * SQL Server instance, it's optional.
   *
   * @var string
   */
  public $host;
  /**
   * Optional. The full email for an IAM user. For normal database users, this
   * will not be filled. Only applicable to MySQL database users.
   *
   * @var string
   */
  public $iamEmail;
  /**
   * Indicates if a group is active or inactive for IAM database authentication.
   *
   * @var string
   */
  public $iamStatus;
  /**
   * The name of the Cloud SQL instance. This does not include the project ID.
   * Can be omitted for `update` because it is already specified on the URL.
   *
   * @var string
   */
  public $instance;
  /**
   * This is always `sql#user`.
   *
   * @var string
   */
  public $kind;
  /**
   * The name of the user in the Cloud SQL instance. Can be omitted for `update`
   * because it is already specified in the URL.
   *
   * @var string
   */
  public $name;
  /**
   * The password for the user.
   *
   * @var string
   */
  public $password;
  protected $passwordPolicyType = UserPasswordValidationPolicy::class;
  protected $passwordPolicyDataType = '';
  /**
   * The project ID of the project containing the Cloud SQL database. The Google
   * apps domain is prefixed if applicable. Can be omitted for `update` because
   * it is already specified on the URL.
   *
   * @var string
   */
  public $project;
  protected $sqlserverUserDetailsType = SqlServerUserDetails::class;
  protected $sqlserverUserDetailsDataType = '';
  /**
   * The user type. It determines the method to authenticate the user during
   * login. The default is the database's built-in user type.
   *
   * @var string
   */
  public $type;

  /**
   * Optional. Role memberships of the user
   *
   * @param string[] $databaseRoles
   */
  public function setDatabaseRoles($databaseRoles)
  {
    $this->databaseRoles = $databaseRoles;
  }
  /**
   * @return string[]
   */
  public function getDatabaseRoles()
  {
    return $this->databaseRoles;
  }
  /**
   * Dual password status for the user.
   *
   * Accepted values: DUAL_PASSWORD_TYPE_UNSPECIFIED, NO_MODIFY_DUAL_PASSWORD,
   * NO_DUAL_PASSWORD, DUAL_PASSWORD
   *
   * @param self::DUAL_PASSWORD_TYPE_* $dualPasswordType
   */
  public function setDualPasswordType($dualPasswordType)
  {
    $this->dualPasswordType = $dualPasswordType;
  }
  /**
   * @return self::DUAL_PASSWORD_TYPE_*
   */
  public function getDualPasswordType()
  {
    return $this->dualPasswordType;
  }
  /**
   * This field is deprecated and will be removed from a future version of the
   * API.
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
   * Optional. The host from which the user can connect. For `insert`
   * operations, host defaults to an empty string. For `update` operations, host
   * is specified as part of the request URL. The host name cannot be updated
   * after insertion. For a MySQL instance, it's required; for a PostgreSQL or
   * SQL Server instance, it's optional.
   *
   * @param string $host
   */
  public function setHost($host)
  {
    $this->host = $host;
  }
  /**
   * @return string
   */
  public function getHost()
  {
    return $this->host;
  }
  /**
   * Optional. The full email for an IAM user. For normal database users, this
   * will not be filled. Only applicable to MySQL database users.
   *
   * @param string $iamEmail
   */
  public function setIamEmail($iamEmail)
  {
    $this->iamEmail = $iamEmail;
  }
  /**
   * @return string
   */
  public function getIamEmail()
  {
    return $this->iamEmail;
  }
  /**
   * Indicates if a group is active or inactive for IAM database authentication.
   *
   * Accepted values: IAM_STATUS_UNSPECIFIED, INACTIVE, ACTIVE
   *
   * @param self::IAM_STATUS_* $iamStatus
   */
  public function setIamStatus($iamStatus)
  {
    $this->iamStatus = $iamStatus;
  }
  /**
   * @return self::IAM_STATUS_*
   */
  public function getIamStatus()
  {
    return $this->iamStatus;
  }
  /**
   * The name of the Cloud SQL instance. This does not include the project ID.
   * Can be omitted for `update` because it is already specified on the URL.
   *
   * @param string $instance
   */
  public function setInstance($instance)
  {
    $this->instance = $instance;
  }
  /**
   * @return string
   */
  public function getInstance()
  {
    return $this->instance;
  }
  /**
   * This is always `sql#user`.
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
   * The name of the user in the Cloud SQL instance. Can be omitted for `update`
   * because it is already specified in the URL.
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
   * The password for the user.
   *
   * @param string $password
   */
  public function setPassword($password)
  {
    $this->password = $password;
  }
  /**
   * @return string
   */
  public function getPassword()
  {
    return $this->password;
  }
  /**
   * User level password validation policy.
   *
   * @param UserPasswordValidationPolicy $passwordPolicy
   */
  public function setPasswordPolicy(UserPasswordValidationPolicy $passwordPolicy)
  {
    $this->passwordPolicy = $passwordPolicy;
  }
  /**
   * @return UserPasswordValidationPolicy
   */
  public function getPasswordPolicy()
  {
    return $this->passwordPolicy;
  }
  /**
   * The project ID of the project containing the Cloud SQL database. The Google
   * apps domain is prefixed if applicable. Can be omitted for `update` because
   * it is already specified on the URL.
   *
   * @param string $project
   */
  public function setProject($project)
  {
    $this->project = $project;
  }
  /**
   * @return string
   */
  public function getProject()
  {
    return $this->project;
  }
  /**
   * @param SqlServerUserDetails $sqlserverUserDetails
   */
  public function setSqlserverUserDetails(SqlServerUserDetails $sqlserverUserDetails)
  {
    $this->sqlserverUserDetails = $sqlserverUserDetails;
  }
  /**
   * @return SqlServerUserDetails
   */
  public function getSqlserverUserDetails()
  {
    return $this->sqlserverUserDetails;
  }
  /**
   * The user type. It determines the method to authenticate the user during
   * login. The default is the database's built-in user type.
   *
   * Accepted values: BUILT_IN, CLOUD_IAM_USER, CLOUD_IAM_SERVICE_ACCOUNT,
   * CLOUD_IAM_GROUP, CLOUD_IAM_GROUP_USER, CLOUD_IAM_GROUP_SERVICE_ACCOUNT,
   * ENTRAID_USER
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(User::class, 'Google_Service_SQLAdmin_User');
