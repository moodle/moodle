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

namespace Google\Service\CloudDomains;

class GeoPolicyItem extends \Google\Collection
{
  protected $collection_key = 'signatureRrdata';
  protected $healthCheckedTargetsType = HealthCheckTargets::class;
  protected $healthCheckedTargetsDataType = '';
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
  public $rrdata;
  /**
   * DNSSEC generated signatures for all the `rrdata` within this item. When
   * using health-checked targets for DNSSEC-enabled zones, you can only use at
   * most one health-checked IP address per item.
   *
   * @var string[]
   */
  public $signatureRrdata;

  /**
   * For A and AAAA types only. Endpoints to return in the query result only if
   * they are healthy. These can be specified along with `rrdata` within this
   * item.
   *
   * @param HealthCheckTargets $healthCheckedTargets
   */
  public function setHealthCheckedTargets(HealthCheckTargets $healthCheckedTargets)
  {
    $this->healthCheckedTargets = $healthCheckedTargets;
  }
  /**
   * @return HealthCheckTargets
   */
  public function getHealthCheckedTargets()
  {
    return $this->healthCheckedTargets;
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
   * @param string[] $rrdata
   */
  public function setRrdata($rrdata)
  {
    $this->rrdata = $rrdata;
  }
  /**
   * @return string[]
   */
  public function getRrdata()
  {
    return $this->rrdata;
  }
  /**
   * DNSSEC generated signatures for all the `rrdata` within this item. When
   * using health-checked targets for DNSSEC-enabled zones, you can only use at
   * most one health-checked IP address per item.
   *
   * @param string[] $signatureRrdata
   */
  public function setSignatureRrdata($signatureRrdata)
  {
    $this->signatureRrdata = $signatureRrdata;
  }
  /**
   * @return string[]
   */
  public function getSignatureRrdata()
  {
    return $this->signatureRrdata;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GeoPolicyItem::class, 'Google_Service_CloudDomains_GeoPolicyItem');
