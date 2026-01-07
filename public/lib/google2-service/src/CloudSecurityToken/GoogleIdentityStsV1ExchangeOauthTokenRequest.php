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

namespace Google\Service\CloudSecurityToken;

class GoogleIdentityStsV1ExchangeOauthTokenRequest extends \Google\Model
{
  /**
   * @var string
   */
  public $clientId;
  /**
   * @var string
   */
  public $code;
  /**
   * @var string
   */
  public $codeVerifier;
  /**
   * @var string
   */
  public $grantType;
  /**
   * @var string
   */
  public $redirectUri;
  /**
   * @var string
   */
  public $refreshToken;
  /**
   * @var string
   */
  public $scope;

  /**
   * @param string
   */
  public function setClientId($clientId)
  {
    $this->clientId = $clientId;
  }
  /**
   * @return string
   */
  public function getClientId()
  {
    return $this->clientId;
  }
  /**
   * @param string
   */
  public function setCode($code)
  {
    $this->code = $code;
  }
  /**
   * @return string
   */
  public function getCode()
  {
    return $this->code;
  }
  /**
   * @param string
   */
  public function setCodeVerifier($codeVerifier)
  {
    $this->codeVerifier = $codeVerifier;
  }
  /**
   * @return string
   */
  public function getCodeVerifier()
  {
    return $this->codeVerifier;
  }
  /**
   * @param string
   */
  public function setGrantType($grantType)
  {
    $this->grantType = $grantType;
  }
  /**
   * @return string
   */
  public function getGrantType()
  {
    return $this->grantType;
  }
  /**
   * @param string
   */
  public function setRedirectUri($redirectUri)
  {
    $this->redirectUri = $redirectUri;
  }
  /**
   * @return string
   */
  public function getRedirectUri()
  {
    return $this->redirectUri;
  }
  /**
   * @param string
   */
  public function setRefreshToken($refreshToken)
  {
    $this->refreshToken = $refreshToken;
  }
  /**
   * @return string
   */
  public function getRefreshToken()
  {
    return $this->refreshToken;
  }
  /**
   * @param string
   */
  public function setScope($scope)
  {
    $this->scope = $scope;
  }
  /**
   * @return string
   */
  public function getScope()
  {
    return $this->scope;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleIdentityStsV1ExchangeOauthTokenRequest::class, 'Google_Service_CloudSecurityToken_GoogleIdentityStsV1ExchangeOauthTokenRequest');
