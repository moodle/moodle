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

class AuthzPolicyTarget extends \Google\Collection
{
  /**
   * Default value. Do not use.
   */
  public const LOAD_BALANCING_SCHEME_LOAD_BALANCING_SCHEME_UNSPECIFIED = 'LOAD_BALANCING_SCHEME_UNSPECIFIED';
  /**
   * Signifies that this is used for Regional internal or Cross-region internal
   * Application Load Balancing.
   */
  public const LOAD_BALANCING_SCHEME_INTERNAL_MANAGED = 'INTERNAL_MANAGED';
  /**
   * Signifies that this is used for Global external or Regional external
   * Application Load Balancing.
   */
  public const LOAD_BALANCING_SCHEME_EXTERNAL_MANAGED = 'EXTERNAL_MANAGED';
  /**
   * Signifies that this is used for Cloud Service Mesh. Meant for use by CSM
   * GKE controller only.
   */
  public const LOAD_BALANCING_SCHEME_INTERNAL_SELF_MANAGED = 'INTERNAL_SELF_MANAGED';
  protected $collection_key = 'resources';
  /**
   * Required. All gateways and forwarding rules referenced by this policy and
   * extensions must share the same load balancing scheme. Supported values:
   * `INTERNAL_MANAGED` and `EXTERNAL_MANAGED`. For more information, refer to
   * [Backend services overview](https://cloud.google.com/load-
   * balancing/docs/backend-service).
   *
   * @var string
   */
  public $loadBalancingScheme;
  /**
   * Required. A list of references to the Forwarding Rules on which this policy
   * will be applied.
   *
   * @var string[]
   */
  public $resources;

  /**
   * Required. All gateways and forwarding rules referenced by this policy and
   * extensions must share the same load balancing scheme. Supported values:
   * `INTERNAL_MANAGED` and `EXTERNAL_MANAGED`. For more information, refer to
   * [Backend services overview](https://cloud.google.com/load-
   * balancing/docs/backend-service).
   *
   * Accepted values: LOAD_BALANCING_SCHEME_UNSPECIFIED, INTERNAL_MANAGED,
   * EXTERNAL_MANAGED, INTERNAL_SELF_MANAGED
   *
   * @param self::LOAD_BALANCING_SCHEME_* $loadBalancingScheme
   */
  public function setLoadBalancingScheme($loadBalancingScheme)
  {
    $this->loadBalancingScheme = $loadBalancingScheme;
  }
  /**
   * @return self::LOAD_BALANCING_SCHEME_*
   */
  public function getLoadBalancingScheme()
  {
    return $this->loadBalancingScheme;
  }
  /**
   * Required. A list of references to the Forwarding Rules on which this policy
   * will be applied.
   *
   * @param string[] $resources
   */
  public function setResources($resources)
  {
    $this->resources = $resources;
  }
  /**
   * @return string[]
   */
  public function getResources()
  {
    return $this->resources;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AuthzPolicyTarget::class, 'Google_Service_NetworkSecurity_AuthzPolicyTarget');
