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

namespace Google\Service\Dns;

class RRSetRoutingPolicyHealthCheckTargets extends \Google\Collection
{
  protected $collection_key = 'internalLoadBalancers';
  /**
   * The Internet IP addresses to be health checked. The format matches the
   * format of ResourceRecordSet.rrdata as defined in RFC 1035 (section 5) and
   * RFC 1034 (section 3.6.1)
   *
   * @var string[]
   */
  public $externalEndpoints;
  protected $internalLoadBalancersType = RRSetRoutingPolicyLoadBalancerTarget::class;
  protected $internalLoadBalancersDataType = 'array';

  /**
   * The Internet IP addresses to be health checked. The format matches the
   * format of ResourceRecordSet.rrdata as defined in RFC 1035 (section 5) and
   * RFC 1034 (section 3.6.1)
   *
   * @param string[] $externalEndpoints
   */
  public function setExternalEndpoints($externalEndpoints)
  {
    $this->externalEndpoints = $externalEndpoints;
  }
  /**
   * @return string[]
   */
  public function getExternalEndpoints()
  {
    return $this->externalEndpoints;
  }
  /**
   * Configuration for internal load balancers to be health checked.
   *
   * @param RRSetRoutingPolicyLoadBalancerTarget[] $internalLoadBalancers
   */
  public function setInternalLoadBalancers($internalLoadBalancers)
  {
    $this->internalLoadBalancers = $internalLoadBalancers;
  }
  /**
   * @return RRSetRoutingPolicyLoadBalancerTarget[]
   */
  public function getInternalLoadBalancers()
  {
    return $this->internalLoadBalancers;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RRSetRoutingPolicyHealthCheckTargets::class, 'Google_Service_Dns_RRSetRoutingPolicyHealthCheckTargets');
