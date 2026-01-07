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

namespace Google\Service\CloudIdentity;

class SamlIdpConfig extends \Google\Model
{
  /**
   * The **Change Password URL** of the identity provider. Users will be sent to
   * this URL when changing their passwords at `myaccount.google.com`. This
   * takes precedence over the change password URL configured at customer-level.
   * Must use `HTTPS`.
   *
   * @var string
   */
  public $changePasswordUri;
  /**
   * Required. The SAML **Entity ID** of the identity provider.
   *
   * @var string
   */
  public $entityId;
  /**
   * The **Logout Redirect URL** (sign-out page URL) of the identity provider.
   * When a user clicks the sign-out link on a Google page, they will be
   * redirected to this URL. This is a pure redirect with no attached SAML
   * `LogoutRequest` i.e. SAML single logout is not supported. Must use `HTTPS`.
   *
   * @var string
   */
  public $logoutRedirectUri;
  /**
   * Required. The `SingleSignOnService` endpoint location (sign-in page URL) of
   * the identity provider. This is the URL where the `AuthnRequest` will be
   * sent. Must use `HTTPS`. Assumed to accept the `HTTP-Redirect` binding.
   *
   * @var string
   */
  public $singleSignOnServiceUri;

  /**
   * The **Change Password URL** of the identity provider. Users will be sent to
   * this URL when changing their passwords at `myaccount.google.com`. This
   * takes precedence over the change password URL configured at customer-level.
   * Must use `HTTPS`.
   *
   * @param string $changePasswordUri
   */
  public function setChangePasswordUri($changePasswordUri)
  {
    $this->changePasswordUri = $changePasswordUri;
  }
  /**
   * @return string
   */
  public function getChangePasswordUri()
  {
    return $this->changePasswordUri;
  }
  /**
   * Required. The SAML **Entity ID** of the identity provider.
   *
   * @param string $entityId
   */
  public function setEntityId($entityId)
  {
    $this->entityId = $entityId;
  }
  /**
   * @return string
   */
  public function getEntityId()
  {
    return $this->entityId;
  }
  /**
   * The **Logout Redirect URL** (sign-out page URL) of the identity provider.
   * When a user clicks the sign-out link on a Google page, they will be
   * redirected to this URL. This is a pure redirect with no attached SAML
   * `LogoutRequest` i.e. SAML single logout is not supported. Must use `HTTPS`.
   *
   * @param string $logoutRedirectUri
   */
  public function setLogoutRedirectUri($logoutRedirectUri)
  {
    $this->logoutRedirectUri = $logoutRedirectUri;
  }
  /**
   * @return string
   */
  public function getLogoutRedirectUri()
  {
    return $this->logoutRedirectUri;
  }
  /**
   * Required. The `SingleSignOnService` endpoint location (sign-in page URL) of
   * the identity provider. This is the URL where the `AuthnRequest` will be
   * sent. Must use `HTTPS`. Assumed to accept the `HTTP-Redirect` binding.
   *
   * @param string $singleSignOnServiceUri
   */
  public function setSingleSignOnServiceUri($singleSignOnServiceUri)
  {
    $this->singleSignOnServiceUri = $singleSignOnServiceUri;
  }
  /**
   * @return string
   */
  public function getSingleSignOnServiceUri()
  {
    return $this->singleSignOnServiceUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SamlIdpConfig::class, 'Google_Service_CloudIdentity_SamlIdpConfig');
