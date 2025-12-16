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

namespace Google\Service\ServiceControl;

class Auth extends \Google\Collection
{
  protected $collection_key = 'audiences';
  /**
   * A list of access level resource names that allow resources to be accessed
   * by authenticated requester. It is part of Secure GCP processing for the
   * incoming request. An access level string has the format:
   * "//{api_service_name}/accessPolicies/{policy_id}/accessLevels/{short_name}"
   * Example: "//accesscontextmanager.googleapis.com/accessPolicies/MY_POLICY_ID
   * /accessLevels/MY_LEVEL"
   *
   * @var string[]
   */
  public $accessLevels;
  /**
   * The intended audience(s) for this authentication information. Reflects the
   * audience (`aud`) claim within a JWT. The audience value(s) depends on the
   * `issuer`, but typically include one or more of the following pieces of
   * information: * The services intended to receive the credential. For
   * example, ["https://pubsub.googleapis.com/",
   * "https://storage.googleapis.com/"]. * A set of service-based scopes. For
   * example, ["https://www.googleapis.com/auth/cloud-platform"]. * The client
   * id of an app, such as the Firebase project id for JWTs from Firebase Auth.
   * Consult the documentation for the credential issuer to determine the
   * information provided.
   *
   * @var string[]
   */
  public $audiences;
  /**
   * Structured claims presented with the credential. JWTs include `{key:
   * value}` pairs for standard and private claims. The following is a subset of
   * the standard required and optional claims that would typically be presented
   * for a Google-based JWT: {'iss': 'accounts.google.com', 'sub':
   * '113289723416554971153', 'aud': ['123456789012', 'pubsub.googleapis.com'],
   * 'azp': '123456789012.apps.googleusercontent.com', 'email':
   * 'jsmith@example.com', 'iat': 1353601026, 'exp': 1353604926} SAML assertions
   * are similarly specified, but with an identity provider dependent structure.
   *
   * @var array[]
   */
  public $claims;
  protected $oauthType = Oauth::class;
  protected $oauthDataType = '';
  /**
   * The authorized presenter of the credential. Reflects the optional
   * Authorized Presenter (`azp`) claim within a JWT or the OAuth client id. For
   * example, a Google Cloud Platform client id looks as follows:
   * "123456789012.apps.googleusercontent.com".
   *
   * @var string
   */
  public $presenter;
  /**
   * The authenticated principal. Reflects the issuer (`iss`) and subject
   * (`sub`) claims within a JWT. The issuer and subject should be `/`
   * delimited, with `/` percent-encoded within the subject fragment. For Google
   * accounts, the principal format is: "https://accounts.google.com/{id}"
   *
   * @var string
   */
  public $principal;

  /**
   * A list of access level resource names that allow resources to be accessed
   * by authenticated requester. It is part of Secure GCP processing for the
   * incoming request. An access level string has the format:
   * "//{api_service_name}/accessPolicies/{policy_id}/accessLevels/{short_name}"
   * Example: "//accesscontextmanager.googleapis.com/accessPolicies/MY_POLICY_ID
   * /accessLevels/MY_LEVEL"
   *
   * @param string[] $accessLevels
   */
  public function setAccessLevels($accessLevels)
  {
    $this->accessLevels = $accessLevels;
  }
  /**
   * @return string[]
   */
  public function getAccessLevels()
  {
    return $this->accessLevels;
  }
  /**
   * The intended audience(s) for this authentication information. Reflects the
   * audience (`aud`) claim within a JWT. The audience value(s) depends on the
   * `issuer`, but typically include one or more of the following pieces of
   * information: * The services intended to receive the credential. For
   * example, ["https://pubsub.googleapis.com/",
   * "https://storage.googleapis.com/"]. * A set of service-based scopes. For
   * example, ["https://www.googleapis.com/auth/cloud-platform"]. * The client
   * id of an app, such as the Firebase project id for JWTs from Firebase Auth.
   * Consult the documentation for the credential issuer to determine the
   * information provided.
   *
   * @param string[] $audiences
   */
  public function setAudiences($audiences)
  {
    $this->audiences = $audiences;
  }
  /**
   * @return string[]
   */
  public function getAudiences()
  {
    return $this->audiences;
  }
  /**
   * Structured claims presented with the credential. JWTs include `{key:
   * value}` pairs for standard and private claims. The following is a subset of
   * the standard required and optional claims that would typically be presented
   * for a Google-based JWT: {'iss': 'accounts.google.com', 'sub':
   * '113289723416554971153', 'aud': ['123456789012', 'pubsub.googleapis.com'],
   * 'azp': '123456789012.apps.googleusercontent.com', 'email':
   * 'jsmith@example.com', 'iat': 1353601026, 'exp': 1353604926} SAML assertions
   * are similarly specified, but with an identity provider dependent structure.
   *
   * @param array[] $claims
   */
  public function setClaims($claims)
  {
    $this->claims = $claims;
  }
  /**
   * @return array[]
   */
  public function getClaims()
  {
    return $this->claims;
  }
  /**
   * Attributes of the OAuth token associated with the request.
   *
   * @param Oauth $oauth
   */
  public function setOauth(Oauth $oauth)
  {
    $this->oauth = $oauth;
  }
  /**
   * @return Oauth
   */
  public function getOauth()
  {
    return $this->oauth;
  }
  /**
   * The authorized presenter of the credential. Reflects the optional
   * Authorized Presenter (`azp`) claim within a JWT or the OAuth client id. For
   * example, a Google Cloud Platform client id looks as follows:
   * "123456789012.apps.googleusercontent.com".
   *
   * @param string $presenter
   */
  public function setPresenter($presenter)
  {
    $this->presenter = $presenter;
  }
  /**
   * @return string
   */
  public function getPresenter()
  {
    return $this->presenter;
  }
  /**
   * The authenticated principal. Reflects the issuer (`iss`) and subject
   * (`sub`) claims within a JWT. The issuer and subject should be `/`
   * delimited, with `/` percent-encoded within the subject fragment. For Google
   * accounts, the principal format is: "https://accounts.google.com/{id}"
   *
   * @param string $principal
   */
  public function setPrincipal($principal)
  {
    $this->principal = $principal;
  }
  /**
   * @return string
   */
  public function getPrincipal()
  {
    return $this->principal;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Auth::class, 'Google_Service_ServiceControl_Auth');
