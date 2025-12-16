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

namespace Google\Service\Integrations;

class GoogleCloudConnectorsV1AuthConfig extends \Google\Collection
{
  /**
   * Authentication type not specified.
   */
  public const AUTH_TYPE_AUTH_TYPE_UNSPECIFIED = 'AUTH_TYPE_UNSPECIFIED';
  /**
   * Username and Password Authentication.
   */
  public const AUTH_TYPE_USER_PASSWORD = 'USER_PASSWORD';
  /**
   * JSON Web Token (JWT) Profile for Oauth 2.0 Authorization Grant based
   * authentication
   */
  public const AUTH_TYPE_OAUTH2_JWT_BEARER = 'OAUTH2_JWT_BEARER';
  /**
   * Oauth 2.0 Client Credentials Grant Authentication
   */
  public const AUTH_TYPE_OAUTH2_CLIENT_CREDENTIALS = 'OAUTH2_CLIENT_CREDENTIALS';
  /**
   * SSH Public Key Authentication
   */
  public const AUTH_TYPE_SSH_PUBLIC_KEY = 'SSH_PUBLIC_KEY';
  /**
   * Oauth 2.0 Authorization Code Flow
   */
  public const AUTH_TYPE_OAUTH2_AUTH_CODE_FLOW = 'OAUTH2_AUTH_CODE_FLOW';
  /**
   * Google authentication
   */
  public const AUTH_TYPE_GOOGLE_AUTHENTICATION = 'GOOGLE_AUTHENTICATION';
  /**
   * Oauth 2.0 Authorization Code Flow with Google Provided OAuth Client
   */
  public const AUTH_TYPE_OAUTH2_AUTH_CODE_FLOW_GOOGLE_MANAGED = 'OAUTH2_AUTH_CODE_FLOW_GOOGLE_MANAGED';
  protected $collection_key = 'additionalVariables';
  protected $additionalVariablesType = GoogleCloudConnectorsV1ConfigVariable::class;
  protected $additionalVariablesDataType = 'array';
  /**
   * Optional. Identifier key for auth config
   *
   * @var string
   */
  public $authKey;
  /**
   * Optional. The type of authentication configured.
   *
   * @var string
   */
  public $authType;
  protected $oauth2AuthCodeFlowType = GoogleCloudConnectorsV1AuthConfigOauth2AuthCodeFlow::class;
  protected $oauth2AuthCodeFlowDataType = '';
  protected $oauth2AuthCodeFlowGoogleManagedType = GoogleCloudConnectorsV1AuthConfigOauth2AuthCodeFlowGoogleManaged::class;
  protected $oauth2AuthCodeFlowGoogleManagedDataType = '';
  protected $oauth2ClientCredentialsType = GoogleCloudConnectorsV1AuthConfigOauth2ClientCredentials::class;
  protected $oauth2ClientCredentialsDataType = '';
  protected $oauth2JwtBearerType = GoogleCloudConnectorsV1AuthConfigOauth2JwtBearer::class;
  protected $oauth2JwtBearerDataType = '';
  protected $sshPublicKeyType = GoogleCloudConnectorsV1AuthConfigSshPublicKey::class;
  protected $sshPublicKeyDataType = '';
  protected $userPasswordType = GoogleCloudConnectorsV1AuthConfigUserPassword::class;
  protected $userPasswordDataType = '';

