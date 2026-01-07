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

class FutureReservationStatusLastKnownGoodState extends \Google\Model
{
  /**
   * Future reservation is approved by GCP.
   */
  public const PROCUREMENT_STATUS_APPROVED = 'APPROVED';
  /**
   * Future reservation is cancelled by the customer.
   */
  public const PROCUREMENT_STATUS_CANCELLED = 'CANCELLED';
  /**
   * Future reservation is committed by the customer.
   */
  public const PROCUREMENT_STATUS_COMMITTED = 'COMMITTED';
  /**
   * Future reservation is rejected by GCP.
   */
  public const PROCUREMENT_STATUS_DECLINED = 'DECLINED';
  /**
   * Related status for PlanningStatus.Draft. Transitions to PENDING_APPROVAL
   * upon user submitting FR.
   */
  public const PROCUREMENT_STATUS_DRAFTING = 'DRAFTING';
  /**
   * Future reservation failed. No additional reservations were provided.
   */
  public const PROCUREMENT_STATUS_FAILED = 'FAILED';
  /**
   * Future reservation is partially fulfilled. Additional reservations were
   * provided but did not reach total_count reserved instance slots.
   */
  public const PROCUREMENT_STATUS_FAILED_PARTIALLY_FULFILLED = 'FAILED_PARTIALLY_FULFILLED';
  /**
   * Future reservation is fulfilled completely.
   */
  public const PROCUREMENT_STATUS_FULFILLED = 'FULFILLED';
  /**
   * An Amendment to the Future Reservation has been requested. If the Amendment
   * is declined, the Future Reservation will be restored to the last known good
   * state.
   */
  public const PROCUREMENT_STATUS_PENDING_AMENDMENT_APPROVAL = 'PENDING_AMENDMENT_APPROVAL';
  /**
   * Future reservation is pending approval by GCP.
   */
  public const PROCUREMENT_STATUS_PENDING_APPROVAL = 'PENDING_APPROVAL';
  public const PROCUREMENT_STATUS_PROCUREMENT_STATUS_UNSPECIFIED = 'PROCUREMENT_STATUS_UNSPECIFIED';
  /**
   * Future reservation is being procured by GCP. Beyond this point, Future
   * reservation is locked and no further modifications are allowed.
   */
  public const PROCUREMENT_STATUS_PROCURING = 'PROCURING';
  /**
   * Future reservation capacity is being provisioned. This state will be
   * entered after start_time, while reservations are being created to provide
   * total_count reserved instance slots. This state will not persist past
   * start_time + 24h.
   */
  public const PROCUREMENT_STATUS_PROVISIONING = 'PROVISIONING';
  /**
   * Output only. [Output Only] The description of the FutureReservation before
   * an amendment was requested.
   *
   * @var string
   */
  public $description;
  protected $existingMatchingUsageInfoType = FutureReservationStatusExistingMatchingUsageInfo::class;
  protected $existingMatchingUsageInfoDataType = '';
  protected $futureReservationSpecsType = FutureReservationStatusLastKnownGoodStateFutureReservationSpecs::class;
  protected $futureReservationSpecsDataType = '';
  /**
   * Output only. [Output Only] The lock time of the FutureReservation before an
   * amendment was requested.
   *
   * @var string
   */
  public $lockTime;
  /**
   * Output only. [Output Only] The name prefix of the Future Reservation before
   * an amendment was requested.
   *
   * @var string
   */
  public $namePrefix;
  /**
   * Output only. [Output Only] The status of the last known good state for the
   * Future Reservation.
   *
   * @var string
   */
  public $procurementStatus;

  /**
   * Output only. [Output Only] The description of the FutureReservation before
   * an amendment was requested.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Output only. [Output Only] Represents the matching usage for the future
   * reservation before an amendment was requested.
   *
   * @param FutureReservationStatusExistingMatchingUsageInfo $existingMatchingUsageInfo
   */
  public function setExistingMatchingUsageInfo(FutureReservationStatusExistingMatchingUsageInfo $existingMatchingUsageInfo)
  {
    $this->existingMatchingUsageInfo = $existingMatchingUsageInfo;
  }
  /**
   * @return FutureReservationStatusExistingMatchingUsageInfo
   */
  public function getExistingMatchingUsageInfo()
  {
    return $this->existingMatchingUsageInfo;
  }
  /**
   * @param FutureReservationStatusLastKnownGoodStateFutureReservationSpecs $futureReservationSpecs
   */
  public function setFutureReservationSpecs(FutureReservationStatusLastKnownGoodStateFutureReservationSpecs $futureReservationSpecs)
  {
    $this->futureReservationSpecs = $futureReservationSpecs;
  }
  /**
   * @return FutureReservationStatusLastKnownGoodStateFutureReservationSpecs
   */
  public function getFutureReservationSpecs()
  {
    return $this->futureReservationSpecs;
  }
  /**
   * Output only. [Output Only] The lock time of the FutureReservation before an
   * amendment was requested.
   *
   * @param string $lockTime
   */
  public function setLockTime($lockTime)
  {
    $this->lockTime = $lockTime;
  }
  /**
   * @return string
   */
  public function getLockTime()
  {
    return $this->lockTime;
  }
  /**
   * Output only. [Output Only] The name prefix of the Future Reservation before
   * an amendment was requested.
   *
   * @param string $namePrefix
   */
  public function setNamePrefix($namePrefix)
  {
    $this->namePrefix = $namePrefix;
  }
  /**
   * @return string
   */
  public function getNamePrefix()
  {
    return $this->namePrefix;
  }
  /**
   * Output only. [Output Only] The status of the last known good state for the
   * Future Reservation.
   *
   * Accepted values: APPROVED, CANCELLED, COMMITTED, DECLINED, DRAFTING,
   * FAILED, FAILED_PARTIALLY_FULFILLED, FULFILLED, PENDING_AMENDMENT_APPROVAL,
   * PENDING_APPROVAL, PROCUREMENT_STATUS_UNSPECIFIED, PROCURING, PROVISIONING
   *
   * @param self::PROCUREMENT_STATUS_* $procurementStatus
   */
  public function setProcurementStatus($procurementStatus)
  {
    $this->procurementStatus = $procurementStatus;
  }
  /**
   * @return self::PROCUREMENT_STATUS_*
   */
  public function getProcurementStatus()
  {
    return $this->procurementStatus;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FutureReservationStatusLastKnownGoodState::class, 'Google_Service_Compute_FutureReservationStatusLastKnownGoodState');
