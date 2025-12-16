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

class GoogleCloudIntegrationsV1alphaCredential extends \Google\Model
{
  /**
   * Unspecified credential type
   */
  public const CREDENTIAL_TYPE_CREDENTIAL_TYPE_UNSPECIFIED = 'CREDENTIAL_TYPE_UNSPECIFIED';
  /**
   * Regular username/password pair.
   */
  public const CREDENTIAL_TYPE_USERNAME_AND_PASSWORD = 'USERNAME_AND_PASSWORD';
  /**
   * API key.
   */
  public const CREDENTIAL_TYPE_API_KEY = 'API_KEY';
  /**
   * OAuth 2.0 Authorization Code Grant Type.
   */
  public const CREDENTIAL_TYPE_OAUTH2_AUTHORIZATION_CODE = 'OAUTH2_AUTHORIZATION_CODE';
  /**
   * OAuth 2.0 Implicit Grant Type.
   */
  public const CREDENTIAL_TYPE_OAUTH2_IMPLICIT = 'OAUTH2_IMPLICIT';
  /**
   * OAuth 2.0 Client Credentials Grant Type.
   */
  public const CREDENTIAL_TYPE_OAUTH2_CLIENT_CREDENTIALS = 'OAUTH2_CLIENT_CREDENTIALS';
  /**
   * OAuth 2.0 Resource Owner Credentials Grant Type.
   */
  public const CREDENTIAL_TYPE_OAUTH2_RESOURCE_OWNER_CREDENTIALS = 'OAUTH2_RESOURCE_OWNER_CREDENTIALS';
  /**
   * JWT Token.
   */
  public const CREDENTIAL_TYPE_JWT = 'JWT';
  /**
   * Auth Token, e.g. bearer token.
   */
  public const CREDENTIAL_TYPE_AUTH_TOKEN = 'AUTH_TOKEN';
  /**
   * Service Account which can be used to generate token for authentication.
   */
  public const CREDENTIAL_TYPE_SERVICE_ACCOUNT = 'SERVICE_ACCOUNT';
  /**
   * Client Certificate only.
   */
  public const CREDENTIAL_TYPE_CLIENT_CERTIFICATE_ONLY = 'CLIENT_CERTIFICATE_ONLY';
  /**
   * Google OIDC ID Token
   */
  public const CREDENTIAL_TYPE_OIDC_TOKEN = 'OIDC_TOKEN';
  protected $authTokenType = GoogleCloudIntegrationsV1alphaAuthToken::class;
  protected $authTokenDataType = '';
  /**
   * Credential type associated with auth config.
   *
   * @var string
   */
  public $credentialType;
  protected $jwtType = GoogleCloudIntegrationsV1alphaJwt::class;
  protected $jwtDataType = '';
  protected $oauth2AuthorizationCodeType = GoogleCloudIntegrationsV1alphaOAuth2AuthorizationCode::class;
  protected $oauth2AuthorizationCodeDataType = '';
  protected $oauth2ClientCredentialsType = GoogleCloudIntegrationsV1alphaOAuth2ClientCredentials::class;
  protected $oauth2ClientCredentialsDataType = '';
  protected $oauth2ResourceOwnerCredentialsType = GoogleCloudIntegrationsV1alphaOAuth2ResourceOwnerCredentials::class;
  protected $oauth2ResourceOwnerCredentialsDataType = '';
  protected $oidcTokenType = GoogleCloudIntegrationsV1alphaOidcToken::class;
  protected $oidcTokenDataType = '';
  protected $serviceAccountCredentialsType = GoogleCloudIntegrationsV1alphaServiceAccountCredentials::class;
  protected $serviceAccountCredentialsDataType = '';
  protected $usernameAndPasswordType = GoogleCloudIntegrationsV1alphaUsernameAndPassword::class;
  protected $usernameAndPasswordDataType = '';

