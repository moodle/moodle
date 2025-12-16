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

namespace Google\Service\AndroidPublisher;

class User extends \Google\Collection
{
  /**
   * Unknown or unspecified access state.
   */
  public const ACCESS_STATE_ACCESS_STATE_UNSPECIFIED = 'ACCESS_STATE_UNSPECIFIED';
  /**
   * User is invited but has not yet accepted the invitation.
   */
  public const ACCESS_STATE_INVITED = 'INVITED';
  /**
   * Invitation has expired.
   */
  public const ACCESS_STATE_INVITATION_EXPIRED = 'INVITATION_EXPIRED';
  /**
   * User has accepted an invitation and has access to the Play Console.
   */
  public const ACCESS_STATE_ACCESS_GRANTED = 'ACCESS_GRANTED';
  /**
   * Account access has expired.
   */
  public const ACCESS_STATE_ACCESS_EXPIRED = 'ACCESS_EXPIRED';
  protected $collection_key = 'grants';
  /**
   * Output only. The state of the user's access to the Play Console.
   *
   * @var string
   */
  public $accessState;
  /**
   * Permissions for the user which apply across the developer account.
   *
   * @var string[]
   */
  public $developerAccountPermissions;
  /**
   * Immutable. The user's email address.
   *
   * @var string
   */
  public $email;
  /**
   * The time at which the user's access expires, if set. When setting this
   * value, it must always be in the future.
   *
   * @var string
   */
  public $expirationTime;
  protected $grantsType = Grant::class;
  protected $grantsDataType = 'array';
  /**
   * Required. Resource name for this user, following the pattern
   * "developers/{developer}/users/{email}".
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Whether there are more permissions for the user that are not
   * represented here. This can happen if the caller does not have permission to
   * manage all apps in the account. This is also `true` if this user is the
   * account owner. If this field is `true`, it should be taken as a signal that
   * this user cannot be fully managed via the API. That is, the API caller is
   * not be able to manage all of the permissions this user holds, either
   * because it doesn't know about them or because the user is the account
   * owner.
   *
   * @var bool
   */
  public $partial;

  /**
   * Output only. The state of the user's access to the Play Console.
   *
   * Accepted values: ACCESS_STATE_UNSPECIFIED, INVITED, INVITATION_EXPIRED,
   * ACCESS_GRANTED, ACCESS_EXPIRED
   *
   * @param self::ACCESS_STATE_* $accessState
   */
  public function setAccessState($accessState)
  {
    $this->accessState = $accessState;
  }
  /**
   * @return self::ACCESS_STATE_*
   */
  public function getAccessState()
  {
    return $this->accessState;
  }
  /**
   * Permissions for the user which apply across the developer account.
   *
   * @param string[] $developerAccountPermissions
   */
  public function setDeveloperAccountPermissions($developerAccountPermissions)
  {
    $this->developerAccountPermissions = $developerAccountPermissions;
  }
  /**
   * @return string[]
   */
  public function getDeveloperAccountPermissions()
  {
    return $this->developerAccountPermissions;
  }
  /**
   * Immutable. The user's email address.
   *
   * @param string $email
   */
  public function setEmail($email)
  {
    $this->email = $email;
  }
  /**
   * @return string
   */
  public function getEmail()
  {
    return $this->email;
  }
  /**
   * The time at which the user's access expires, if set. When setting this
   * value, it must always be in the future.
   *
   * @param string $expirationTime
   */
  public function setExpirationTime($expirationTime)
  {
    $this->expirationTime = $expirationTime;
  }
  /**
   * @return string
   */
  public function getExpirationTime()
  {
    return $this->expirationTime;
  }
  /**
   * Output only. Per-app permissions for the user.
   *
   * @param Grant[] $grants
   */
  public function setGrants($grants)
  {
    $this->grants = $grants;
  }
  /**
   * @return Grant[]
   */
  public function getGrants()
  {
    return $this->grants;
  }
  /**
   * Required. Resource name for this user, following the pattern
   * "developers/{developer}/users/{email}".
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
   * Output only. Whether there are more permissions for the user that are not
   * represented here. This can happen if the caller does not have permission to
   * manage all apps in the account. This is also `true` if this user is the
   * account owner. If this field is `true`, it should be taken as a signal that
   * this user cannot be fully managed via the API. That is, the API caller is
   * not be able to manage all of the permissions this user holds, either
   * because it doesn't know about them or because the user is the account
   * owner.
   *
   * @param bool $partial
   */
  public function setPartial($partial)
  {
    $this->partial = $partial;
  }
  /**
   * @return bool
   */
  public function getPartial()
  {
    return $this->partial;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(User::class, 'Google_Service_AndroidPublisher_User');
