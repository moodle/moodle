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

namespace Google\Service\NetworkSecurity;

class AuthzPolicyAuthzRulePrincipal extends \Google\Model
{
  /**
   * Unspecified principal selector. It will be treated as CLIENT_CERT_URI_SAN
   * by default.
   */
  public const PRINCIPAL_SELECTOR_PRINCIPAL_SELECTOR_UNSPECIFIED = 'PRINCIPAL_SELECTOR_UNSPECIFIED';
  /**
   * The principal rule is matched against a list of URI SANs in the validated
   * client's certificate. A match happens when there is any exact URI SAN value
   * match. This is the default principal selector.
   */
  public const PRINCIPAL_SELECTOR_CLIENT_CERT_URI_SAN = 'CLIENT_CERT_URI_SAN';
  /**
   * The principal rule is matched against a list of DNS Name SANs in the
   * validated client's certificate. A match happens when there is any exact DNS
   * Name SAN value match. This is only applicable for Application Load
   * Balancers except for classic Global External Application load balancer.
   * CLIENT_CERT_DNS_NAME_SAN is not supported for INTERNAL_SELF_MANAGED load
   * balancing scheme.
   */
  public const PRINCIPAL_SELECTOR_CLIENT_CERT_DNS_NAME_SAN = 'CLIENT_CERT_DNS_NAME_SAN';
  /**
   * The principal rule is matched against the common name in the client's
   * certificate. Authorization against multiple common names in the client
   * certificate is not supported. Requests with multiple common names in the
   * client certificate will be rejected if CLIENT_CERT_COMMON_NAME is set as
   * the principal selector. A match happens when there is an exact common name
   * value match. This is only applicable for Application Load Balancers except
   * for global external Application Load Balancer and classic Application Load
   * Balancer. CLIENT_CERT_COMMON_NAME is not supported for
   * INTERNAL_SELF_MANAGED load balancing scheme.
   */
  public const PRINCIPAL_SELECTOR_CLIENT_CERT_COMMON_NAME = 'CLIENT_CERT_COMMON_NAME';
  protected $principalType = AuthzPolicyAuthzRuleStringMatch::class;
  protected $principalDataType = '';
  /**
   * Optional. An enum to decide what principal value the principal rule will
   * match against. If not specified, the PrincipalSelector is
   * CLIENT_CERT_URI_SAN.
   *
   * @var string
   */
  public $principalSelector;

  /**
   * Required. A non-empty string whose value is matched against the principal
   * value based on the principal_selector. Only exact match can be applied for
   * CLIENT_CERT_URI_SAN, CLIENT_CERT_DNS_NAME_SAN, CLIENT_CERT_COMMON_NAME
   * selectors.
   *
   * @param AuthzPolicyAuthzRuleStringMatch $principal
   */
  public function setPrincipal(AuthzPolicyAuthzRuleStringMatch $principal)
  {
    $this->principal = $principal;
  }
  /**
   * @return AuthzPolicyAuthzRuleStringMatch
   */
  public function getPrincipal()
  {
    return $this->principal;
  }
  /**
   * Optional. An enum to decide what principal value the principal rule will
   * match against. If not specified, the PrincipalSelector is
   * CLIENT_CERT_URI_SAN.
   *
   * Accepted values: PRINCIPAL_SELECTOR_UNSPECIFIED, CLIENT_CERT_URI_SAN,
   * CLIENT_CERT_DNS_NAME_SAN, CLIENT_CERT_COMMON_NAME
   *
   * @param self::PRINCIPAL_SELECTOR_* $principalSelector
   */
  public function setPrincipalSelector($principalSelector)
  {
    $this->principalSelector = $principalSelector;
  }
  /**
   * @return self::PRINCIPAL_SELECTOR_*
   */
  public function getPrincipalSelector()
  {
    return $this->principalSelector;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AuthzPolicyAuthzRulePrincipal::class, 'Google_Service_NetworkSecurity_AuthzPolicyAuthzRulePrincipal');
