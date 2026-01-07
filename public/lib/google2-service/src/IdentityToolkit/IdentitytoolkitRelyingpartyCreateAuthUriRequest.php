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

namespace Google\Service\IdentityToolkit;

class IdentitytoolkitRelyingpartyCreateAuthUriRequest extends \Google\Model
{
  /**
   * The app ID of the mobile app, base64(CERT_SHA1):PACKAGE_NAME for Android,
   * BUNDLE_ID for iOS.
   *
   * @var string
   */
  public $appId;
  /**
   * Explicitly specify the auth flow type. Currently only support "CODE_FLOW"
   * type. The field is only used for Google provider.
   *
   * @var string
   */
  public $authFlowType;
  /**
   * The relying party OAuth client ID.
   *
   * @var string
   */
  public $clientId;
  /**
   * The opaque value used by the client to maintain context info between the
   * authentication request and the IDP callback.
   *
   * @var string
   */
  public $context;
  /**
   * The URI to which the IDP redirects the user after the federated login flow.
   *
   * @var string
   */
  public $continueUri;
  /**
   * The query parameter that client can customize by themselves in auth url.
   * The following parameters are reserved for server so that they cannot be
   * customized by clients: client_id, response_type, scope, redirect_uri,
   * state, oauth_token.
   *
   * @var string[]
   */
  public $customParameter;
  /**
   * The hosted domain to restrict sign-in to accounts at that domain for Google
   * Apps hosted accounts.
   *
   * @var string
   */
  public $hostedDomain;
  /**
   * The email or federated ID of the user.
   *
   * @var string
   */
  public $identifier;
  /**
   * The developer's consumer key for OpenId OAuth Extension
   *
   * @var string
   */
  public $oauthConsumerKey;
  /**
   * Additional oauth scopes, beyond the basid user profile, that the user would
   * be prompted to grant
   *
   * @var string
   */
  public $oauthScope;
  /**
   * Optional realm for OpenID protocol. The sub string "scheme://domain:port"
   * of the param "continueUri" is used if this is not set.
   *
   * @var string
   */
  public $openidRealm;
  /**
   * The native app package for OTA installation.
   *
   * @var string
   */
  public $otaApp;
  /**
   * The IdP ID. For white listed IdPs it's a short domain name e.g. google.com,
   * aol.com, live.net and yahoo.com. For other OpenID IdPs it's the OP
   * identifier.
   *
   * @var string
   */
  public $providerId;
  /**
   * The session_id passed by client.
   *
   * @var string
   */
  public $sessionId;
  /**
   * For multi-tenant use cases, in order to construct sign-in URL with the
   * correct IDP parameters, Firebear needs to know which Tenant to retrieve IDP
   * configs from.
   *
   * @var string
   */
  public $tenantId;
  /**
   * Tenant project number to be used for idp discovery.
   *
   * @var string
   */
  public $tenantProjectNumber;

