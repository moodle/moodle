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

namespace Google\Service\CloudOSLogin;

class PosixAccount extends \Google\Model
{
  /**
   * The operating system type associated with the user account information is
   * unspecified.
   */
  public const OPERATING_SYSTEM_TYPE_OPERATING_SYSTEM_TYPE_UNSPECIFIED = 'OPERATING_SYSTEM_TYPE_UNSPECIFIED';
  /**
   * Linux user account information.
   */
  public const OPERATING_SYSTEM_TYPE_LINUX = 'LINUX';
  /**
   * Windows user account information.
   */
  public const OPERATING_SYSTEM_TYPE_WINDOWS = 'WINDOWS';
  /**
   * Output only. A POSIX account identifier.
   *
   * @var string
   */
  public $accountId;
  /**
   * The GECOS (user information) entry for this account.
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
   * Output only. The canonical resource name.
   *
   * @var string
   */
  public $name;
  /**
   * The operating system type where this account applies.
   *
   * @var string
   */
  public $operatingSystemType;
  /**
   * Only one POSIX account can be marked as primary.
   *
   * @var bool
   */
  public $primary;
  /**
   * The path to the logic shell for this account.
   *
   * @var string
   */
  public $shell;
  /**
   * System identifier for which account the username or uid applies to. By
   * default, the empty value is used.
   *
   * @var string
   */
  public $systemId;
  /**
   * The user ID.
   *
   * @var string
   */
  public $uid;
  /**
   * The username of the POSIX account.
   *
   * @var string
   */
  public $username;

  /**
   * Output only. A POSIX account identifier.
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
   * The GECOS (user information) entry for this account.
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
   * Output only. The canonical resource name.
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
   * The operating system type where this account applies.
   *
   * Accepted values: OPERATING_SYSTEM_TYPE_UNSPECIFIED, LINUX, WINDOWS
   *
   * @param self::OPERATING_SYSTEM_TYPE_* $operatingSystemType
   */
  public function setOperatingSystemType($operatingSystemType)
  {
    $this->operatingSystemType = $operatingSystemType;
  }
  /**
   * @return self::OPERATING_SYSTEM_TYPE_*
   */
  public function getOperatingSystemType()
  {
    return $this->operatingSystemType;
  }
  /**
   * Only one POSIX account can be marked as primary.
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
   * The path to the logic shell for this account.
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
   * System identifier for which account the username or uid applies to. By
   * default, the empty value is used.
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
   * The user ID.
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
   * The username of the POSIX account.
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
class_alias(PosixAccount::class, 'Google_Service_CloudOSLogin_PosixAccount');
