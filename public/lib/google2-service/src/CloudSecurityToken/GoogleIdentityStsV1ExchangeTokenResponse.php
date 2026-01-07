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

class GoogleIdentityStsV1ExchangeTokenResponse extends \Google\Model
{
  protected $internal_gapi_mappings = [
        "accessBoundarySessionKey" => "access_boundary_session_key",
        "accessToken" => "access_token",
        "expiresIn" => "expires_in",
        "issuedTokenType" => "issued_token_type",
        "tokenType" => "token_type",
  ];
  /**
   * The access boundary session key. This key is used along with the access
   * boundary intermediary token to generate Credential Access Boundary tokens
   * at client side. This field is absent when the `requested_token_type` from
   * the request is not `urn:ietf:params:oauth:token-
   * type:access_boundary_intermediary_token`.
   *
   * @var string
   */
  public $accessBoundarySessionKey;
  /**
   * An OAuth 2.0 security token, issued by Google, in response to the token
   * exchange request. Tokens can vary in size, depending in part on the size of
   * mapped claims, up to a maximum of 12288 bytes (12 KB). Google reserves the
   * right to change the token size and the maximum length at any time.
   *
   * @var string
   */
  public $accessToken;
  /**
   * The amount of time, in seconds, between the time when the access token was
   * issued and the time when the access token will expire. This field is absent
   * when the `subject_token` in the request is a a short-lived access token for
   * a Cloud Identity or Google Workspace user account. In this case, the access
   * token has the same expiration time as the `subject_token`.
   *
   * @var int
   */
  public $expiresIn;
  /**
   * The token type. Always matches the value of `requested_token_type` from the
   * request.
   *
   * @var string
   */
  public $issuedTokenType;
  /**
   * The type of access token. Always has the value `Bearer`.
   *
   * @var string
   */
  public $tokenType;

  /**
   * The access boundary session key. This key is used along with the access
   * boundary intermediary token to generate Credential Access Boundary tokens
   * at client side. This field is absent when the `requested_token_type` from
   * the request is not `urn:ietf:params:oauth:token-
   * type:access_boundary_intermediary_token`.
   *
   * @param string $accessBoundarySessionKey
   */
  public function setAccessBoundarySessionKey($accessBoundarySessionKey)
  {
    $this->accessBoundarySessionKey = $accessBoundarySessionKey;
  }
  /**
   * @return string
   */
  public function getAccessBoundarySessionKey()
  {
    return $this->accessBoundarySessionKey;
  }
  /**
   * An OAuth 2.0 security token, issued by Google, in response to the token
   * exchange request. Tokens can vary in size, depending in part on the size of
   * mapped claims, up to a maximum of 12288 bytes (12 KB). Google reserves the
   * right to change the token size and the maximum length at any time.
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
   * The amount of time, in seconds, between the time when the access token was
   * issued and the time when the access token will expire. This field is absent
   * when the `subject_token` in the request is a a short-lived access token for
   * a Cloud Identity or Google Workspace user account. In this case, the access
   * token has the same expiration time as the `subject_token`.
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
   * The token type. Always matches the value of `requested_token_type` from the
   * request.
   *
   * @param string $issuedTokenType
   */
  public function setIssuedTokenType($issuedTokenType)
  {
    $this->issuedTokenType = $issuedTokenType;
  }
  /**
   * @return string
   */
  public function getIssuedTokenType()
  {
    return $this->issuedTokenType;
  }
  /**
   * The type of access token. Always has the value `Bearer`.
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
class_alias(GoogleIdentityStsV1ExchangeTokenResponse::class, 'Google_Service_CloudSecurityToken_GoogleIdentityStsV1ExchangeTokenResponse');