  /**
   * Optional. List containing additional auth configs.
   *
   * @param GoogleCloudConnectorsV1ConfigVariable[] $additionalVariables
   */
  public function setAdditionalVariables($additionalVariables)
  {
    $this->additionalVariables = $additionalVariables;
  }
  /**
   * @return GoogleCloudConnectorsV1ConfigVariable[]
   */
  public function getAdditionalVariables()
  {
    return $this->additionalVariables;
  }
  /**
   * Optional. Identifier key for auth config
   *
   * @param string $authKey
   */
  public function setAuthKey($authKey)
  {
    $this->authKey = $authKey;
  }
  /**
   * @return string
   */
  public function getAuthKey()
  {
    return $this->authKey;
  }
  /**
   * Optional. The type of authentication configured.
   *
   * Accepted values: AUTH_TYPE_UNSPECIFIED, USER_PASSWORD, OAUTH2_JWT_BEARER,
   * OAUTH2_CLIENT_CREDENTIALS, SSH_PUBLIC_KEY, OAUTH2_AUTH_CODE_FLOW,
   * GOOGLE_AUTHENTICATION, OAUTH2_AUTH_CODE_FLOW_GOOGLE_MANAGED
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
   * Oauth2AuthCodeFlow.
   *
   * @param GoogleCloudConnectorsV1AuthConfigOauth2AuthCodeFlow $oauth2AuthCodeFlow
   */
  public function setOauth2AuthCodeFlow(GoogleCloudConnectorsV1AuthConfigOauth2AuthCodeFlow $oauth2AuthCodeFlow)
  {
    $this->oauth2AuthCodeFlow = $oauth2AuthCodeFlow;
  }
  /**
   * @return GoogleCloudConnectorsV1AuthConfigOauth2AuthCodeFlow
   */
  public function getOauth2AuthCodeFlow()
  {
    return $this->oauth2AuthCodeFlow;
  }
  /**
   * Oauth2AuthCodeFlowGoogleManaged.
   *
   * @param GoogleCloudConnectorsV1AuthConfigOauth2AuthCodeFlowGoogleManaged $oauth2AuthCodeFlowGoogleManaged
   */
  public function setOauth2AuthCodeFlowGoogleManaged(GoogleCloudConnectorsV1AuthConfigOauth2AuthCodeFlowGoogleManaged $oauth2AuthCodeFlowGoogleManaged)
  {
    $this->oauth2AuthCodeFlowGoogleManaged = $oauth2AuthCodeFlowGoogleManaged;
  }
  /**
   * @return GoogleCloudConnectorsV1AuthConfigOauth2AuthCodeFlowGoogleManaged
   */
  public function getOauth2AuthCodeFlowGoogleManaged()
  {
    return $this->oauth2AuthCodeFlowGoogleManaged;
  }
  /**
   * Oauth2ClientCredentials.
   *
   * @param GoogleCloudConnectorsV1AuthConfigOauth2ClientCredentials $oauth2ClientCredentials
   */
  public function setOauth2ClientCredentials(GoogleCloudConnectorsV1AuthConfigOauth2ClientCredentials $oauth2ClientCredentials)
  {
    $this->oauth2ClientCredentials = $oauth2ClientCredentials;
  }
  /**
   * @return GoogleCloudConnectorsV1AuthConfigOauth2ClientCredentials
   */
  public function getOauth2ClientCredentials()
  {
    return $this->oauth2ClientCredentials;
  }
  /**
   * Oauth2JwtBearer.
   *
   * @param GoogleCloudConnectorsV1AuthConfigOauth2JwtBearer $oauth2JwtBearer
   */
  public function setOauth2JwtBearer(GoogleCloudConnectorsV1AuthConfigOauth2JwtBearer $oauth2JwtBearer)
  {
    $this->oauth2JwtBearer = $oauth2JwtBearer;
  }
  /**
   * @return GoogleCloudConnectorsV1AuthConfigOauth2JwtBearer
   */
  public function getOauth2JwtBearer()
  {
    return $this->oauth2JwtBearer;
  }
  /**
   * SSH Public Key.
   *
   * @param GoogleCloudConnectorsV1AuthConfigSshPublicKey $sshPublicKey
   */
  public function setSshPublicKey(GoogleCloudConnectorsV1AuthConfigSshPublicKey $sshPublicKey)
  {
    $this->sshPublicKey = $sshPublicKey;
  }
  /**
   * @return GoogleCloudConnectorsV1AuthConfigSshPublicKey
   */
  public function getSshPublicKey()
  {
    return $this->sshPublicKey;
  }
  /**
   * UserPassword.
   *
   * @param GoogleCloudConnectorsV1AuthConfigUserPassword $userPassword
   */
  public function setUserPassword(GoogleCloudConnectorsV1AuthConfigUserPassword $userPassword)
  {
    $this->userPassword = $userPassword;
  }
  /**
   * @return GoogleCloudConnectorsV1AuthConfigUserPassword
   */
  public function getUserPassword()
  {
    return $this->userPassword;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudConnectorsV1AuthConfig::class, 'Google_Service_Integrations_GoogleCloudConnectorsV1AuthConfig');
