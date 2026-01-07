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

class GroupMaintenanceInfo extends \Google\Model
{
  /**
   * Maintenance on all reserved instances in the reservation is synchronized.
   */
  public const SCHEDULING_TYPE_GROUPED = 'GROUPED';
  /**
   * Unknown maintenance type.
   */
  public const SCHEDULING_TYPE_GROUP_MAINTENANCE_TYPE_UNSPECIFIED = 'GROUP_MAINTENANCE_TYPE_UNSPECIFIED';
  /**
   * Maintenance is not synchronized for this reservation. Instead, each
   * instance has its own maintenance window.
   */
  public const SCHEDULING_TYPE_INDEPENDENT = 'INDEPENDENT';
  /**
   * Describes number of instances that have ongoing maintenance.
   *
   * @var int
   */
  public $instanceMaintenanceOngoingCount;
  /**
   * Describes number of instances that have pending maintenance.
   *
   * @var int
   */
  public $instanceMaintenancePendingCount;
  /**
   * Progress for ongoing maintenance for this group of VMs/hosts. Describes
   * number of hosts in the block that have ongoing maintenance.
   *
   * @var int
   */
  public $maintenanceOngoingCount;
  /**
   * Progress for ongoing maintenance for this group of VMs/hosts. Describes
   * number of hosts in the block that have pending maintenance.
   *
   * @var int
   */
  public $maintenancePendingCount;
  /**
   * The type of maintenance for the reservation.
   *
   * @var string
   */
  public $schedulingType;
  /**
   * Describes number of subblock Infrastructure that has ongoing maintenance.
   * Here, Subblock Infrastructure Maintenance pertains to upstream hardware
   * contained in the Subblock that is necessary for a VM Family(e.g. NVLink
   * Domains). Not all VM Families will support this field.
   *
   * @var int
   */
  public $subblockInfraMaintenanceOngoingCount;
  /**
   * Describes number of subblock Infrastructure that has pending maintenance.
   * Here, Subblock Infrastructure Maintenance pertains to upstream hardware
   * contained in the Subblock that is necessary for a VM Family (e.g. NVLink
   * Domains). Not all VM Families will support this field.
   *
   * @var int
   */
  public $subblockInfraMaintenancePendingCount;
  protected $upcomingGroupMaintenanceType = UpcomingMaintenance::class;
  protected $upcomingGroupMaintenanceDataType = '';

  /**
   * Describes number of instances that have ongoing maintenance.
   *
   * @param int $instanceMaintenanceOngoingCount
   */
  public function setInstanceMaintenanceOngoingCount($instanceMaintenanceOngoingCount)
  {
    $this->instanceMaintenanceOngoingCount = $instanceMaintenanceOngoingCount;
  }
  /**
   * @return int
   */
  public function getInstanceMaintenanceOngoingCount()
  {
    return $this->instanceMaintenanceOngoingCount;
  }
  /**
   * Describes number of instances that have pending maintenance.
   *
   * @param int $instanceMaintenancePendingCount
   */
  public function setInstanceMaintenancePendingCount($instanceMaintenancePendingCount)
  {
    $this->instanceMaintenancePendingCount = $instanceMaintenancePendingCount;
  }
  /**
   * @return int
   */
  public function getInstanceMaintenancePendingCount()
  {
    return $this->instanceMaintenancePendingCount;
  }
  /**
   * Progress for ongoing maintenance for this group of VMs/hosts. Describes
   * number of hosts in the block that have ongoing maintenance.
   *
   * @param int $maintenanceOngoingCount
   */
  public function setMaintenanceOngoingCount($maintenanceOngoingCount)
  {
    $this->maintenanceOngoingCount = $maintenanceOngoingCount;
  }
  /**
   * @return int
   */
  public function getMaintenanceOngoingCount()
  {
    return $this->maintenanceOngoingCount;
  }
  /**
   * Progress for ongoing maintenance for this group of VMs/hosts. Describes
   * number of hosts in the block that have pending maintenance.
   *
   * @param int $maintenancePendingCount
   */
  public function setMaintenancePendingCount($maintenancePendingCount)
  {
    $this->maintenancePendingCount = $maintenancePendingCount;
  }
  /**
   * @return int
   */
  public function getMaintenancePendingCount()
  {
    return $this->maintenancePendingCount;
  }
  /**
   * The type of maintenance for the reservation.
   *
   * Accepted values: GROUPED, GROUP_MAINTENANCE_TYPE_UNSPECIFIED, INDEPENDENT
   *
   * @param self::SCHEDULING_TYPE_* $schedulingType
   */
  public function setSchedulingType($schedulingType)
  {
    $this->schedulingType = $schedulingType;
  }
  /**
   * @return self::SCHEDULING_TYPE_*
   */
  public function getSchedulingType()
  {
    return $this->schedulingType;
  }
  /**
   * Describes number of subblock Infrastructure that has ongoing maintenance.
   * Here, Subblock Infrastructure Maintenance pertains to upstream hardware
   * contained in the Subblock that is necessary for a VM Family(e.g. NVLink
   * Domains). Not all VM Families will support this field.
   *
   * @param int $subblockInfraMaintenanceOngoingCount
   */
  public function setSubblockInfraMaintenanceOngoingCount($subblockInfraMaintenanceOngoingCount)
  {
    $this->subblockInfraMaintenanceOngoingCount = $subblockInfraMaintenanceOngoingCount;
  }
  /**
   * @return int
   */
  public function getSubblockInfraMaintenanceOngoingCount()
  {
    return $this->subblockInfraMaintenanceOngoingCount;
  }
  /**
   * Describes number of subblock Infrastructure that has pending maintenance.
   * Here, Subblock Infrastructure Maintenance pertains to upstream hardware
   * contained in the Subblock that is necessary for a VM Family (e.g. NVLink
   * Domains). Not all VM Families will support this field.
   *
   * @param int $subblockInfraMaintenancePendingCount
   */
  public function setSubblockInfraMaintenancePendingCount($subblockInfraMaintenancePendingCount)
  {
    $this->subblockInfraMaintenancePendingCount = $subblockInfraMaintenancePendingCount;
  }
  /**
   * @return int
   */
  public function getSubblockInfraMaintenancePendingCount()
  {
    return $this->subblockInfraMaintenancePendingCount;
  }
  /**
   * Maintenance information on this group of VMs.
   *
   * @param UpcomingMaintenance $upcomingGroupMaintenance
   */
  public function setUpcomingGroupMaintenance(UpcomingMaintenance $upcomingGroupMaintenance)
  {
    $this->upcomingGroupMaintenance = $upcomingGroupMaintenance;
  }
  /**
   * @return UpcomingMaintenance
   */
  public function getUpcomingGroupMaintenance()
  {
    return $this->upcomingGroupMaintenance;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GroupMaintenanceInfo::class, 'Google_Service_Compute_GroupMaintenanceInfo');
