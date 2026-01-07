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

class FutureReservationStatus extends \Google\Collection
{
  /**
   * The requested amendment to the Future Resevation has been approved and
   * applied by GCP.
   */
  public const AMENDMENT_STATUS_AMENDMENT_APPROVED = 'AMENDMENT_APPROVED';
  /**
   * The requested amendment to the Future Reservation has been declined by GCP
   * and the original state was restored.
   */
  public const AMENDMENT_STATUS_AMENDMENT_DECLINED = 'AMENDMENT_DECLINED';
  /**
   * The requested amendment to the Future Reservation is currently being
   * reviewd by GCP.
   */
  public const AMENDMENT_STATUS_AMENDMENT_IN_REVIEW = 'AMENDMENT_IN_REVIEW';
  public const AMENDMENT_STATUS_AMENDMENT_STATUS_UNSPECIFIED = 'AMENDMENT_STATUS_UNSPECIFIED';
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
  protected $collection_key = 'autoCreatedReservations';
  /**
   * Output only. [Output Only] The current status of the requested amendment.
   *
   * @var string
   */
  public $amendmentStatus;
  /**
   * Output only. Fully qualified urls of the automatically created reservations
   * at start_time.
   *
   * @var string[]
   */
  public $autoCreatedReservations;
  protected $existingMatchingUsageInfoType = FutureReservationStatusExistingMatchingUsageInfo::class;
  protected $existingMatchingUsageInfoDataType = '';
  /**
   * Output only. This count indicates the fulfilled capacity so far. This is
   * set during "PROVISIONING" state. This count also includes capacity
   * delivered as part of existing matching reservations.
   *
   * @var string
   */
  public $fulfilledCount;
  protected $lastKnownGoodStateType = FutureReservationStatusLastKnownGoodState::class;
  protected $lastKnownGoodStateDataType = '';
  /**
   * Output only. Time when Future Reservation would become LOCKED, after which
   * no modifications to Future Reservation will be allowed. Applicable only
   * after the Future Reservation is in the APPROVED state. The lock_time is an
   * RFC3339 string. The procurement_status will transition to PROCURING state
   * at this time.
   *
   * @var string
   */
  public $lockTime;
  /**
   * Output only. Current state of this Future Reservation
   *
   * @var string
   */
  public $procurementStatus;
  protected $specificSkuPropertiesType = FutureReservationStatusSpecificSKUProperties::class;
  protected $specificSkuPropertiesDataType = '';

  /**
   * Output only. [Output Only] The current status of the requested amendment.
   *
   * Accepted values: AMENDMENT_APPROVED, AMENDMENT_DECLINED,
   * AMENDMENT_IN_REVIEW, AMENDMENT_STATUS_UNSPECIFIED
   *
   * @param self::AMENDMENT_STATUS_* $amendmentStatus
   */
  public function setAmendmentStatus($amendmentStatus)
  {
    $this->amendmentStatus = $amendmentStatus;
  }
  /**
   * @return self::AMENDMENT_STATUS_*
   */
  public function getAmendmentStatus()
  {
    return $this->amendmentStatus;
  }
  /**
   * Output only. Fully qualified urls of the automatically created reservations
   * at start_time.
   *
   * @param string[] $autoCreatedReservations
   */
  public function setAutoCreatedReservations($autoCreatedReservations)
  {
    $this->autoCreatedReservations = $autoCreatedReservations;
  }
  /**
   * @return string[]
   */
  public function getAutoCreatedReservations()
  {
    return $this->autoCreatedReservations;
  }
  /**
   * Output only. [Output Only] Represents the existing matching usage for the
   * future reservation.
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
   * Output only. This count indicates the fulfilled capacity so far. This is
   * set during "PROVISIONING" state. This count also includes capacity
   * delivered as part of existing matching reservations.
   *
   * @param string $fulfilledCount
   */
  public function setFulfilledCount($fulfilledCount)
  {
    $this->fulfilledCount = $fulfilledCount;
  }
  /**
   * @return string
   */
  public function getFulfilledCount()
  {
    return $this->fulfilledCount;
  }
  /**
   * Output only. [Output Only] This field represents the future reservation
   * before an amendment was requested. If the amendment is declined, the Future
   * Reservation will be reverted to the last known good state. The last known
   * good state is not set when updating a future reservation whose Procurement
   * Status is DRAFTING.
   *
   * @param FutureReservationStatusLastKnownGoodState $lastKnownGoodState
   */
  public function setLastKnownGoodState(FutureReservationStatusLastKnownGoodState $lastKnownGoodState)
  {
    $this->lastKnownGoodState = $lastKnownGoodState;
  }
  /**
   * @return FutureReservationStatusLastKnownGoodState
   */
  public function getLastKnownGoodState()
  {
    return $this->lastKnownGoodState;
  }
  /**
   * Output only. Time when Future Reservation would become LOCKED, after which
   * no modifications to Future Reservation will be allowed. Applicable only
   * after the Future Reservation is in the APPROVED state. The lock_time is an
   * RFC3339 string. The procurement_status will transition to PROCURING state
   * at this time.
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
   * Output only. Current state of this Future Reservation
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
  /**
   * @param FutureReservationStatusSpecificSKUProperties $specificSkuProperties
   */
  public function setSpecificSkuProperties(FutureReservationStatusSpecificSKUProperties $specificSkuProperties)
  {
    $this->specificSkuProperties = $specificSkuProperties;
  }
  /**
   * @return FutureReservationStatusSpecificSKUProperties
   */
  public function getSpecificSkuProperties()
  {
    return $this->specificSkuProperties;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FutureReservationStatus::class, 'Google_Service_Compute_FutureReservationStatus');
