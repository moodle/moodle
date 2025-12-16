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

namespace Google\Service\Baremetalsolution;

class SnapshotReservationDetail extends \Google\Model
{
  /**
   * The space on this storage volume reserved for snapshots, shown in GiB.
   *
   * @var string
   */
  public $reservedSpaceGib;
  /**
   * Percent of the total Volume size reserved for snapshot copies. Enabling
   * snapshots requires reserving 20% or more of the storage volume space for
   * snapshots. Maximum reserved space for snapshots is 40%. Setting this field
   * will effectively set snapshot_enabled to true.
   *
   * @var int
   */
  public $reservedSpacePercent;
  /**
   * The amount, in GiB, of available space in this storage volume's reserved
   * snapshot space.
   *
   * @var string
   */
  public $reservedSpaceRemainingGib;
  /**
   * The percent of snapshot space on this storage volume actually being used by
   * the snapshot copies. This value might be higher than 100% if the snapshot
   * copies have overflowed into the data portion of the storage volume.
   *
   * @var int
   */
  public $reservedSpaceUsedPercent;

  /**
   * The space on this storage volume reserved for snapshots, shown in GiB.
   *
   * @param string $reservedSpaceGib
   */
  public function setReservedSpaceGib($reservedSpaceGib)
  {
    $this->reservedSpaceGib = $reservedSpaceGib;
  }
  /**
   * @return string
   */
  public function getReservedSpaceGib()
  {
    return $this->reservedSpaceGib;
  }
  /**
   * Percent of the total Volume size reserved for snapshot copies. Enabling
   * snapshots requires reserving 20% or more of the storage volume space for
   * snapshots. Maximum reserved space for snapshots is 40%. Setting this field
   * will effectively set snapshot_enabled to true.
   *
   * @param int $reservedSpacePercent
   */
  public function setReservedSpacePercent($reservedSpacePercent)
  {
    $this->reservedSpacePercent = $reservedSpacePercent;
  }
  /**
   * @return int
   */
  public function getReservedSpacePercent()
  {
    return $this->reservedSpacePercent;
  }
  /**
   * The amount, in GiB, of available space in this storage volume's reserved
   * snapshot space.
   *
   * @param string $reservedSpaceRemainingGib
   */
  public function setReservedSpaceRemainingGib($reservedSpaceRemainingGib)
  {
    $this->reservedSpaceRemainingGib = $reservedSpaceRemainingGib;
  }
  /**
   * @return string
   */
  public function getReservedSpaceRemainingGib()
  {
    return $this->reservedSpaceRemainingGib;
  }
  /**
   * The percent of snapshot space on this storage volume actually being used by
   * the snapshot copies. This value might be higher than 100% if the snapshot
   * copies have overflowed into the data portion of the storage volume.
   *
   * @param int $reservedSpaceUsedPercent
   */
  public function setReservedSpaceUsedPercent($reservedSpaceUsedPercent)
  {
    $this->reservedSpaceUsedPercent = $reservedSpaceUsedPercent;
  }
  /**
   * @return int
   */
  public function getReservedSpaceUsedPercent()
  {
    return $this->reservedSpaceUsedPercent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SnapshotReservationDetail::class, 'Google_Service_Baremetalsolution_SnapshotReservationDetail');
