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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowCxV3beta1WebhookGenericWebServiceOAuthConfig extends \Google\Collection
{
  protected $collection_key = 'scopes';
  /**
   * Required. The client ID provided by the 3rd party platform.
   *
   * @var string
   */
  public $clientId;
  /**
   * Optional. The client secret provided by the 3rd party platform.
   *
   * @var string
   */
  public $clientSecret;
  /**
   * Optional. The OAuth scopes to grant.
   *
   * @var string[]
   */
  public $scopes;
  /**
   * Optional. The name of the SecretManager secret version resource storing the
   * client secret. If this field is set, the `client_secret` field will be
   * ignored. Format: `projects/{project}/secrets/{secret}/versions/{version}`
   *
   * @var string
   */
  public $secretVersionForClientSecret;
  /**
   * Required. The token endpoint provided by the 3rd party platform to exchange
   * an access token.
   *
   * @var string
   */
  public $tokenEndpoint;

  /**
   * Required. The client ID provided by the 3rd party platform.
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
   * Optional. The client secret provided by the 3rd party platform.
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
   * Optional. The OAuth scopes to grant.
   *
   * @param string[] $scopes
   */
  public function setScopes($scopes)
  {
    $this->scopes = $scopes;
  }
  /**
   * @return string[]
   */
  public function getScopes()
  {
    return $this->scopes;
  }
  /**
   * Optional. The name of the SecretManager secret version resource storing the
   * client secret. If this field is set, the `client_secret` field will be
   * ignored. Format: `projects/{project}/secrets/{secret}/versions/{version}`
   *
   * @param string $secretVersionForClientSecret
   */
  public function setSecretVersionForClientSecret($secretVersionForClientSecret)
  {
    $this->secretVersionForClientSecret = $secretVersionForClientSecret;
  }
  /**
   * @return string
   */
  public function getSecretVersionForClientSecret()
  {
    return $this->secretVersionForClientSecret;
  }
  /**
   * Required. The token endpoint provided by the 3rd party platform to exchange
   * an access token.
   *
   * @param string $tokenEndpoint
   */
  public function setTokenEndpoint($tokenEndpoint)
  {
    $this->tokenEndpoint = $tokenEndpoint;
  }
  /**
   * @return string
   */
  public function getTokenEndpoint()
  {
    return $this->tokenEndpoint;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3beta1WebhookGenericWebServiceOAuthConfig::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3beta1WebhookGenericWebServiceOAuthConfig');
