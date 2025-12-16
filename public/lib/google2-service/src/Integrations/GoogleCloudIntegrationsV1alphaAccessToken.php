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

namespace Google\Service\Integrations;

class GoogleCloudIntegrationsV1alphaAccessToken extends \Google\Model
{
  /**
   * The access token encapsulating the security identity of a process or
   * thread.
   *
   * @var string
   */
  public $accessToken;
  /**
   * Required. The approximate time until the access token retrieved is valid.
   *
   * @var string
   */
  public $accessTokenExpireTime;
  /**
   * If the access token will expire, use the refresh token to obtain another
   * access token.
   *
   * @var string
   */
  public $refreshToken;
  /**
   * The approximate time until the refresh token retrieved is valid.
   *
   * @var string
   */
  public $refreshTokenExpireTime;
  /**
   * Only support "bearer" token in v1 as bearer token is the predominant type
   * used with OAuth 2.0.
   *
   * @var string
   */
  public $tokenType;

  /**
   * The access token encapsulating the security identity of a process or
   * thread.
   *
   * @param string $accessToken
   */
  public function setAccessToken($accessToken)
  {
    $this->accessToken = $accessToken;
  }
  /**
   * @return string
   */
  public function getAccessToken()
  {
    return $this->accessToken;
  }
  /**
   * Required. The approximate time until the access token retrieved is valid.
   *
   * @param string $accessTokenExpireTime
   */
  public function setAccessTokenExpireTime($accessTokenExpireTime)
  {
    $this->accessTokenExpireTime = $accessTokenExpireTime;
  }
  /**
   * @return string
   */
  public function getAccessTokenExpireTime()
  {
    return $this->accessTokenExpireTime;
  }
  /**
   * If the access token will expire, use the refresh token to obtain another
   * access token.
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
   * The approximate time until the refresh token retrieved is valid.
   *
   * @param string $refreshTokenExpireTime
   */
  public function setRefreshTokenExpireTime($refreshTokenExpireTime)
  {
    $this->refreshTokenExpireTime = $refreshTokenExpireTime;
  }
  /**
   * @return string
   */
  public function getRefreshTokenExpireTime()
  {
    return $this->refreshTokenExpireTime;
  }
  /**
   * Only support "bearer" token in v1 as bearer token is the predominant type
   * used with OAuth 2.0.
   *
   * @param string $tokenType
   */
  public function setTokenType($tokenType)
  {
    $this->tokenType = $tokenType;
  }
  /**
   * @return string
   */
  public function getTokenType()
  {
    return $this->tokenType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudIntegrationsV1alphaAccessToken::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaAccessToken');