  /**
   * Auth token credential
   *
   * @param GoogleCloudIntegrationsV1alphaAuthToken $authToken
   */
  public function setAuthToken(GoogleCloudIntegrationsV1alphaAuthToken $authToken)
  {
    $this->authToken = $authToken;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaAuthToken
   */
  public function getAuthToken()
  {
    return $this->authToken;
  }
  /**
   * Credential type associated with auth config.
   *
   * Accepted values: CREDENTIAL_TYPE_UNSPECIFIED, USERNAME_AND_PASSWORD,
   * API_KEY, OAUTH2_AUTHORIZATION_CODE, OAUTH2_IMPLICIT,
   * OAUTH2_CLIENT_CREDENTIALS, OAUTH2_RESOURCE_OWNER_CREDENTIALS, JWT,
   * AUTH_TOKEN, SERVICE_ACCOUNT, CLIENT_CERTIFICATE_ONLY, OIDC_TOKEN
   *
   * @param self::CREDENTIAL_TYPE_* $credentialType
   */
  public function setCredentialType($credentialType)
  {
    $this->credentialType = $credentialType;
  }
  /**
   * @return self::CREDENTIAL_TYPE_*
   */
  public function getCredentialType()
  {
    return $this->credentialType;
  }
  /**
   * JWT credential
   *
   * @param GoogleCloudIntegrationsV1alphaJwt $jwt
   */
  public function setJwt(GoogleCloudIntegrationsV1alphaJwt $jwt)
  {
    $this->jwt = $jwt;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaJwt
   */
  public function getJwt()
  {
    return $this->jwt;
  }
  /**
   * The api_key and oauth2_implicit are not covered in v1 and will be picked up
   * once v1 is implemented. ApiKey api_key = 3; OAuth2 authorization code
   * credential
   *
   * @param GoogleCloudIntegrationsV1alphaOAuth2AuthorizationCode $oauth2AuthorizationCode
   */
  public function setOauth2AuthorizationCode(GoogleCloudIntegrationsV1alphaOAuth2AuthorizationCode $oauth2AuthorizationCode)
  {
    $this->oauth2AuthorizationCode = $oauth2AuthorizationCode;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaOAuth2AuthorizationCode
   */
  public function getOauth2AuthorizationCode()
  {
    return $this->oauth2AuthorizationCode;
  }
  /**
   * OAuth2Implicit oauth2_implicit = 5; OAuth2 client credentials
   *
   * @param GoogleCloudIntegrationsV1alphaOAuth2ClientCredentials $oauth2ClientCredentials
   */
  public function setOauth2ClientCredentials(GoogleCloudIntegrationsV1alphaOAuth2ClientCredentials $oauth2ClientCredentials)
  {
    $this->oauth2ClientCredentials = $oauth2ClientCredentials;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaOAuth2ClientCredentials
   */
  public function getOauth2ClientCredentials()
  {
    return $this->oauth2ClientCredentials;
  }
  /**
   * OAuth2 resource owner credentials
   *
   * @param GoogleCloudIntegrationsV1alphaOAuth2ResourceOwnerCredentials $oauth2ResourceOwnerCredentials
   */
  public function setOauth2ResourceOwnerCredentials(GoogleCloudIntegrationsV1alphaOAuth2ResourceOwnerCredentials $oauth2ResourceOwnerCredentials)
  {
    $this->oauth2ResourceOwnerCredentials = $oauth2ResourceOwnerCredentials;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaOAuth2ResourceOwnerCredentials
   */
  public function getOauth2ResourceOwnerCredentials()
  {
    return $this->oauth2ResourceOwnerCredentials;
  }
  /**
   * Google OIDC ID Token
   *
   * @param GoogleCloudIntegrationsV1alphaOidcToken $oidcToken
   */
  public function setOidcToken(GoogleCloudIntegrationsV1alphaOidcToken $oidcToken)
  {
    $this->oidcToken = $oidcToken;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaOidcToken
   */
  public function getOidcToken()
  {
    return $this->oidcToken;
  }
  /**
   * Service account credential
   *
   * @param GoogleCloudIntegrationsV1alphaServiceAccountCredentials $serviceAccountCredentials
   */
  public function setServiceAccountCredentials(GoogleCloudIntegrationsV1alphaServiceAccountCredentials $serviceAccountCredentials)
  {
    $this->serviceAccountCredentials = $serviceAccountCredentials;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaServiceAccountCredentials
   */
  public function getServiceAccountCredentials()
  {
    return $this->serviceAccountCredentials;
  }
  /**
   * Username and password credential
   *
   * @param GoogleCloudIntegrationsV1alphaUsernameAndPassword $usernameAndPassword
   */
  public function setUsernameAndPassword(GoogleCloudIntegrationsV1alphaUsernameAndPassword $usernameAndPassword)
  {
    $this->usernameAndPassword = $usernameAndPassword;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaUsernameAndPassword
   */
  public function getUsernameAndPassword()
  {
    return $this->usernameAndPassword;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudIntegrationsV1alphaCredential::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaCredential');
