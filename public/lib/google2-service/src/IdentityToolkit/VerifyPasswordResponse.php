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

class VerifyPasswordResponse extends \Google\Model
{
  /**
   * The name of the user.
   *
   * @var string
   */
  public $displayName;
  /**
   * The email returned by the IdP. NOTE: The federated login user may not own
   * the email.
   *
   * @var string
   */
  public $email;
  /**
   * If idToken is STS id token, then this field will be expiration time of STS
   * id token in seconds.
   *
   * @var string
   */
  public $expiresIn;
  /**
   * The GITKit token for authenticated user.
   *
   * @var string
   */
  public $idToken;
  /**
   * The fixed string "identitytoolkit#VerifyPasswordResponse".
   *
   * @var string
   */
  public $kind;
  /**
   * The RP local ID if it's already been mapped to the IdP account identified
   * by the federated ID.
   *
   * @var string
   */
  public $localId;
  /**
   * The OAuth2 access token.
   *
   * @var string
   */
  public $oauthAccessToken;
  /**
   * The OAuth2 authorization code.
   *
   * @var string
   */
  public $oauthAuthorizationCode;
  /**
   * The lifetime in seconds of the OAuth2 access token.
   *
   * @var int
   */
  public $oauthExpireIn;
  /**
   * The URI of the user's photo at IdP
   *
   * @var string
   */
  public $photoUrl;
  /**
   * If idToken is STS id token, then this field will be refresh token.
   *
   * @var string
   */
  public $refreshToken;
  /**
   * Whether the email is registered.
   *
   * @var bool
   */
  public $registered;

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
   * The email returned by the IdP. NOTE: The federated login user may not own
   * the email.
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
   * If idToken is STS id token, then this field will be expiration time of STS
   * id token in seconds.
   *
   * @param string $expiresIn
   */
  public function setExpiresIn($expiresIn)
  {
    $this->expiresIn = $expiresIn;
  }
  /**
   * @return string
   */
  public function getExpiresIn()
  {
    return $this->expiresIn;
  }
  /**
   * The GITKit token for authenticated user.
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
   * The fixed string "identitytoolkit#VerifyPasswordResponse".
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
   * The RP local ID if it's already been mapped to the IdP account identified
   * by the federated ID.
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
   * The OAuth2 access token.
   *
   * @param string $oauthAccessToken
   */
  public function setOauthAccessToken($oauthAccessToken)
  {
    $this->oauthAccessToken = $oauthAccessToken;
  }
  /**
   * @return string
   */
  public function getOauthAccessToken()
  {
    return $this->oauthAccessToken;
  }
  /**
   * The OAuth2 authorization code.
   *
   * @param string $oauthAuthorizationCode
   */
  public function setOauthAuthorizationCode($oauthAuthorizationCode)
  {
    $this->oauthAuthorizationCode = $oauthAuthorizationCode;
  }
  /**
   * @return string
   */
  public function getOauthAuthorizationCode()
  {
    return $this->oauthAuthorizationCode;
  }
  /**
   * The lifetime in seconds of the OAuth2 access token.
   *
   * @param int $oauthExpireIn
   */
  public function setOauthExpireIn($oauthExpireIn)
  {
    $this->oauthExpireIn = $oauthExpireIn;
  }
  /**
   * @return int
   */
  public function getOauthExpireIn()
  {
    return $this->oauthExpireIn;
  }
  /**
   * The URI of the user's photo at IdP
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
   * If idToken is STS id token, then this field will be refresh token.
   *
   * @param string $refreshToken
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
   * Whether the email is registered.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VerifyPasswordResponse::class, 'Google_Service_IdentityToolkit_VerifyPasswordResponse');
