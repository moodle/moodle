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

namespace Google\Service\CloudAlloyDBAdmin;

class StorageDatabasecenterPartnerapiV1mainAvailabilityConfiguration extends \Google\Model
{
  public const AVAILABILITY_TYPE_AVAILABILITY_TYPE_UNSPECIFIED = 'AVAILABILITY_TYPE_UNSPECIFIED';
  /**
   * Zonal available instance.
   */
  public const AVAILABILITY_TYPE_ZONAL = 'ZONAL';
  /**
   * Regional available instance.
   */
  public const AVAILABILITY_TYPE_REGIONAL = 'REGIONAL';
  /**
   * Multi regional instance
   */
  public const AVAILABILITY_TYPE_MULTI_REGIONAL = 'MULTI_REGIONAL';
  /**
   * For rest of the other category
   */
  public const AVAILABILITY_TYPE_AVAILABILITY_TYPE_OTHER = 'AVAILABILITY_TYPE_OTHER';
  /**
   * Checks for existence of (multi-cluster) routing configuration that allows
   * automatic failover to a different zone/region in case of an outage.
   * Applicable to Bigtable resources.
   *
   * @var bool
   */
  public $automaticFailoverRoutingConfigured;
  /**
   * Availability type. Potential values: * `ZONAL`: The instance serves data
   * from only one zone. Outages in that zone affect data accessibility. *
   * `REGIONAL`: The instance can serve data from more than one zone in a region
   * (it is highly available).
   *
   * @var string
   */
  public $availabilityType;
  /**
   * Checks for resources that are configured to have redundancy, and ongoing
   * replication across regions
   *
   * @var bool
   */
  public $crossRegionReplicaConfigured;
  /**
   * @var bool
   */
  public $externalReplicaConfigured;
  /**
   * @var bool
   */
  public $promotableReplicaConfigured;

  /**
   * Checks for existence of (multi-cluster) routing configuration that allows
   * automatic failover to a different zone/region in case of an outage.
   * Applicable to Bigtable resources.
   *
   * @param bool $automaticFailoverRoutingConfigured
   */
  public function setAutomaticFailoverRoutingConfigured($automaticFailoverRoutingConfigured)
  {
    $this->automaticFailoverRoutingConfigured = $automaticFailoverRoutingConfigured;
  }
  /**
   * @return bool
   */
  public function getAutomaticFailoverRoutingConfigured()
  {
    return $this->automaticFailoverRoutingConfigured;
  }
  /**
   * Availability type. Potential values: * `ZONAL`: The instance serves data
   * from only one zone. Outages in that zone affect data accessibility. *
   * `REGIONAL`: The instance can serve data from more than one zone in a region
   * (it is highly available).
   *
   * Accepted values: AVAILABILITY_TYPE_UNSPECIFIED, ZONAL, REGIONAL,
   * MULTI_REGIONAL, AVAILABILITY_TYPE_OTHER
   *
   * @param self::AVAILABILITY_TYPE_* $availabilityType
   */
  public function setAvailabilityType($availabilityType)
  {
    $this->availabilityType = $availabilityType;
  }
  /**
   * @return self::AVAILABILITY_TYPE_*
   */
  public function getAvailabilityType()
  {
    return $this->availabilityType;
  }
  /**
   * Checks for resources that are configured to have redundancy, and ongoing
   * replication across regions
   *
   * @param bool $crossRegionReplicaConfigured
   */
  public function setCrossRegionReplicaConfigured($crossRegionReplicaConfigured)
  {
    $this->crossRegionReplicaConfigured = $crossRegionReplicaConfigured;
  }
  /**
   * @return bool
   */
  public function getCrossRegionReplicaConfigured()
  {
    return $this->crossRegionReplicaConfigured;
  }
  /**
   * @param bool $externalReplicaConfigured
   */
  public function setExternalReplicaConfigured($externalReplicaConfigured)
  {
    $this->externalReplicaConfigured = $externalReplicaConfigured;
  }
  /**
   * @return bool
   */
  public function getExternalReplicaConfigured()
  {
    return $this->externalReplicaConfigured;
  }
  /**
   * @param bool $promotableReplicaConfigured
   */
  public function setPromotableReplicaConfigured($promotableReplicaConfigured)
  {
    $this->promotableReplicaConfigured = $promotableReplicaConfigured;
  }
  /**
   * @return bool
   */
  public function getPromotableReplicaConfigured()
  {
    return $this->promotableReplicaConfigured;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StorageDatabasecenterPartnerapiV1mainAvailabilityConfiguration::class, 'Google_Service_CloudAlloyDBAdmin_StorageDatabasecenterPartnerapiV1mainAvailabilityConfiguration');
