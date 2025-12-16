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

namespace Google\Service\IdentityToolkit;

class UserInfo extends \Google\Collection
{
  protected $collection_key = 'providerUserInfo';
  /**
   * User creation timestamp.
   *
   * @var string
   */
  public $createdAt;
  /**
   * The custom attributes to be set in the user's id token.
   *
   * @var string
   */
  public $customAttributes;
  /**
   * Whether the user is authenticated by the developer.
   *
   * @var bool
   */
  public $customAuth;
  /**
   * Whether the user is disabled.
   *
   * @var bool
   */
  public $disabled;
  /**
   * The name of the user.
   *
   * @var string
   */
  public $displayName;
  /**
   * The email of the user.
   *
   * @var string
   */
  public $email;
  /**
   * Whether the email has been verified.
   *
   * @var bool
   */
  public $emailVerified;
  /**
   * last login timestamp.
   *
   * @var string
   */
  public $lastLoginAt;
  /**
   * The local ID of the user.
   *
   * @var string
   */
  public $localId;
  /**
   * The user's hashed password.
   *
   * @var string
   */
  public $passwordHash;
  /**
   * The timestamp when the password was last updated.
   *
   * @var 
   */
  public $passwordUpdatedAt;
  /**
   * User's phone number.
   *
   * @var string
   */
  public $phoneNumber;
  /**
   * The URL of the user profile photo.
   *
   * @var string
   */
  public $photoUrl;
  protected $providerUserInfoType = UserInfoProviderUserInfo::class;
  protected $providerUserInfoDataType = 'array';
  /**
   * The user's plain text password.
   *
   * @var string
   */
  public $rawPassword;
  /**
   * The user's password salt.
   *
   * @var string
   */
  public $salt;
  /**
   * User's screen name at Twitter or login name at Github.
   *
   * @var string
   */
  public $screenName;
  /**
   * Timestamp in seconds for valid login token.
   *
   * @var string
   */
  public $validSince;
  /**
   * Version of the user's password.
   *
   * @var int
   */
  public $version;

  /**
   * User creation timestamp.
   *
   * @param string $createdAt
   */
  public function setCreatedAt($createdAt)
  {
    $this->createdAt = $createdAt;
  }
  /**
   * @return string
   */
  public function getCreatedAt()
  {
    return $this->createdAt;
  }
  /**
   * The custom attributes to be set in the user's id token.
   *
   * @param string $customAttributes
   */
  public function setCustomAttributes($customAttributes)
  {
    $this->customAttributes = $customAttributes;
  }
  /**
   * @return string
   */
  public function getCustomAttributes()
  {
    return $this->customAttributes;
  }
  /**
   * Whether the user is authenticated by the developer.
   *
   * @param bool $customAuth
   */
  public function setCustomAuth($customAuth)
  {
    $this->customAuth = $customAuth;
  }
  /**
   * @return bool
   */
  public function getCustomAuth()
  {
    return $this->customAuth;
  }
  /**
   * Whether the user is disabled.
   *
   * @param bool $disabled
   */
  public function setDisabled($disabled)
  {
    $this->disabled = $disabled;
  }
  /**
   * @return bool
   */
  public function getDisabled()
  {
    return $this->disabled;
  }
  /**
   * The name of the user.
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
   * The email of the user.
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
   * Whether the email has been verified.
   *
   * @param bool $emailVerified
   */
  public function setEmailVerified($emailVerified)
  {
    $this->emailVerified = $emailVerified;
  }
  /**
   * @return bool
   */
  public function getEmailVerified()
  {
    return $this->emailVerified;
  }
  /**
   * last login timestamp.
   *
   * @param string $lastLoginAt
   */
  public function setLastLoginAt($lastLoginAt)
  {
    $this->lastLoginAt = $lastLoginAt;
  }
  /**
   * @return string
   */
  public function getLastLoginAt()
  {
    return $this->lastLoginAt;
  }
  /**
   * The local ID of the user.
   *
   * @param string $localId
   */
  public function setLocalId($localId)
  {
    $this->localId = $localId;
  }
  /**
   * @return string
   */
  public function getLocalId()
  {
    return $this->localId;
  }
  /**
   * The user's hashed password.
   *
   * @param string $passwordHash
   */
  public function setPasswordHash($passwordHash)
  {
    $this->passwordHash = $passwordHash;
  }
  /**
   * @return string
   */
  public function getPasswordHash()
  {
    return $this->passwordHash;
  }
  public function setPasswordUpdatedAt($passwordUpdatedAt)
  {
    $this->passwordUpdatedAt = $passwordUpdatedAt;
  }
  public function getPasswordUpdatedAt()
  {
    return $this->passwordUpdatedAt;
  }
  /**
   * User's phone number.
   *
   * @param string $phoneNumber
   */
  public function setPhoneNumber($phoneNumber)
  {
    $this->phoneNumber = $phoneNumber;
  }
  /**
   * @return string
   */
  public function getPhoneNumber()
  {
    return $this->phoneNumber;
  }
  /**
   * The URL of the user profile photo.
   *
   * @param string $photoUrl
   */
  public function setPhotoUrl($photoUrl)
  {
    $this->photoUrl = $photoUrl;
  }
  /**
   * @return string
   */
  public function getPhotoUrl()
  {
    return $this->photoUrl;
  }
  /**
   * The IDP of the user.
   *
   * @param UserInfoProviderUserInfo[] $providerUserInfo
   */
  public function setProviderUserInfo($providerUserInfo)
  {
    $this->providerUserInfo = $providerUserInfo;
  }
  /**
   * @return UserInfoProviderUserInfo[]
   */
  public function getProviderUserInfo()
  {
    return $this->providerUserInfo;
  }
  /**
   * The user's plain text password.
   *
   * @param string $rawPassword
   */
  public function setRawPassword($rawPassword)
  {
    $this->rawPassword = $rawPassword;
  }
  /**
   * @return string
   */
  public function getRawPassword()
  {
    return $this->rawPassword;
  }
  /**
   * The user's password salt.
   *
   * @param string $salt
   */
  public function setSalt($salt)
  {
    $this->salt = $salt;
  }
  /**
   * @return string
   */
  public function getSalt()
  {
    return $this->salt;
  }
  /**
   * User's screen name at Twitter or login name at Github.
   *
   * @param string $screenName
   */
  public function setScreenName($screenName)
  {
    $this->screenName = $screenName;
  }
  /**
   * @return string
   */
  public function getScreenName()
  {
    return $this->screenName;
  }
  /**
   * Timestamp in seconds for valid login token.
   *
   * @param string $validSince
   */
  public function setValidSince($validSince)
  {
    $this->validSince = $validSince;
  }
  /**
   * @return string
   */
  public function getValidSince()
  {
    return $this->validSince;
  }
  /**
   * Version of the user's password.
   *
   * @param int $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return int
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UserInfo::class, 'Google_Service_IdentityToolkit_UserInfo');
