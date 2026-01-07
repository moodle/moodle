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

class CreateAuthUriResponse extends \Google\Collection
{
  protected $collection_key = 'signinMethods';
  /**
   * all providers the user has once used to do federated login
   *
   * @var string[]
   */
  public $allProviders;
  /**
   * The URI used by the IDP to authenticate the user.
   *
   * @var string
   */
  public $authUri;
  /**
   * True if captcha is required.
   *
   * @var bool
   */
  public $captchaRequired;
  /**
   * True if the authUri is for user's existing provider.
   *
   * @var bool
   */
  public $forExistingProvider;
  /**
   * The fixed string identitytoolkit#CreateAuthUriResponse".
   *
   * @var string
   */
  public $kind;
  /**
   * The provider ID of the auth URI.
   *
   * @var string
   */
  public $providerId;
  /**
   * Whether the user is registered if the identifier is an email.
   *
   * @var bool
   */
  public $registered;
  /**
   * Session ID which should be passed in the following verifyAssertion request.
   *
   * @var string
   */
  public $sessionId;
  /**
   * All sign-in methods this user has used.
   *
   * @var string[]
   */
  public $signinMethods;

  /**
   * all providers the user has once used to do federated login
   *
   * @param string[] $allProviders
   */
  public function setAllProviders($allProviders)
  {
    $this->allProviders = $allProviders;
  }
  /**
   * @return string[]
   */
  public function getAllProviders()
  {
    return $this->allProviders;
  }
  /**
   * The URI used by the IDP to authenticate the user.
   *
   * @param string $authUri
   */
  public function setAuthUri($authUri)
  {
    $this->authUri = $authUri;
  }
  /**
   * @return string
   */
  public function getAuthUri()
  {
    return $this->authUri;
  }
  /**
   * True if captcha is required.
   *
   * @param bool $captchaRequired
   */
  public function setCaptchaRequired($captchaRequired)
  {
    $this->captchaRequired = $captchaRequired;
  }
  /**
   * @return bool
   */
  public function getCaptchaRequired()
  {
    return $this->captchaRequired;
  }
  /**
   * True if the authUri is for user's existing provider.
   *
   * @param bool $forExistingProvider
   */
  public function setForExistingProvider($forExistingProvider)
  {
    $this->forExistingProvider = $forExistingProvider;
  }
  /**
   * @return bool
   */
  public function getForExistingProvider()
  {
    return $this->forExistingProvider;
  }
  /**
   * The fixed string identitytoolkit#CreateAuthUriResponse".
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
   * The provider ID of the auth URI.
   *
   * @param string $providerId
   */
  public function setProviderId($providerId)
  {
    $this->providerId = $providerId;
  }
  /**
   * @return string
   */
  public function getProviderId()
  {
    return $this->providerId;
  }
  /**
   * Whether the user is registered if the identifier is an email.
   *
   * @param bool $registered
   */
  public function setRegistered($registered)
  {
    $this->registered = $registered;
  }
  /**
   * @return bool
   */
  public function getRegistered()
  {
    return $this->registered;
  }
  /**
   * Session ID which should be passed in the following verifyAssertion request.
   *
   * @param string $sessionId
   */
  public function setSessionId($sessionId)
  {
    $this->sessionId = $sessionId;
  }
  /**
   * @return string
   */
  public function getSessionId()
  {
    return $this->sessionId;
  }
  /**
   * All sign-in methods this user has used.
   *
   * @param string[] $signinMethods
   */
  public function setSigninMethods($signinMethods)
  {
    $this->signinMethods = $signinMethods;
  }
  /**
   * @return string[]
   */
  public function getSigninMethods()
  {
    return $this->signinMethods;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreateAuthUriResponse::class, 'Google_Service_IdentityToolkit_CreateAuthUriResponse');
