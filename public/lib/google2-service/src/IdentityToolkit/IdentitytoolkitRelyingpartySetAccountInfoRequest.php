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

class IdentitytoolkitRelyingpartySetAccountInfoRequest extends \Google\Collection
{
  protected $collection_key = 'provider';
  /**
   * The captcha challenge.
   *
   * @var string
   */
  public $captchaChallenge;
  /**
   * Response to the captcha.
   *
   * @var string
   */
  public $captchaResponse;
  /**
   * The timestamp when the account is created.
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
   * GCP project number of the requesting delegated app. Currently only intended
   * for Firebase V1 migration.
   *
   * @var string
   */
  public $delegatedProjectNumber;
  /**
   * The attributes users request to delete.
   *
   * @var string[]
   */
  public $deleteAttribute;
  /**
   * The IDPs the user request to delete.
   *
   * @var string[]
   */
  public $deleteProvider;
  /**
   * Whether to disable the user.
   *
   * @var bool
   */
  public $disableUser;
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
   * Mark the email as verified or not.
   *
   * @var bool
   */
  public $emailVerified;
  /**
   * The GITKit token of the authenticated user.
   *
   * @var string
   */
  public $idToken;
  /**
   * Instance id token of the app.
   *
   * @var string
   */
  public $instanceId;
  /**
   * Last login timestamp.
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
   * The out-of-band code of the change email request.
   *
   * @var string
   */
  public $oobCode;
  /**
   * The new password of the user.
   *
   * @var string
   */
  public $password;
  /**
   * Privileged caller can update user with specified phone number.
   *
   * @var string
   */
  public $phoneNumber;
  /**
   * The photo url of the user.
   *
   * @var string
   */
  public $photoUrl;
  /**
   * The associated IDPs of the user.
   *
   * @var string[]
   */
  public $provider;
  /**
   * Whether return sts id token and refresh token instead of gitkit token.
   *
   * @var bool
   */
  public $returnSecureToken;
  /**
   * Mark the user to upgrade to federated login.
   *
   * @var bool
   */
  public $upgradeToFederatedLogin;
  /**
   * Timestamp in seconds for valid login token.
   *
   * @var string
   */
  public $validSince;

  /**
   * The captcha challenge.
   *
   * @param string $captchaChallenge
   */
  public function setCaptchaChallenge($captchaChallenge)
  {
    $this->captchaChallenge = $captchaChallenge;
  }
  /**
   * @return string
   */
  public function getCaptchaChallenge()
  {
    return $this->captchaChallenge;
  }
  /**
   * Response to the captcha.
   *
   * @param string $captchaResponse
   */
  public function setCaptchaResponse($captchaResponse)
  {
    $this->captchaResponse = $captchaResponse;
  }
  /**
   * @return string
   */
  public function getCaptchaResponse()
  {
    return $this->captchaResponse;
  }
  /**
   * The timestamp when the account is created.
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
   * GCP project number of the requesting delegated app. Currently only intended
   * for Firebase V1 migration.
   *
   * @param string $delegatedProjectNumber
   */
  public function setDelegatedProjectNumber($delegatedProjectNumber)
  {
    $this->delegatedProjectNumber = $delegatedProjectNumber;
  }
  /**
   * @return string
   */
  public function getDelegatedProjectNumber()
  {
    return $this->delegatedProjectNumber;
  }
  /**
   * The attributes users request to delete.
   *
   * @param string[] $deleteAttribute
   */
  public function setDeleteAttribute($deleteAttribute)
  {
    $this->deleteAttribute = $deleteAttribute;
  }
  /**
   * @return string[]
   */
  public function getDeleteAttribute()
  {
    return $this->deleteAttribute;
  }
  /**
   * The IDPs the user request to delete.
   *
   * @param string[] $deleteProvider
   */
  public function setDeleteProvider($deleteProvider)
  {
    $this->deleteProvider = $deleteProvider;
  }
  /**
   * @return string[]
   */
  public function getDeleteProvider()
  {
    return $this->deleteProvider;
  }
  /**
   * Whether to disable the user.
   *
   * @param bool $disableUser
   */
  public function setDisableUser($disableUser)
  {
    $this->disableUser = $disableUser;
  }
  /**
   * @return bool
   */
  public function getDisableUser()
  {
    return $this->disableUser;
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
   * Mark the email as verified or not.
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
   * The GITKit token of the authenticated user.
   *
   * @param string $idToken
   */
  public function setIdToken($idToken)
  {
    $this->idToken = $idToken;
  }
  /**
   * @return string
   */
  public function getIdToken()
  {
    return $this->idToken;
  }
  /**
   * Instance id token of the app.
   *
   * @param string $instanceId
   */
  public function setInstanceId($instanceId)
  {
    $this->instanceId = $instanceId;
  }
  /**
   * @return string
   */
  public function getInstanceId()
  {
    return $this->instanceId;
  }
  /**
   * Last login timestamp.
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
   * The out-of-band code of the change email request.
   *
   * @param string $oobCode
   */
  public function setOobCode($oobCode)
  {
    $this->oobCode = $oobCode;
  }
  /**
   * @return string
   */
  public function getOobCode()
  {
    return $this->oobCode;
  }
  /**
   * The new password of the user.
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
   * Privileged caller can update user with specified phone number.
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
   * The photo url of the user.
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
   * The associated IDPs of the user.
   *
   * @param string[] $provider
   */
  public function setProvider($provider)
  {
    $this->provider = $provider;
  }
  /**
   * @return string[]
   */
  public function getProvider()
  {
    return $this->provider;
  }
  /**
   * Whether return sts id token and refresh token instead of gitkit token.
   *
   * @param bool $returnSecureToken
   */
  public function setReturnSecureToken($returnSecureToken)
  {
    $this->returnSecureToken = $returnSecureToken;
  }
  /**
   * @return bool
   */
  public function getReturnSecureToken()
  {
    return $this->returnSecureToken;
  }
  /**
   * Mark the user to upgrade to federated login.
   *
   * @param bool $upgradeToFederatedLogin
   */
  public function setUpgradeToFederatedLogin($upgradeToFederatedLogin)
  {
    $this->upgradeToFederatedLogin = $upgradeToFederatedLogin;
  }
  /**
   * @return bool
   */
  public function getUpgradeToFederatedLogin()
  {
    return $this->upgradeToFederatedLogin;
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IdentitytoolkitRelyingpartySetAccountInfoRequest::class, 'Google_Service_IdentityToolkit_IdentitytoolkitRelyingpartySetAccountInfoRequest');
