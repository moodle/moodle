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

namespace Google\Service\Compute;

class ResourceStatus extends \Google\Model
{
  protected $effectiveInstanceMetadataType = ResourceStatusEffectiveInstanceMetadata::class;
  protected $effectiveInstanceMetadataDataType = '';
  /**
   * Output only. [Output Only] The precise location of your instance within the
   * zone's data center, including the block, sub-block, and host. The field is
   * formatted as follows: blockId/subBlockId/hostId.
   *
   * @var string
   */
  public $physicalHost;
  protected $physicalHostTopologyType = ResourceStatusPhysicalHostTopology::class;
  protected $physicalHostTopologyDataType = '';
  protected $reservationConsumptionInfoType = ResourceStatusReservationConsumptionInfo::class;
  protected $reservationConsumptionInfoDataType = '';
  protected $schedulingType = ResourceStatusScheduling::class;
  protected $schedulingDataType = '';
  protected $upcomingMaintenanceType = UpcomingMaintenance::class;
  protected $upcomingMaintenanceDataType = '';

  /**
   * Output only. [Output Only] Effective metadata is a field that consolidates
   * project, zonal instance settings, and instance-level predefined metadata
   * keys to provide the overridden value for those metadata keys at the
   * instance level.
   *
   * @param ResourceStatusEffectiveInstanceMetadata $effectiveInstanceMetadata
   */
  public function setEffectiveInstanceMetadata(ResourceStatusEffectiveInstanceMetadata $effectiveInstanceMetadata)
  {
    $this->effectiveInstanceMetadata = $effectiveInstanceMetadata;
  }
  /**
   * @return ResourceStatusEffectiveInstanceMetadata
   */
  public function getEffectiveInstanceMetadata()
  {
    return $this->effectiveInstanceMetadata;
  }
  /**
   * Output only. [Output Only] The precise location of your instance within the
   * zone's data center, including the block, sub-block, and host. The field is
   * formatted as follows: blockId/subBlockId/hostId.
   *
   * @param string $physicalHost
   */
  public function setPhysicalHost($physicalHost)
  {
    $this->physicalHost = $physicalHost;
  }
  /**
   * @return string
   */
  public function getPhysicalHost()
  {
    return $this->physicalHost;
  }
  /**
   * Output only. [Output Only] A series of fields containing the global name of
   * the Compute Engine cluster, as well as the ID of the block, sub-block, and
   * host on which the running instance is located.
   *
   * @param ResourceStatusPhysicalHostTopology $physicalHostTopology
   */
  public function setPhysicalHostTopology(ResourceStatusPhysicalHostTopology $physicalHostTopology)
  {
    $this->physicalHostTopology = $physicalHostTopology;
  }
  /**
   * @return ResourceStatusPhysicalHostTopology
   */
  public function getPhysicalHostTopology()
  {
    return $this->physicalHostTopology;
  }
  /**
   * Output only. [Output Only] Reservation information that the instance is
   * consuming from.
   *
   * @param ResourceStatusReservationConsumptionInfo $reservationConsumptionInfo
   */
  public function setReservationConsumptionInfo(ResourceStatusReservationConsumptionInfo $reservationConsumptionInfo)
  {
    $this->reservationConsumptionInfo = $reservationConsumptionInfo;
  }
  /**
   * @return ResourceStatusReservationConsumptionInfo
   */
  public function getReservationConsumptionInfo()
  {
    return $this->reservationConsumptionInfo;
  }
  /**
   * @param ResourceStatusScheduling $scheduling
   */
  public function setScheduling(ResourceStatusScheduling $scheduling)
  {
    $this->scheduling = $scheduling;
  }
  /**
   * @return ResourceStatusScheduling
   */
  public function getScheduling()
  {
    return $this->scheduling;
  }
  /**
   * @param UpcomingMaintenance $upcomingMaintenance
   */
  public function setUpcomingMaintenance(UpcomingMaintenance $upcomingMaintenance)
  {
    $this->upcomingMaintenance = $upcomingMaintenance;
  }
  /**
   * @return UpcomingMaintenance
   */
  public function getUpcomingMaintenance()
  {
    return $this->upcomingMaintenance;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResourceStatus::class, 'Google_Service_Compute_ResourceStatus');
