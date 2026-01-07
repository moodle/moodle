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

namespace Google\Service\Datastream;

class Oauth2ClientCredentials extends \Google\Model
{
  /**
   * Required. Client ID for Salesforce OAuth2 Client Credentials.
   *
   * @var string
   */
  public $clientId;
  /**
   * Optional. Client secret for Salesforce OAuth2 Client Credentials. Mutually
   * exclusive with the `secret_manager_stored_client_secret` field.
   *
   * @var string
   */
  public $clientSecret;
  /**
   * Optional. A reference to a Secret Manager resource name storing the
   * Salesforce OAuth2 client_secret. Mutually exclusive with the
   * `client_secret` field.
   *
   * @var string
   */
  public $secretManagerStoredClientSecret;

  /**
   * Required. Client ID for Salesforce OAuth2 Client Credentials.
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
   * Optional. Client secret for Salesforce OAuth2 Client Credentials. Mutually
   * exclusive with the `secret_manager_stored_client_secret` field.
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
   * Optional. A reference to a Secret Manager resource name storing the
   * Salesforce OAuth2 client_secret. Mutually exclusive with the
   * `client_secret` field.
   *
   * @param string $secretManagerStoredClientSecret
   */
  public function setSecretManagerStoredClientSecret($secretManagerStoredClientSecret)
  {
    $this->secretManagerStoredClientSecret = $secretManagerStoredClientSecret;
  }
  /**
   * @return string
   */
  public function getSecretManagerStoredClientSecret()
  {
    return $this->secretManagerStoredClientSecret;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Oauth2ClientCredentials::class, 'Google_Service_Datastream_Oauth2ClientCredentials');
