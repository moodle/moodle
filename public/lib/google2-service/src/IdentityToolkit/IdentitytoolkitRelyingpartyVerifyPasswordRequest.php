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

class IdentitytoolkitRelyingpartyVerifyPasswordRequest extends \Google\Model
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
   * GCP project number of the requesting delegated app. Currently only intended
   * for Firebase V1 migration.
   *
   * @var string
   */
  public $delegatedProjectNumber;
  /**
   * The email of the user.
   *
   * @var string
   */
  public $email;
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
   * The password inputed by the user.
   *
   * @var string
   */
  public $password;
  /**
   * The GITKit token for the non-trusted IDP, which is to be confirmed by the
   * user.
   *
   * @var string
   */
  public $pendingIdToken;
  /**
   * Whether return sts id token and refresh token instead of gitkit token.
   *
   * @var bool
   */
  public $returnSecureToken;
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
   * The password inputed by the user.
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
   * The GITKit token for the non-trusted IDP, which is to be confirmed by the
   * user.
   *
   * @param string $pendingIdToken
   */
  public function setPendingIdToken($pendingIdToken)
  {
    $this->pendingIdToken = $pendingIdToken;
  }
  /**
   * @return string
   */
  public function getPendingIdToken()
  {
    return $this->pendingIdToken;
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
class_alias(IdentitytoolkitRelyingpartyVerifyPasswordRequest::class, 'Google_Service_IdentityToolkit_IdentitytoolkitRelyingpartyVerifyPasswordRequest');
