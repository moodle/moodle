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

class GoogleCloudDialogflowCxV3ToolAuthenticationOAuthConfig extends \Google\Collection
{
  /**
   * Default value. This value is unused.
   */
  public const OAUTH_GRANT_TYPE_OAUTH_GRANT_TYPE_UNSPECIFIED = 'OAUTH_GRANT_TYPE_UNSPECIFIED';
  /**
   * Represents the [client credential flow](https://oauth.net/2/grant-
   * types/client-credentials).
   */
  public const OAUTH_GRANT_TYPE_CLIENT_CREDENTIAL = 'CLIENT_CREDENTIAL';
  protected $collection_key = 'scopes';
  /**
   * Required. The client ID from the OAuth provider.
   *
   * @var string
   */
  public $clientId;
  /**
   * Optional. The client secret from the OAuth provider. If the
   * `secret_version_for_client_secret` field is set, this field will be
   * ignored.
   *
   * @var string
   */
  public $clientSecret;
  /**
   * Required. OAuth grant types.
   *
   * @var string
   */
  public $oauthGrantType;
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
   * Required. The token endpoint in the OAuth provider to exchange for an
   * access token.
   *
   * @var string
   */
  public $tokenEndpoint;

  /**
   * Required. The client ID from the OAuth provider.
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
   * Optional. The client secret from the OAuth provider. If the
   * `secret_version_for_client_secret` field is set, this field will be
   * ignored.
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
   * Required. OAuth grant types.
   *
   * Accepted values: OAUTH_GRANT_TYPE_UNSPECIFIED, CLIENT_CREDENTIAL
   *
   * @param self::OAUTH_GRANT_TYPE_* $oauthGrantType
   */
  public function setOauthGrantType($oauthGrantType)
  {
    $this->oauthGrantType = $oauthGrantType;
  }
  /**
   * @return self::OAUTH_GRANT_TYPE_*
   */
  public function getOauthGrantType()
  {
    return $this->oauthGrantType;
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
   * Required. The token endpoint in the OAuth provider to exchange for an
   * access token.
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
class_alias(GoogleCloudDialogflowCxV3ToolAuthenticationOAuthConfig::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3ToolAuthenticationOAuthConfig');
