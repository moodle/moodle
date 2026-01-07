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

namespace Google\Service\APIhub;

class GoogleCloudApihubV1AuthConfig extends \Google\Model
{
  /**
   * Authentication type not specified.
   */
  public const AUTH_TYPE_AUTH_TYPE_UNSPECIFIED = 'AUTH_TYPE_UNSPECIFIED';
  /**
   * No authentication.
   */
  public const AUTH_TYPE_NO_AUTH = 'NO_AUTH';
  /**
   * Google service account authentication.
   */
  public const AUTH_TYPE_GOOGLE_SERVICE_ACCOUNT = 'GOOGLE_SERVICE_ACCOUNT';
  /**
   * Username and password authentication.
   */
  public const AUTH_TYPE_USER_PASSWORD = 'USER_PASSWORD';
  /**
   * API Key authentication.
   */
  public const AUTH_TYPE_API_KEY = 'API_KEY';
  /**
   * Oauth 2.0 client credentials grant authentication.
   */
  public const AUTH_TYPE_OAUTH2_CLIENT_CREDENTIALS = 'OAUTH2_CLIENT_CREDENTIALS';
  protected $apiKeyConfigType = GoogleCloudApihubV1ApiKeyConfig::class;
  protected $apiKeyConfigDataType = '';
  /**
   * Required. The authentication type.
   *
   * @var string
   */
  public $authType;
  protected $googleServiceAccountConfigType = GoogleCloudApihubV1GoogleServiceAccountConfig::class;
  protected $googleServiceAccountConfigDataType = '';
  protected $oauth2ClientCredentialsConfigType = GoogleCloudApihubV1Oauth2ClientCredentialsConfig::class;
  protected $oauth2ClientCredentialsConfigDataType = '';
  protected $userPasswordConfigType = GoogleCloudApihubV1UserPasswordConfig::class;
  protected $userPasswordConfigDataType = '';

  /**
   * Api Key Config.
   *
   * @param GoogleCloudApihubV1ApiKeyConfig $apiKeyConfig
   */
  public function setApiKeyConfig(GoogleCloudApihubV1ApiKeyConfig $apiKeyConfig)
  {
    $this->apiKeyConfig = $apiKeyConfig;
  }
  /**
   * @return GoogleCloudApihubV1ApiKeyConfig
   */
  public function getApiKeyConfig()
  {
    return $this->apiKeyConfig;
  }
  /**
   * Required. The authentication type.
   *
   * Accepted values: AUTH_TYPE_UNSPECIFIED, NO_AUTH, GOOGLE_SERVICE_ACCOUNT,
   * USER_PASSWORD, API_KEY, OAUTH2_CLIENT_CREDENTIALS
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
   * Google Service Account.
   *
   * @param GoogleCloudApihubV1GoogleServiceAccountConfig $googleServiceAccountConfig
   */
  public function setGoogleServiceAccountConfig(GoogleCloudApihubV1GoogleServiceAccountConfig $googleServiceAccountConfig)
  {
    $this->googleServiceAccountConfig = $googleServiceAccountConfig;
  }
  /**
   * @return GoogleCloudApihubV1GoogleServiceAccountConfig
   */
  public function getGoogleServiceAccountConfig()
  {
    return $this->googleServiceAccountConfig;
  }
  /**
   * Oauth2.0 Client Credentials.
   *
   * @param GoogleCloudApihubV1Oauth2ClientCredentialsConfig $oauth2ClientCredentialsConfig
   */
  public function setOauth2ClientCredentialsConfig(GoogleCloudApihubV1Oauth2ClientCredentialsConfig $oauth2ClientCredentialsConfig)
  {
    $this->oauth2ClientCredentialsConfig = $oauth2ClientCredentialsConfig;
  }
  /**
   * @return GoogleCloudApihubV1Oauth2ClientCredentialsConfig
   */
  public function getOauth2ClientCredentialsConfig()
  {
    return $this->oauth2ClientCredentialsConfig;
  }
  /**
   * User Password.
   *
   * @param GoogleCloudApihubV1UserPasswordConfig $userPasswordConfig
   */
  public function setUserPasswordConfig(GoogleCloudApihubV1UserPasswordConfig $userPasswordConfig)
  {
    $this->userPasswordConfig = $userPasswordConfig;
  }
  /**
   * @return GoogleCloudApihubV1UserPasswordConfig
   */
  public function getUserPasswordConfig()
  {
    return $this->userPasswordConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1AuthConfig::class, 'Google_Service_APIhub_GoogleCloudApihubV1AuthConfig');
