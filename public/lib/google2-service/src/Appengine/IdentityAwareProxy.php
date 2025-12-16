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

namespace Google\Service\Appengine;

class IdentityAwareProxy extends \Google\Model
{
  /**
   * Whether the serving infrastructure will authenticate and authorize all
   * incoming requests.If true, the oauth2_client_id and oauth2_client_secret
   * fields must be non-empty.
   *
   * @var bool
   */
  public $enabled;
  /**
   * OAuth2 client ID to use for the authentication flow.
   *
   * @var string
   */
  public $oauth2ClientId;
  /**
   * OAuth2 client secret to use for the authentication flow.For security
   * reasons, this value cannot be retrieved via the API. Instead, the SHA-256
   * hash of the value is returned in the oauth2_client_secret_sha256
   * field.@InputOnly
   *
   * @var string
   */
  public $oauth2ClientSecret;
  /**
   * Output only. Hex-encoded SHA-256 hash of the client secret.@OutputOnly
   *
   * @var string
   */
  public $oauth2ClientSecretSha256;

  /**
   * Whether the serving infrastructure will authenticate and authorize all
   * incoming requests.If true, the oauth2_client_id and oauth2_client_secret
   * fields must be non-empty.
   *
   * @param bool $enabled
   */
  public function setEnabled($enabled)
  {
    $this->enabled = $enabled;
  }
  /**
   * @return bool
   */
  public function getEnabled()
  {
    return $this->enabled;
  }
  /**
   * OAuth2 client ID to use for the authentication flow.
   *
   * @param string $oauth2ClientId
   */
  public function setOauth2ClientId($oauth2ClientId)
  {
    $this->oauth2ClientId = $oauth2ClientId;
  }
  /**
   * @return string
   */
  public function getOauth2ClientId()
  {
    return $this->oauth2ClientId;
  }
  /**
   * OAuth2 client secret to use for the authentication flow.For security
   * reasons, this value cannot be retrieved via the API. Instead, the SHA-256
   * hash of the value is returned in the oauth2_client_secret_sha256
   * field.@InputOnly
   *
   * @param string $oauth2ClientSecret
   */
  public function setOauth2ClientSecret($oauth2ClientSecret)
  {
    $this->oauth2ClientSecret = $oauth2ClientSecret;
  }
  /**
   * @return string
   */
  public function getOauth2ClientSecret()
  {
    return $this->oauth2ClientSecret;
  }
  /**
   * Output only. Hex-encoded SHA-256 hash of the client secret.@OutputOnly
   *
   * @param string $oauth2ClientSecretSha256
   */
  public function setOauth2ClientSecretSha256($oauth2ClientSecretSha256)
  {
    $this->oauth2ClientSecretSha256 = $oauth2ClientSecretSha256;
  }
  /**
   * @return string
   */
  public function getOauth2ClientSecretSha256()
  {
    return $this->oauth2ClientSecretSha256;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IdentityAwareProxy::class, 'Google_Service_Appengine_IdentityAwareProxy');
