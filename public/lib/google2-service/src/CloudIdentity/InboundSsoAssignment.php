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

class InboundSsoAssignment extends \Google\Model
{
  /**
   * Not allowed.
   */
  public const SSO_MODE_SSO_MODE_UNSPECIFIED = 'SSO_MODE_UNSPECIFIED';
  /**
   * Disable SSO for the targeted users.
   */
  public const SSO_MODE_SSO_OFF = 'SSO_OFF';
  /**
   * Use an external SAML Identity Provider for SSO for the targeted users.
   */
  public const SSO_MODE_SAML_SSO = 'SAML_SSO';
  /**
   * Use an external OIDC Identity Provider for SSO for the targeted users.
   */
  public const SSO_MODE_OIDC_SSO = 'OIDC_SSO';
  /**
   * Use the domain-wide SAML Identity Provider for the targeted users if one is
   * configured; otherwise, this is equivalent to `SSO_OFF`. Note that this will
   * also be equivalent to `SSO_OFF` if/when support for domain-wide SAML is
   * removed. Google may disallow this mode at that point and existing
   * assignments with this mode may be automatically changed to `SSO_OFF`.
   */
  public const SSO_MODE_DOMAIN_WIDE_SAML_IF_ENABLED = 'DOMAIN_WIDE_SAML_IF_ENABLED';
  /**
   * Immutable. The customer. For example: `customers/C0123abc`.
   *
   * @var string
   */
  public $customer;
  /**
   * Output only. [Resource
   * name](https://cloud.google.com/apis/design/resource_names) of the Inbound
   * SSO Assignment.
   *
   * @var string
   */
  public $name;
  protected $oidcSsoInfoType = OidcSsoInfo::class;
  protected $oidcSsoInfoDataType = '';
  /**
   * Must be zero (which is the default value so it can be omitted) for
   * assignments with `target_org_unit` set and must be greater-than-or-equal-to
   * one for assignments with `target_group` set.
   *
   * @var int
   */
  public $rank;
  protected $samlSsoInfoType = SamlSsoInfo::class;
  protected $samlSsoInfoDataType = '';
  protected $signInBehaviorType = SignInBehavior::class;
  protected $signInBehaviorDataType = '';
  /**
   * Inbound SSO behavior.
   *
   * @var string
   */
  public $ssoMode;
  /**
   * Immutable. Must be of the form `groups/{group}`.
   *
   * @var string
   */
  public $targetGroup;
  /**
   * Immutable. Must be of the form `orgUnits/{org_unit}`.
   *
   * @var string
   */
  public $targetOrgUnit;

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
   * Output only. [Resource
   * name](https://cloud.google.com/apis/design/resource_names) of the Inbound
   * SSO Assignment.
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
   * OpenID Connect SSO details. Must be set if and only if `sso_mode` is set to
   * `OIDC_SSO`.
   *
   * @param OidcSsoInfo $oidcSsoInfo
   */
  public function setOidcSsoInfo(OidcSsoInfo $oidcSsoInfo)
  {
    $this->oidcSsoInfo = $oidcSsoInfo;
  }
  /**
   * @return OidcSsoInfo
   */
  public function getOidcSsoInfo()
  {
    return $this->oidcSsoInfo;
  }
  /**
   * Must be zero (which is the default value so it can be omitted) for
   * assignments with `target_org_unit` set and must be greater-than-or-equal-to
   * one for assignments with `target_group` set.
   *
   * @param int $rank
   */
  public function setRank($rank)
  {
    $this->rank = $rank;
  }
  /**
   * @return int
   */
  public function getRank()
  {
    return $this->rank;
  }
  /**
   * SAML SSO details. Must be set if and only if `sso_mode` is set to
   * `SAML_SSO`.
   *
   * @param SamlSsoInfo $samlSsoInfo
   */
  public function setSamlSsoInfo(SamlSsoInfo $samlSsoInfo)
  {
    $this->samlSsoInfo = $samlSsoInfo;
  }
  /**
   * @return SamlSsoInfo
   */
  public function getSamlSsoInfo()
  {
    return $this->samlSsoInfo;
  }
  /**
   * Assertions about users assigned to an IdP will always be accepted from that
   * IdP. This controls whether/when Google should redirect a user to the IdP.
   * Unset (defaults) is the recommended configuration.
   *
   * @param SignInBehavior $signInBehavior
   */
  public function setSignInBehavior(SignInBehavior $signInBehavior)
  {
    $this->signInBehavior = $signInBehavior;
  }
  /**
   * @return SignInBehavior
   */
  public function getSignInBehavior()
  {
    return $this->signInBehavior;
  }
  /**
   * Inbound SSO behavior.
   *
   * Accepted values: SSO_MODE_UNSPECIFIED, SSO_OFF, SAML_SSO, OIDC_SSO,
   * DOMAIN_WIDE_SAML_IF_ENABLED
   *
   * @param self::SSO_MODE_* $ssoMode
   */
  public function setSsoMode($ssoMode)
  {
    $this->ssoMode = $ssoMode;
  }
  /**
   * @return self::SSO_MODE_*
   */
  public function getSsoMode()
  {
    return $this->ssoMode;
  }
  /**
   * Immutable. Must be of the form `groups/{group}`.
   *
   * @param string $targetGroup
   */
  public function setTargetGroup($targetGroup)
  {
    $this->targetGroup = $targetGroup;
  }
  /**
   * @return string
   */
  public function getTargetGroup()
  {
    return $this->targetGroup;
  }
  /**
   * Immutable. Must be of the form `orgUnits/{org_unit}`.
   *
   * @param string $targetOrgUnit
   */
  public function setTargetOrgUnit($targetOrgUnit)
  {
    $this->targetOrgUnit = $targetOrgUnit;
  }
  /**
   * @return string
   */
  public function getTargetOrgUnit()
  {
    return $this->targetOrgUnit;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InboundSsoAssignment::class, 'Google_Service_CloudIdentity_InboundSsoAssignment');
