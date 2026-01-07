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

namespace Google\Service\AIPlatformNotebooks;

class GenerateAccessTokenResponse extends \Google\Model
{
  protected $internal_gapi_mappings = [
        "accessToken" => "access_token",
        "expiresIn" => "expires_in",
        "tokenType" => "token_type",
  ];
  /**
   * Short-lived access token string which may be used to access Google APIs.
   *
   * @var string
   */
  public $accessToken;
  /**
   * The time in seconds when the access token expires. Typically that's 3600.
   *
   * @var int
   */
  public $expiresIn;
  /**
   * Space-separated list of scopes contained in the returned token.
   * https://cloud.google.com/docs/authentication/token-types#access-contents
   *
   * @var string
   */
  public $scope;
  /**
   * Type of the returned access token (e.g. "Bearer"). It specifies how the
   * token must be used. Bearer tokens may be used by any entity without proof
   * of identity.
   *
   * @var string
   */
  public $tokenType;

  /**
   * Short-lived access token string which may be used to access Google APIs.
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
   * The time in seconds when the access token expires. Typically that's 3600.
   *
   * @param int $expiresIn
   */
  public function setExpiresIn($expiresIn)
  {
    $this->expiresIn = $expiresIn;
  }
  /**
   * @return int
   */
  public function getExpiresIn()
  {
    return $this->expiresIn;
  }
  /**
   * Space-separated list of scopes contained in the returned token.
   * https://cloud.google.com/docs/authentication/token-types#access-contents
   *
   * @param string $scope
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
  /**
   * Type of the returned access token (e.g. "Bearer"). It specifies how the
   * token must be used. Bearer tokens may be used by any entity without proof
   * of identity.
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
class_alias(GenerateAccessTokenResponse::class, 'Google_Service_AIPlatformNotebooks_GenerateAccessTokenResponse');
