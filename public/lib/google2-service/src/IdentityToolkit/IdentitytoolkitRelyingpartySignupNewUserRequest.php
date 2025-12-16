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

class IdentitytoolkitRelyingpartySignupNewUserRequest extends \Google\Model
{
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
   * Whether to disable the user. Only can be used by service account.
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
   * Mark the email as verified or not. Only can be used by service account.
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
   * Privileged caller can create user with specified user id.
   *
   * @var string
   */
  public $localId;
  /**
   * The new password of the user.
   *
   * @var string
   */
  public $password;
  /**
   * Privileged caller can create user with specified phone number.
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
   * For multi-tenant use cases, in order to construct sign-in URL with the
   * correct IDP parameters, Firebear needs to know which Tenant to retrieve IDP
   * configs from.
   *
   * @var string
   */
  public $tenantId;
  /**
   * Tenant project number to be used for idp discovery.
   *
   * @var string
   */
  public $tenantProjectNumber;

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
   * Whether to disable the user. Only can be used by service account.
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
   * Mark the email as verified or not. Only can be used by service account.
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
   * Privileged caller can create user with specified user id.
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
   * Privileged caller can create user with specified phone number.
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
   * For multi-tenant use cases, in order to construct sign-in URL with the
   * correct IDP parameters, Firebear needs to know which Tenant to retrieve IDP
   * configs from.
   *
   * @param string $tenantId
   */
  public function setTenantId($tenantId)
  {
    $this->tenantId = $tenantId;
  }
  /**
   * @return string
   */
  public function getTenantId()
  {
    return $this->tenantId;
  }
  /**
   * Tenant project number to be used for idp discovery.
   *
   * @param string $tenantProjectNumber
   */
  public function setTenantProjectNumber($tenantProjectNumber)
  {
    $this->tenantProjectNumber = $tenantProjectNumber;
  }
  /**
   * @return string
   */
  public function getTenantProjectNumber()
  {
    return $this->tenantProjectNumber;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IdentitytoolkitRelyingpartySignupNewUserRequest::class, 'Google_Service_IdentityToolkit_IdentitytoolkitRelyingpartySignupNewUserRequest');
