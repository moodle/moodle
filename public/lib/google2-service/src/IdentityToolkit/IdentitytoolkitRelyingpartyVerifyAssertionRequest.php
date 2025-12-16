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

class IdentitytoolkitRelyingpartyVerifyAssertionRequest extends \Google\Model
{
  /**
   * When it's true, automatically creates a new account if the user doesn't
   * exist. When it's false, allows existing user to sign in normally and throws
   * exception if the user doesn't exist.
   *
   * @var bool
   */
  public $autoCreate;
  /**
   * GCP project number of the requesting delegated app. Currently only intended
   * for Firebase V1 migration.
   *
   * @var string
   */
  public $delegatedProjectNumber;
  /**
   * The GITKit token of the authenticated user.
   *
   * @var string
   */
  public $idToken;
  /**
   * Instance id token of the app.
   *
   * @var string
   */
  public $instanceId;
  /**
   * The GITKit token for the non-trusted IDP pending to be confirmed by the
   * user.
   *
   * @var string
   */
  public $pendingIdToken;
  /**
   * The post body if the request is a HTTP POST.
   *
   * @var string
   */
  public $postBody;
  /**
   * The URI to which the IDP redirects the user back. It may contain federated
   * login result params added by the IDP.
   *
   * @var string
   */
  public $requestUri;
  /**
   * Whether return 200 and IDP credential rather than throw exception when
   * federated id is already linked.
   *
   * @var bool
   */
  public $returnIdpCredential;
  /**
   * Whether to return refresh tokens.
   *
   * @var bool
   */
  public $returnRefreshToken;
  /**
   * Whether return sts id token and refresh token instead of gitkit token.
   *
   * @var bool
   */
  public $returnSecureToken;
  /**
   * Session ID, which should match the one in previous createAuthUri request.
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
   * When it's true, automatically creates a new account if the user doesn't
   * exist. When it's false, allows existing user to sign in normally and throws
   * exception if the user doesn't exist.
   *
   * @param bool $autoCreate
   */
  public function setAutoCreate($autoCreate)
  {
    $this->autoCreate = $autoCreate;
  }
  /**
   * @return bool
   */
  public function getAutoCreate()
  {
    return $this->autoCreate;
  }
  /**
   * GCP project number of the requesting delegated app. Currently only intended
   * for Firebase V1 migration.
   *
   * @param string $delegatedProjectNumber
   */
  public function setDelegatedProjectNumber($delegatedProjectNumber)
  {
    $this->delegatedProjectNumber = $delegatedProjectNumber;
  }
  /**
   * @return string
   */
  public function getDelegatedProjectNumber()
  {
    return $this->delegatedProjectNumber;
  }
  /**
   * The GITKit token of the authenticated user.
   *
   * @param string $idToken
   */
  public function setIdToken($idToken)
  {
    $this->idToken = $idToken;
  }
  /**
   * @return string
   */
  public function getIdToken()
  {
    return $this->idToken;
  }
  /**
   * Instance id token of the app.
   *
   * @param string $instanceId
   */
  public function setInstanceId($instanceId)
  {
    $this->instanceId = $instanceId;
  }
  /**
   * @return string
   */
  public function getInstanceId()
  {
    return $this->instanceId;
  }
  /**
   * The GITKit token for the non-trusted IDP pending to be confirmed by the
   * user.
   *
   * @param string $pendingIdToken
   */
  public function setPendingIdToken($pendingIdToken)
  {
    $this->pendingIdToken = $pendingIdToken;
  }
  /**
   * @return string
   */
  public function getPendingIdToken()
  {
    return $this->pendingIdToken;
  }
  /**
   * The post body if the request is a HTTP POST.
   *
   * @param string $postBody
   */
  public function setPostBody($postBody)
  {
    $this->postBody = $postBody;
  }
  /**
   * @return string
   */
  public function getPostBody()
  {
    return $this->postBody;
  }
  /**
   * The URI to which the IDP redirects the user back. It may contain federated
   * login result params added by the IDP.
   *
   * @param string $requestUri
   */
  public function setRequestUri($requestUri)
  {
    $this->requestUri = $requestUri;
  }
  /**
   * @return string
   */
  public function getRequestUri()
  {
    return $this->requestUri;
  }
  /**
   * Whether return 200 and IDP credential rather than throw exception when
   * federated id is already linked.
   *
   * @param bool $returnIdpCredential
   */
  public function setReturnIdpCredential($returnIdpCredential)
  {
    $this->returnIdpCredential = $returnIdpCredential;
  }
  /**
   * @return bool
   */
  public function getReturnIdpCredential()
  {
    return $this->returnIdpCredential;
  }
  /**
   * Whether to return refresh tokens.
   *
   * @param bool $returnRefreshToken
   */
  public function setReturnRefreshToken($returnRefreshToken)
  {
    $this->returnRefreshToken = $returnRefreshToken;
  }
  /**
   * @return bool
   */
  public function getReturnRefreshToken()
  {
    return $this->returnRefreshToken;
  }
  /**
   * Whether return sts id token and refresh token instead of gitkit token.
   *
   * @param bool $returnSecureToken
   */
  public function setReturnSecureToken($returnSecureToken)
  {
    $this->returnSecureToken = $returnSecureToken;
  }
  /**
   * @return bool
   */
  public function getReturnSecureToken()
  {
    return $this->returnSecureToken;
  }
  /**
   * Session ID, which should match the one in previous createAuthUri request.
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
class_alias(IdentitytoolkitRelyingpartyVerifyAssertionRequest::class, 'Google_Service_IdentityToolkit_IdentitytoolkitRelyingpartyVerifyAssertionRequest');