  /**
   * The app ID of the mobile app, base64(CERT_SHA1):PACKAGE_NAME for Android,
   * BUNDLE_ID for iOS.
   *
   * @param string $appId
   */
  public function setAppId($appId)
  {
    $this->appId = $appId;
  }
  /**
   * @return string
   */
  public function getAppId()
  {
    return $this->appId;
  }
  /**
   * Explicitly specify the auth flow type. Currently only support "CODE_FLOW"
   * type. The field is only used for Google provider.
   *
   * @param string $authFlowType
   */
  public function setAuthFlowType($authFlowType)
  {
    $this->authFlowType = $authFlowType;
  }
  /**
   * @return string
   */
  public function getAuthFlowType()
  {
    return $this->authFlowType;
  }
  /**
   * The relying party OAuth client ID.
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
   * The opaque value used by the client to maintain context info between the
   * authentication request and the IDP callback.
   *
   * @param string $context
   */
  public function setContext($context)
  {
    $this->context = $context;
  }
  /**
   * @return string
   */
  public function getContext()
  {
    return $this->context;
  }
  /**
   * The URI to which the IDP redirects the user after the federated login flow.
   *
   * @param string $continueUri
   */
  public function setContinueUri($continueUri)
  {
    $this->continueUri = $continueUri;
  }
  /**
   * @return string
   */
  public function getContinueUri()
  {
    return $this->continueUri;
  }
  /**
   * The query parameter that client can customize by themselves in auth url.
   * The following parameters are reserved for server so that they cannot be
   * customized by clients: client_id, response_type, scope, redirect_uri,
   * state, oauth_token.
   *
   * @param string[] $customParameter
   */
  public function setCustomParameter($customParameter)
  {
    $this->customParameter = $customParameter;
  }
  /**
   * @return string[]
   */
  public function getCustomParameter()
  {
    return $this->customParameter;
  }
  /**
   * The hosted domain to restrict sign-in to accounts at that domain for Google
   * Apps hosted accounts.
   *
   * @param string $hostedDomain
   */
  public function setHostedDomain($hostedDomain)
  {
    $this->hostedDomain = $hostedDomain;
  }
  /**
   * @return string
   */
  public function getHostedDomain()
  {
    return $this->hostedDomain;
  }
  /**
   * The email or federated ID of the user.
   *
   * @param string $identifier
   */
  public function setIdentifier($identifier)
  {
    $this->identifier = $identifier;
  }
  /**
   * @return string
   */
  public function getIdentifier()
  {
    return $this->identifier;
  }
  /**
   * The developer's consumer key for OpenId OAuth Extension
   *
   * @param string $oauthConsumerKey
   */
  public function setOauthConsumerKey($oauthConsumerKey)
  {
    $this->oauthConsumerKey = $oauthConsumerKey;
  }
  /**
   * @return string
   */
  public function getOauthConsumerKey()
  {
    return $this->oauthConsumerKey;
  }
  /**
   * Additional oauth scopes, beyond the basid user profile, that the user would
   * be prompted to grant
   *
   * @param string $oauthScope
   */
  public function setOauthScope($oauthScope)
  {
    $this->oauthScope = $oauthScope;
  }
  /**
   * @return string
   */
  public function getOauthScope()
  {
    return $this->oauthScope;
  }
  /**
   * Optional realm for OpenID protocol. The sub string "scheme://domain:port"
   * of the param "continueUri" is used if this is not set.
   *
   * @param string $openidRealm
   */
  public function setOpenidRealm($openidRealm)
  {
    $this->openidRealm = $openidRealm;
  }
  /**
   * @return string
   */
  public function getOpenidRealm()
  {
    return $this->openidRealm;
  }
  /**
   * The native app package for OTA installation.
   *
   * @param string $otaApp
   */
  public function setOtaApp($otaApp)
  {
    $this->otaApp = $otaApp;
  }
  /**
   * @return string
   */
  public function getOtaApp()
  {
    return $this->otaApp;
  }
  /**
   * The IdP ID. For white listed IdPs it's a short domain name e.g. google.com,
   * aol.com, live.net and yahoo.com. For other OpenID IdPs it's the OP
   * identifier.
   *
   * @param string $providerId
   */
  public function setProviderId($providerId)
  {
    $this->providerId = $providerId;
  }
  /**
   * @return string
   */
  public function getProviderId()
  {
    return $this->providerId;
  }
  /**
   * The session_id passed by client.
   *
   * @param string $sessionId
   */
  public function setSessionId($sessionId)
  {
    $this->sessionId = $sessionId;
  }
  /**
   * @return string
   */
  public function getSessionId()
  {
    return $this->sessionId;
  }
  /**
   * For multi-tenant use cases, in order to construct sign-in URL with the
   * correct IDP parameters, Firebear needs to know which Tenant to retrieve IDP
   * configs from.
   *
   * @param string $tenantId
   */
  public function setTenantId($tenantId)
  {
    $this->tenantId = $tenantId;
  }
  /**
   * @return string
   */
  public function getTenantId()
  {
    return $this->tenantId;
  }
  /**
   * Tenant project number to be used for idp discovery.
   *
   * @param string $tenantProjectNumber
   */
  public function setTenantProjectNumber($tenantProjectNumber)
  {
    $this->tenantProjectNumber = $tenantProjectNumber;
  }
  /**
   * @return string
   */
  public function getTenantProjectNumber()
  {
    return $this->tenantProjectNumber;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IdentitytoolkitRelyingpartyCreateAuthUriRequest::class, 'Google_Service_IdentityToolkit_IdentitytoolkitRelyingpartyCreateAuthUriRequest');
