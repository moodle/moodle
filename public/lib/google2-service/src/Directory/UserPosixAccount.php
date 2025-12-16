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

class UserPosixAccount extends \Google\Model
{
  /**
   * A POSIX account field identifier.
   *
   * @var string
   */
  public $accountId;
  /**
   * The GECOS (user information) for this account.
   *
   * @var string
   */
  public $gecos;
  /**
   * The default group ID.
   *
   * @var string
   */
  public $gid;
  /**
   * The path to the home directory for this account.
   *
   * @var string
   */
  public $homeDirectory;
  /**
   * The operating system type for this account.
   *
   * @var string
   */
  public $operatingSystemType;
  /**
   * If this is user's primary account within the SystemId.
   *
   * @var bool
   */
  public $primary;
  /**
   * The path to the login shell for this account.
   *
   * @var string
   */
  public $shell;
  /**
   * System identifier for which account Username or Uid apply to.
   *
   * @var string
   */
  public $systemId;
  /**
   * The POSIX compliant user ID.
   *
   * @var string
   */
  public $uid;
  /**
   * The username of the account.
   *
   * @var string
   */
  public $username;

  /**
   * A POSIX account field identifier.
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
   * The GECOS (user information) for this account.
   *
   * @param string $gecos
   */
  public function setGecos($gecos)
  {
    $this->gecos = $gecos;
  }
  /**
   * @return string
   */
  public function getGecos()
  {
    return $this->gecos;
  }
  /**
   * The default group ID.
   *
   * @param string $gid
   */
  public function setGid($gid)
  {
    $this->gid = $gid;
  }
  /**
   * @return string
   */
  public function getGid()
  {
    return $this->gid;
  }
  /**
   * The path to the home directory for this account.
   *
   * @param string $homeDirectory
   */
  public function setHomeDirectory($homeDirectory)
  {
    $this->homeDirectory = $homeDirectory;
  }
  /**
   * @return string
   */
  public function getHomeDirectory()
  {
    return $this->homeDirectory;
  }
  /**
   * The operating system type for this account.
   *
   * @param string $operatingSystemType
   */
  public function setOperatingSystemType($operatingSystemType)
  {
    $this->operatingSystemType = $operatingSystemType;
  }
  /**
   * @return string
   */
  public function getOperatingSystemType()
  {
    return $this->operatingSystemType;
  }
  /**
   * If this is user's primary account within the SystemId.
   *
   * @param bool $primary
   */
  public function setPrimary($primary)
  {
    $this->primary = $primary;
  }
  /**
   * @return bool
   */
  public function getPrimary()
  {
    return $this->primary;
  }
  /**
   * The path to the login shell for this account.
   *
   * @param string $shell
   */
  public function setShell($shell)
  {
    $this->shell = $shell;
  }
  /**
   * @return string
   */
  public function getShell()
  {
    return $this->shell;
  }
  /**
   * System identifier for which account Username or Uid apply to.
   *
   * @param string $systemId
   */
  public function setSystemId($systemId)
  {
    $this->systemId = $systemId;
  }
  /**
   * @return string
   */
  public function getSystemId()
  {
    return $this->systemId;
  }
  /**
   * The POSIX compliant user ID.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
  /**
   * The username of the account.
   *
   * @param string $username
   */
  public function setUsername($username)
  {
    $this->username = $username;
  }
  /**
   * @return string
   */
  public function getUsername()
  {
    return $this->username;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UserPosixAccount::class, 'Google_Service_Directory_UserPosixAccount');
