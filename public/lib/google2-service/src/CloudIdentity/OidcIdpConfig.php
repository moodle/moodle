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

class OidcIdpConfig extends \Google\Model
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
   * Required. The Issuer identifier for the IdP. Must be a URL. The discovery
   * URL will be derived from this as described in Section 4 of [the OIDC
   * specification](https://openid.net/specs/openid-connect-discovery-1_0.html).
   *
   * @var string
   */
  public $issuerUri;

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
   * Required. The Issuer identifier for the IdP. Must be a URL. The discovery
   * URL will be derived from this as described in Section 4 of [the OIDC
   * specification](https://openid.net/specs/openid-connect-discovery-1_0.html).
   *
   * @param string $issuerUri
   */
  public function setIssuerUri($issuerUri)
  {
    $this->issuerUri = $issuerUri;
  }
  /**
   * @return string
   */
  public function getIssuerUri()
  {
    return $this->issuerUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OidcIdpConfig::class, 'Google_Service_CloudIdentity_OidcIdpConfig');
