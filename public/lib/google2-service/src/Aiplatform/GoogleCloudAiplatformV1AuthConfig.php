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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1AuthConfig extends \Google\Model
{
  public const AUTH_TYPE_AUTH_TYPE_UNSPECIFIED = 'AUTH_TYPE_UNSPECIFIED';
  /**
   * No Auth.
   */
  public const AUTH_TYPE_NO_AUTH = 'NO_AUTH';
  /**
   * API Key Auth.
   */
  public const AUTH_TYPE_API_KEY_AUTH = 'API_KEY_AUTH';
  /**
   * HTTP Basic Auth.
   */
  public const AUTH_TYPE_HTTP_BASIC_AUTH = 'HTTP_BASIC_AUTH';
  /**
   * Google Service Account Auth.
   */
  public const AUTH_TYPE_GOOGLE_SERVICE_ACCOUNT_AUTH = 'GOOGLE_SERVICE_ACCOUNT_AUTH';
  /**
   * OAuth auth.
   */
  public const AUTH_TYPE_OAUTH = 'OAUTH';
  /**
   * OpenID Connect (OIDC) Auth.
   */
  public const AUTH_TYPE_OIDC_AUTH = 'OIDC_AUTH';
  protected $apiKeyConfigType = GoogleCloudAiplatformV1AuthConfigApiKeyConfig::class;
  protected $apiKeyConfigDataType = '';
  /**
   * Type of auth scheme.
   *
   * @var string
   */
  public $authType;
  protected $googleServiceAccountConfigType = GoogleCloudAiplatformV1AuthConfigGoogleServiceAccountConfig::class;
  protected $googleServiceAccountConfigDataType = '';
  protected $httpBasicAuthConfigType = GoogleCloudAiplatformV1AuthConfigHttpBasicAuthConfig::class;
  protected $httpBasicAuthConfigDataType = '';
  protected $oauthConfigType = GoogleCloudAiplatformV1AuthConfigOauthConfig::class;
  protected $oauthConfigDataType = '';
  protected $oidcConfigType = GoogleCloudAiplatformV1AuthConfigOidcConfig::class;
  protected $oidcConfigDataType = '';

  /**
   * Config for API key auth.
   *
   * @param GoogleCloudAiplatformV1AuthConfigApiKeyConfig $apiKeyConfig
   */
  public function setApiKeyConfig(GoogleCloudAiplatformV1AuthConfigApiKeyConfig $apiKeyConfig)
  {
    $this->apiKeyConfig = $apiKeyConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1AuthConfigApiKeyConfig
   */
  public function getApiKeyConfig()
  {
    return $this->apiKeyConfig;
  }
  /**
   * Type of auth scheme.
   *
   * Accepted values: AUTH_TYPE_UNSPECIFIED, NO_AUTH, API_KEY_AUTH,
   * HTTP_BASIC_AUTH, GOOGLE_SERVICE_ACCOUNT_AUTH, OAUTH, OIDC_AUTH
   *
   * @param self::AUTH_TYPE_* $authType
   */
  public function setAuthType($authType)
  {
    $this->authType = $authType;
  }
  /**
   * @return self::AUTH_TYPE_*
   */
  public function getAuthType()
  {
    return $this->authType;
  }
  /**
   * Config for Google Service Account auth.
   *
   * @param GoogleCloudAiplatformV1AuthConfigGoogleServiceAccountConfig $googleServiceAccountConfig
   */
  public function setGoogleServiceAccountConfig(GoogleCloudAiplatformV1AuthConfigGoogleServiceAccountConfig $googleServiceAccountConfig)
  {
    $this->googleServiceAccountConfig = $googleServiceAccountConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1AuthConfigGoogleServiceAccountConfig
   */
  public function getGoogleServiceAccountConfig()
  {
    return $this->googleServiceAccountConfig;
  }
  /**
   * Config for HTTP Basic auth.
   *
   * @param GoogleCloudAiplatformV1AuthConfigHttpBasicAuthConfig $httpBasicAuthConfig
   */
  public function setHttpBasicAuthConfig(GoogleCloudAiplatformV1AuthConfigHttpBasicAuthConfig $httpBasicAuthConfig)
  {
    $this->httpBasicAuthConfig = $httpBasicAuthConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1AuthConfigHttpBasicAuthConfig
   */
  public function getHttpBasicAuthConfig()
  {
    return $this->httpBasicAuthConfig;
  }
  /**
   * Config for user oauth.
   *
   * @param GoogleCloudAiplatformV1AuthConfigOauthConfig $oauthConfig
   */
  public function setOauthConfig(GoogleCloudAiplatformV1AuthConfigOauthConfig $oauthConfig)
  {
    $this->oauthConfig = $oauthConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1AuthConfigOauthConfig
   */
  public function getOauthConfig()
  {
    return $this->oauthConfig;
  }
  /**
   * Config for user OIDC auth.
   *
   * @param GoogleCloudAiplatformV1AuthConfigOidcConfig $oidcConfig
   */
  public function setOidcConfig(GoogleCloudAiplatformV1AuthConfigOidcConfig $oidcConfig)
  {
    $this->oidcConfig = $oidcConfig;
  }
  /**
   * @return GoogleCloudAiplatformV1AuthConfigOidcConfig
   */
  public function getOidcConfig()
  {
    return $this->oidcConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1AuthConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1AuthConfig');
