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

class AllocationResourceStatus extends \Google\Model
{
  protected $healthInfoType = AllocationResourceStatusHealthInfo::class;
  protected $healthInfoDataType = '';
  /**
   * The number of reservation blocks associated with this reservation.
   *
   * @var int
   */
  public $reservationBlockCount;
  protected $reservationMaintenanceType = GroupMaintenanceInfo::class;
  protected $reservationMaintenanceDataType = '';
  protected $specificSkuAllocationType = AllocationResourceStatusSpecificSKUAllocation::class;
  protected $specificSkuAllocationDataType = '';

  /**
   * [Output only] Health information for the reservation.
   *
   * @param AllocationResourceStatusHealthInfo $healthInfo
   */
  public function setHealthInfo(AllocationResourceStatusHealthInfo $healthInfo)
  {
    $this->healthInfo = $healthInfo;
  }
  /**
   * @return AllocationResourceStatusHealthInfo
   */
  public function getHealthInfo()
  {
    return $this->healthInfo;
  }
  /**
   * The number of reservation blocks associated with this reservation.
   *
   * @param int $reservationBlockCount
   */
  public function setReservationBlockCount($reservationBlockCount)
  {
    $this->reservationBlockCount = $reservationBlockCount;
  }
  /**
   * @return int
   */
  public function getReservationBlockCount()
  {
    return $this->reservationBlockCount;
  }
  /**
   * Maintenance information for this reservation
   *
   * @param GroupMaintenanceInfo $reservationMaintenance
   */
  public function setReservationMaintenance(GroupMaintenanceInfo $reservationMaintenance)
  {
    $this->reservationMaintenance = $reservationMaintenance;
  }
  /**
   * @return GroupMaintenanceInfo
   */
  public function getReservationMaintenance()
  {
    return $this->reservationMaintenance;
  }
  /**
   * Allocation Properties of this reservation.
   *
   * @param AllocationResourceStatusSpecificSKUAllocation $specificSkuAllocation
   */
  public function setSpecificSkuAllocation(AllocationResourceStatusSpecificSKUAllocation $specificSkuAllocation)
  {
    $this->specificSkuAllocation = $specificSkuAllocation;
  }
  /**
   * @return AllocationResourceStatusSpecificSKUAllocation
   */
  public function getSpecificSkuAllocation()
  {
    return $this->specificSkuAllocation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AllocationResourceStatus::class, 'Google_Service_Compute_AllocationResourceStatus');
