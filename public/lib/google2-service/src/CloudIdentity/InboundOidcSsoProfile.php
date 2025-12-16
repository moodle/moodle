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

class InboundOidcSsoProfile extends \Google\Model
{
  /**
   * Immutable. The customer. For example: `customers/C0123abc`.
   *
   * @var string
   */
  public $customer;
  /**
   * Human-readable name of the OIDC SSO profile.
   *
   * @var string
   */
  public $displayName;
  protected $idpConfigType = OidcIdpConfig::class;
  protected $idpConfigDataType = '';
  /**
   * Output only. [Resource
   * name](https://cloud.google.com/apis/design/resource_names) of the OIDC SSO
   * profile.
   *
   * @var string
   */
  public $name;
  protected $rpConfigType = OidcRpConfig::class;
  protected $rpConfigDataType = '';

  /**
   * Immutable. The customer. For example: `customers/C0123abc`.
   *
   * @param string $customer
   */
  public function setCustomer($customer)
  {
    $this->customer = $customer;
  }
  /**
   * @return string
   */
  public function getCustomer()
  {
    return $this->customer;
  }
  /**
   * Human-readable name of the OIDC SSO profile.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * OIDC identity provider configuration.
   *
   * @param OidcIdpConfig $idpConfig
   */
  public function setIdpConfig(OidcIdpConfig $idpConfig)
  {
    $this->idpConfig = $idpConfig;
  }
  /**
   * @return OidcIdpConfig
   */
  public function getIdpConfig()
  {
    return $this->idpConfig;
  }
  /**
   * Output only. [Resource
   * name](https://cloud.google.com/apis/design/resource_names) of the OIDC SSO
   * profile.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * OIDC relying party (RP) configuration for this OIDC SSO profile. These are
   * the RP details provided by Google that should be configured on the
   * corresponding identity provider.
   *
   * @param OidcRpConfig $rpConfig
   */
  public function setRpConfig(OidcRpConfig $rpConfig)
  {
    $this->rpConfig = $rpConfig;
  }
  /**
   * @return OidcRpConfig
   */
  public function getRpConfig()
  {
    return $this->rpConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InboundOidcSsoProfile::class, 'Google_Service_CloudIdentity_InboundOidcSsoProfile');
