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

class Source extends \Google\Collection
{
  protected $collection_key = 'principals';
  /**
   * Optional. List of CIDR ranges to match based on source IP address. At least
   * one IP block should match. Single IP (e.g., "1.2.3.4") and CIDR (e.g.,
   * "1.2.3.0/24") are supported. Authorization based on source IP alone should
   * be avoided. The IP addresses of any load balancers or proxies should be
   * considered untrusted.
   *
   * @var string[]
   */
  public $ipBlocks;
  /**
   * Optional. List of peer identities to match for authorization. At least one
   * principal should match. Each peer can be an exact match, or a prefix match
   * (example, "namespace") or a suffix match (example, "service-account") or a
   * presence match "*". Authorization based on the principal name without
   * certificate validation (configured by ServerTlsPolicy resource) is
   * considered insecure.
   *
   * @var string[]
   */
  public $principals;

  /**
   * Optional. List of CIDR ranges to match based on source IP address. At least
   * one IP block should match. Single IP (e.g., "1.2.3.4") and CIDR (e.g.,
   * "1.2.3.0/24") are supported. Authorization based on source IP alone should
   * be avoided. The IP addresses of any load balancers or proxies should be
   * considered untrusted.
   *
   * @param string[] $ipBlocks
   */
  public function setIpBlocks($ipBlocks)
  {
    $this->ipBlocks = $ipBlocks;
  }
  /**
   * @return string[]
   */
  public function getIpBlocks()
  {
    return $this->ipBlocks;
  }
  /**
   * Optional. List of peer identities to match for authorization. At least one
   * principal should match. Each peer can be an exact match, or a prefix match
   * (example, "namespace") or a suffix match (example, "service-account") or a
   * presence match "*". Authorization based on the principal name without
   * certificate validation (configured by ServerTlsPolicy resource) is
   * considered insecure.
   *
   * @param string[] $principals
   */
  public function setPrincipals($principals)
  {
    $this->principals = $principals;
  }
  /**
   * @return string[]
   */
  public function getPrincipals()
  {
    return $this->principals;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Source::class, 'Google_Service_NetworkSecurity_Source');
