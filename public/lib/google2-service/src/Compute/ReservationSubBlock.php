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

class ReservationSubBlock extends \Google\Model
{
  /**
   * Resources are being allocated for the reservation subBlock.
   */
  public const STATUS_CREATING = 'CREATING';
  /**
   * Reservation subBlock is currently being deleted.
   */
  public const STATUS_DELETING = 'DELETING';
  public const STATUS_INVALID = 'INVALID';
  /**
   * Reservation subBlock has allocated all its resources.
   */
  public const STATUS_READY = 'READY';
  protected $acceleratorTopologiesInfoType = AcceleratorTopologiesInfo::class;
  protected $acceleratorTopologiesInfoDataType = '';
  /**
   * Output only. [Output Only] The number of hosts that are allocated in this
   * reservation subBlock.
   *
   * @var int
   */
  public $count;
  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @var string
   */
  public $creationTimestamp;
  protected $healthInfoType = ReservationSubBlockHealthInfo::class;
  protected $healthInfoDataType = '';
  /**
   * Output only. [Output Only] The unique identifier for the resource. This
   * identifier is defined by the server.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. [Output Only] The number of instances that are currently in
   * use on this reservation subBlock.
   *
   * @var int
   */
  public $inUseCount;
  /**
   * Output only. [Output Only] Type of the resource.
   * Alwayscompute#reservationSubBlock for reservation subBlocks.
   *
   * @var string
   */
  public $kind;
  /**
   * Output only. [Output Only] The name of this reservation subBlock generated
   * by Google Compute Engine. The name must be 1-63 characters long, and comply
   * with RFC1035 @pattern [a-z](?:[-a-z0-9]{0,61}[a-z0-9])?
   *
   * @var string
   */
  public $name;
  protected $physicalTopologyType = ReservationSubBlockPhysicalTopology::class;
  protected $physicalTopologyDataType = '';
  protected $reservationSubBlockMaintenanceType = GroupMaintenanceInfo::class;
  protected $reservationSubBlockMaintenanceDataType = '';
  /**
   * Output only. [Output Only] Server-defined fully-qualified URL for this
   * resource.
   *
   * @var string
   */
  public $selfLink;
  /**
   * Output only. [Output Only] Server-defined URL for this resource with the
   * resource id.
   *
   * @var string
   */
  public $selfLinkWithId;
  /**
   * Output only. [Output Only] Status of the reservation subBlock.
   *
   * @var string
   */
  public $status;
  /**
   * Output only. [Output Only] Zone in which the reservation subBlock resides.
   *
   * @var string
   */
  public $zone;

  /**
   * Output only. [Output Only] Slice info for the reservation subBlock.
   *
   * @param AcceleratorTopologiesInfo $acceleratorTopologiesInfo
   */
  public function setAcceleratorTopologiesInfo(AcceleratorTopologiesInfo $acceleratorTopologiesInfo)
  {
    $this->acceleratorTopologiesInfo = $acceleratorTopologiesInfo;
  }
  /**
   * @return AcceleratorTopologiesInfo
   */
  public function getAcceleratorTopologiesInfo()
  {
    return $this->acceleratorTopologiesInfo;
  }
  /**
   * Output only. [Output Only] The number of hosts that are allocated in this
   * reservation subBlock.
   *
   * @param int $count
   */
  public function setCount($count)
  {
    $this->count = $count;
  }
  /**
   * @return int
   */
  public function getCount()
  {
    return $this->count;
  }
  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @param string $creationTimestamp
   */
  public function setCreationTimestamp($creationTimestamp)
  {
    $this->creationTimestamp = $creationTimestamp;
  }
  /**
   * @return string
   */
  public function getCreationTimestamp()
  {
    return $this->creationTimestamp;
  }
  /**
   * Output only. [Output Only] Health information for the reservation subBlock.
   *
   * @param ReservationSubBlockHealthInfo $healthInfo
   */
  public function setHealthInfo(ReservationSubBlockHealthInfo $healthInfo)
  {
    $this->healthInfo = $healthInfo;
  }
  /**
   * @return ReservationSubBlockHealthInfo
   */
  public function getHealthInfo()
  {
    return $this->healthInfo;
  }
  /**
   * Output only. [Output Only] The unique identifier for the resource. This
   * identifier is defined by the server.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Output only. [Output Only] The number of instances that are currently in
   * use on this reservation subBlock.
   *
   * @param int $inUseCount
   */
  public function setInUseCount($inUseCount)
  {
    $this->inUseCount = $inUseCount;
  }
  /**
   * @return int
   */
  public function getInUseCount()
  {
    return $this->inUseCount;
  }
  /**
   * Output only. [Output Only] Type of the resource.
   * Alwayscompute#reservationSubBlock for reservation subBlocks.
   *
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
   * Output only. [Output Only] The name of this reservation subBlock generated
   * by Google Compute Engine. The name must be 1-63 characters long, and comply
   * with RFC1035 @pattern [a-z](?:[-a-z0-9]{0,61}[a-z0-9])?
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. [Output Only] The physical topology of the reservation
   * subBlock.
   *
   * @param ReservationSubBlockPhysicalTopology $physicalTopology
   */
  public function setPhysicalTopology(ReservationSubBlockPhysicalTopology $physicalTopology)
  {
    $this->physicalTopology = $physicalTopology;
  }
  /**
   * @return ReservationSubBlockPhysicalTopology
   */
  public function getPhysicalTopology()
  {
    return $this->physicalTopology;
  }
  /**
   * Output only. Maintenance information for this reservation subBlock.
   *
   * @param GroupMaintenanceInfo $reservationSubBlockMaintenance
   */
  public function setReservationSubBlockMaintenance(GroupMaintenanceInfo $reservationSubBlockMaintenance)
  {
    $this->reservationSubBlockMaintenance = $reservationSubBlockMaintenance;
  }
  /**
   * @return GroupMaintenanceInfo
   */
  public function getReservationSubBlockMaintenance()
  {
    return $this->reservationSubBlockMaintenance;
  }
  /**
   * Output only. [Output Only] Server-defined fully-qualified URL for this
   * resource.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * Output only. [Output Only] Server-defined URL for this resource with the
   * resource id.
   *
   * @param string $selfLinkWithId
   */
  public function setSelfLinkWithId($selfLinkWithId)
  {
    $this->selfLinkWithId = $selfLinkWithId;
  }
  /**
   * @return string
   */
  public function getSelfLinkWithId()
  {
    return $this->selfLinkWithId;
  }
  /**
   * Output only. [Output Only] Status of the reservation subBlock.
   *
   * Accepted values: CREATING, DELETING, INVALID, READY
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Output only. [Output Only] Zone in which the reservation subBlock resides.
   *
   * @param string $zone
   */
  public function setZone($zone)
  {
    $this->zone = $zone;
  }
  /**
   * @return string
   */
  public function getZone()
  {
    return $this->zone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReservationSubBlock::class, 'Google_Service_Compute_ReservationSubBlock');
