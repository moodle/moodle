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

namespace Google\Service\CloudIdentity;

class OidcRpConfig extends \Google\Collection
{
  protected $collection_key = 'redirectUris';
  /**
   * OAuth2 client ID for OIDC.
   *
   * @var string
   */
  public $clientId;
  /**
   * Input only. OAuth2 client secret for OIDC.
   *
   * @var string
   */
  public $clientSecret;
  /**
   * Output only. The URL(s) that this client may use in authentication
   * requests.
   *
   * @var string[]
   */
  public $redirectUris;

  /**
   * OAuth2 client ID for OIDC.
   *
   * @param string $clientId
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
   * Input only. OAuth2 client secret for OIDC.
   *
   * @param string $clientSecret
   */
  public function setClientSecret($clientSecret)
  {
    $this->clientSecret = $clientSecret;
  }
  /**
   * @return string
   */
  public function getClientSecret()
  {
    return $this->clientSecret;
  }
  /**
   * Output only. The URL(s) that this client may use in authentication
   * requests.
   *
   * @param string[] $redirectUris
   */
  public function setRedirectUris($redirectUris)
  {
    $this->redirectUris = $redirectUris;
  }
  /**
   * @return string[]
   */
  public function getRedirectUris()
  {
    return $this->redirectUris;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OidcRpConfig::class, 'Google_Service_CloudIdentity_OidcRpConfig');
