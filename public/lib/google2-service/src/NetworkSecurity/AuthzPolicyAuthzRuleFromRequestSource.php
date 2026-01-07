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

class AuthzPolicyAuthzRuleFromRequestSource extends \Google\Collection
{
  protected $collection_key = 'resources';
  protected $ipBlocksType = AuthzPolicyAuthzRuleIpBlock::class;
  protected $ipBlocksDataType = 'array';
  protected $principalsType = AuthzPolicyAuthzRulePrincipal::class;
  protected $principalsDataType = 'array';
  protected $resourcesType = AuthzPolicyAuthzRuleRequestResource::class;
  protected $resourcesDataType = 'array';

  /**
   * Optional. A list of IP addresses or IP address ranges to match against the
   * source IP address of the request. Limited to 10 ip_blocks per Authorization
   * Policy
   *
   * @param AuthzPolicyAuthzRuleIpBlock[] $ipBlocks
   */
  public function setIpBlocks($ipBlocks)
  {
    $this->ipBlocks = $ipBlocks;
  }
  /**
   * @return AuthzPolicyAuthzRuleIpBlock[]
   */
  public function getIpBlocks()
  {
    return $this->ipBlocks;
  }
  /**
   * Optional. A list of identities derived from the client's certificate. This
   * field will not match on a request unless frontend mutual TLS is enabled for
   * the forwarding rule or Gateway and the client certificate has been
   * successfully validated by mTLS. Each identity is a string whose value is
   * matched against a list of URI SANs, DNS Name SANs, or the common name in
   * the client's certificate. A match happens when any principal matches with
   * the rule. Limited to 50 principals per Authorization Policy for regional
   * internal Application Load Balancers, regional external Application Load
   * Balancers, cross-region internal Application Load Balancers, and Cloud
   * Service Mesh. This field is not supported for global external Application
   * Load Balancers.
   *
   * @param AuthzPolicyAuthzRulePrincipal[] $principals
   */
  public function setPrincipals($principals)
  {
    $this->principals = $principals;
  }
  /**
   * @return AuthzPolicyAuthzRulePrincipal[]
   */
  public function getPrincipals()
  {
    return $this->principals;
  }
  /**
   * Optional. A list of resources to match against the resource of the source
   * VM of a request. Limited to 10 resources per Authorization Policy.
   *
   * @param AuthzPolicyAuthzRuleRequestResource[] $resources
   */
  public function setResources($resources)
  {
    $this->resources = $resources;
  }
  /**
   * @return AuthzPolicyAuthzRuleRequestResource[]
   */
  public function getResources()
  {
    return $this->resources;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AuthzPolicyAuthzRuleFromRequestSource::class, 'Google_Service_NetworkSecurity_AuthzPolicyAuthzRuleFromRequestSource');
