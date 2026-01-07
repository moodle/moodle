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

namespace Google\Service\CloudAlloyDBAdmin;

class User extends \Google\Collection
{
  /**
   * Unspecified user type.
   */
  public const USER_TYPE_USER_TYPE_UNSPECIFIED = 'USER_TYPE_UNSPECIFIED';
  /**
   * The default user type that authenticates via password-based authentication.
   */
  public const USER_TYPE_ALLOYDB_BUILT_IN = 'ALLOYDB_BUILT_IN';
  /**
   * Database user that can authenticate via IAM-Based authentication.
   */
  public const USER_TYPE_ALLOYDB_IAM_USER = 'ALLOYDB_IAM_USER';
  protected $collection_key = 'databaseRoles';
  /**
   * Optional. List of database roles this user has. The database role strings
   * are subject to the PostgreSQL naming conventions.
   *
   * @var string[]
   */
  public $databaseRoles;
  /**
   * Input only. If the user already exists and it has additional roles, keep
   * them granted.
   *
   * @var bool
   */
  public $keepExtraRoles;
  /**
   * Output only. Name of the resource in the form of
   * projects/{project}/locations/{location}/cluster/{cluster}/users/{user}.
   *
   * @var string
   */
  public $name;
  /**
   * Input only. Password for the user.
   *
   * @var string
   */
  public $password;
  /**
   * Optional. Type of this user.
   *
   * @var string
   */
  public $userType;

  /**
   * Optional. List of database roles this user has. The database role strings
   * are subject to the PostgreSQL naming conventions.
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
   * Input only. If the user already exists and it has additional roles, keep
   * them granted.
   *
   * @param bool $keepExtraRoles
   */
  public function setKeepExtraRoles($keepExtraRoles)
  {
    $this->keepExtraRoles = $keepExtraRoles;
  }
  /**
   * @return bool
   */
  public function getKeepExtraRoles()
  {
    return $this->keepExtraRoles;
  }
  /**
   * Output only. Name of the resource in the form of
   * projects/{project}/locations/{location}/cluster/{cluster}/users/{user}.
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
   * Input only. Password for the user.
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
   * Optional. Type of this user.
   *
   * Accepted values: USER_TYPE_UNSPECIFIED, ALLOYDB_BUILT_IN, ALLOYDB_IAM_USER
   *
   * @param self::USER_TYPE_* $userType
   */
  public function setUserType($userType)
  {
    $this->userType = $userType;
  }
  /**
   * @return self::USER_TYPE_*
   */
  public function getUserType()
  {
    return $this->userType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(User::class, 'Google_Service_CloudAlloyDBAdmin_User');
