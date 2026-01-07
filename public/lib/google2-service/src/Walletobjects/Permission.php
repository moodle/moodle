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

namespace Google\Service\Walletobjects;

class Permission extends \Google\Model
{
  public const ROLE_ROLE_UNSPECIFIED = 'ROLE_UNSPECIFIED';
  public const ROLE_OWNER = 'OWNER';
  /**
   * Legacy alias for `OWNER`. Deprecated.
   *
   * @deprecated
   */
  public const ROLE_owner = 'owner';
  public const ROLE_READER = 'READER';
  /**
   * Legacy alias for `READER`. Deprecated.
   *
   * @deprecated
   */
  public const ROLE_reader = 'reader';
  public const ROLE_WRITER = 'WRITER';
  /**
   * Legacy alias for `WRITER`. Deprecated.
   *
   * @deprecated
   */
  public const ROLE_writer = 'writer';
  /**
   * The email address of the user, group, or service account to which this
   * permission refers to.
   *
   * @var string
   */
  public $emailAddress;
  /**
   * The role granted by this permission.
   *
   * @var string
   */
  public $role;

  /**
   * The email address of the user, group, or service account to which this
   * permission refers to.
   *
   * @param string $emailAddress
   */
  public function setEmailAddress($emailAddress)
  {
    $this->emailAddress = $emailAddress;
  }
  /**
   * @return string
   */
  public function getEmailAddress()
  {
    return $this->emailAddress;
  }
  /**
   * The role granted by this permission.
   *
   * Accepted values: ROLE_UNSPECIFIED, OWNER, owner, READER, reader, WRITER,
   * writer
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
class_alias(Permission::class, 'Google_Service_Walletobjects_Permission');
