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

class RRSetRoutingPolicyGeoPolicyGeoPolicyItem extends \Google\Collection
{
  protected $collection_key = 'signatureRrdatas';
  protected $healthCheckedTargetsType = RRSetRoutingPolicyHealthCheckTargets::class;
  protected $healthCheckedTargetsDataType = '';
  /**
   * @var string
   */
  public $kind;
  /**
   * The geo-location granularity is a GCP region. This location string should
   * correspond to a GCP region. e.g. "us-east1", "southamerica-east1", "asia-
   * east1", etc.
   *
   * @var string
   */
  public $location;
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
   * For A and AAAA types only. Endpoints to return in the query result only if
   * they are healthy. These can be specified along with `rrdata` within this
   * item.
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
   * The geo-location granularity is a GCP region. This location string should
   * correspond to a GCP region. e.g. "us-east1", "southamerica-east1", "asia-
   * east1", etc.
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RRSetRoutingPolicyGeoPolicyGeoPolicyItem::class, 'Google_Service_Dns_RRSetRoutingPolicyGeoPolicyGeoPolicyItem');
