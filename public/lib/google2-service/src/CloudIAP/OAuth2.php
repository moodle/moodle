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

namespace Google\Service\CloudIAP;

class OAuth2 extends \Google\Model
{
  /**
   * The OAuth 2.0 client ID registered in the workforce identity federation
   * OAuth 2.0 Server.
   *
   * @var string
   */
  public $clientId;
  /**
   * Input only. The OAuth 2.0 client secret created while registering the
   * client ID.
   *
   * @var string
   */
  public $clientSecret;
  /**
   * Output only. SHA256 hash value for the client secret. This field is
   * returned by IAP when the settings are retrieved.
   *
   * @var string
   */
  public $clientSecretSha256;

  /**
   * The OAuth 2.0 client ID registered in the workforce identity federation
   * OAuth 2.0 Server.
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
   * Input only. The OAuth 2.0 client secret created while registering the
   * client ID.
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
   * Output only. SHA256 hash value for the client secret. This field is
   * returned by IAP when the settings are retrieved.
   *
   * @param string $clientSecretSha256
   */
  public function setClientSecretSha256($clientSecretSha256)
  {
    $this->clientSecretSha256 = $clientSecretSha256;
  }
  /**
   * @return string
   */
  public function getClientSecretSha256()
  {
    return $this->clientSecretSha256;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OAuth2::class, 'Google_Service_CloudIAP_OAuth2');
