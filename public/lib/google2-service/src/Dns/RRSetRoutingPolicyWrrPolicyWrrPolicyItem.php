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

class RRSetRoutingPolicyWrrPolicyWrrPolicyItem extends \Google\Collection
{
  protected $collection_key = 'signatureRrdatas';
  protected $healthCheckedTargetsType = RRSetRoutingPolicyHealthCheckTargets::class;
  protected $healthCheckedTargetsDataType = '';
  /**
   * @var string
   */
  public $kind;
  /**
   * @var string[]
   */
  public $rrdatas;
  /**
   * DNSSEC generated signatures for all the `rrdata` within this item. When
   * using health-checked targets for DNSSEC-enabled zones, you can only use at
   * most one health-checked IP address per item.
   *
   * @var string[]
   */
  public $signatureRrdatas;
  /**
   * The weight corresponding to this `WrrPolicyItem` object. When multiple
   * `WrrPolicyItem` objects are configured, the probability of returning an
   * `WrrPolicyItem` object's data is proportional to its weight relative to the
   * sum of weights configured for all items. This weight must be non-negative.
   *
   * @var 
   */
  public $weight;

  /**
   * Endpoints that are health checked before making the routing decision. The
   * unhealthy endpoints are omitted from the result. If all endpoints within a
   * bucket are unhealthy, we choose a different bucket (sampled with respect to
   * its weight) for responding. If DNSSEC is enabled for this zone, only one of
   * `rrdata` or `health_checked_targets` can be set.
   *
   * @param RRSetRoutingPolicyHealthCheckTargets $healthCheckedTargets
   */
  public function setHealthCheckedTargets(RRSetRoutingPolicyHealthCheckTargets $healthCheckedTargets)
  {
    $this->healthCheckedTargets = $healthCheckedTargets;
  }
  /**
   * @return RRSetRoutingPolicyHealthCheckTargets
   */
  public function getHealthCheckedTargets()
  {
    return $this->healthCheckedTargets;
  }
  /**
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * @param string[] $rrdatas
   */
  public function setRrdatas($rrdatas)
  {
    $this->rrdatas = $rrdatas;
  }
  /**
   * @return string[]
   */
  public function getRrdatas()
  {
    return $this->rrdatas;
  }
  /**
   * DNSSEC generated signatures for all the `rrdata` within this item. When
   * using health-checked targets for DNSSEC-enabled zones, you can only use at
   * most one health-checked IP address per item.
   *
   * @param string[] $signatureRrdatas
   */
  public function setSignatureRrdatas($signatureRrdatas)
  {
    $this->signatureRrdatas = $signatureRrdatas;
  }
  /**
   * @return string[]
   */
  public function getSignatureRrdatas()
  {
    return $this->signatureRrdatas;
  }
  public function setWeight($weight)
  {
    $this->weight = $weight;
  }
  public function getWeight()
  {
    return $this->weight;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RRSetRoutingPolicyWrrPolicyWrrPolicyItem::class, 'Google_Service_Dns_RRSetRoutingPolicyWrrPolicyWrrPolicyItem');
