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

namespace Google\Service\BigQueryReservation;

class Reservation extends \Google\Model
{
  /**
   * Default value, which will be treated as ENTERPRISE.
   */
  public const EDITION_EDITION_UNSPECIFIED = 'EDITION_UNSPECIFIED';
  /**
   * Standard edition.
   */
  public const EDITION_STANDARD = 'STANDARD';
  /**
   * Enterprise edition.
   */
  public const EDITION_ENTERPRISE = 'ENTERPRISE';
  /**
   * Enterprise Plus edition.
   */
  public const EDITION_ENTERPRISE_PLUS = 'ENTERPRISE_PLUS';
  /**
   * Default value of ScalingMode.
   */
  public const SCALING_MODE_SCALING_MODE_UNSPECIFIED = 'SCALING_MODE_UNSPECIFIED';
  /**
   * The reservation will scale up only using slots from autoscaling. It will
   * not use any idle slots even if there may be some available. The upper limit
   * that autoscaling can scale up to will be max_slots - baseline. For example,
   * if max_slots is 1000, baseline is 200 and customer sets ScalingMode to
   * AUTOSCALE_ONLY, then autoscalerg will scale up to 800 slots and no idle
   * slots will be used. Please note, in this mode, the ignore_idle_slots field
   * must be set to true. Otherwise the request will be rejected with error code
   * `google.rpc.Code.INVALID_ARGUMENT`.
   */
  public const SCALING_MODE_AUTOSCALE_ONLY = 'AUTOSCALE_ONLY';
  /**
   * The reservation will scale up using only idle slots contributed by other
   * reservations or from unassigned commitments. If no idle slots are available
   * it will not scale up further. If the idle slots which it is using are
   * reclaimed by the contributing reservation(s) it may be forced to scale
   * down. The max idle slots the reservation can be max_slots - baseline
   * capacity. For example, if max_slots is 1000, baseline is 200 and customer
   * sets ScalingMode to IDLE_SLOTS_ONLY, 1. if there are 1000 idle slots
   * available in other reservations, the reservation will scale up to 1000
   * slots with 200 baseline and 800 idle slots. 2. if there are 500 idle slots
   * available in other reservations, the reservation will scale up to 700 slots
   * with 200 baseline and 300 idle slots. Please note, in this mode, the
   * reservation might not be able to scale up to max_slots. Please note, in
   * this mode, the ignore_idle_slots field must be set to false. Otherwise the
   * request will be rejected with error code
   * `google.rpc.Code.INVALID_ARGUMENT`.
   */
  public const SCALING_MODE_IDLE_SLOTS_ONLY = 'IDLE_SLOTS_ONLY';
  /**
   * The reservation will scale up using all slots available to it. It will use
   * idle slots contributed by other reservations or from unassigned commitments
   * first. If no idle slots are available it will scale up using autoscaling.
   * For example, if max_slots is 1000, baseline is 200 and customer sets
   * ScalingMode to ALL_SLOTS, 1. if there are 800 idle slots available in other
   * reservations, the reservation will scale up to 1000 slots with 200 baseline
   * and 800 idle slots. 2. if there are 500 idle slots available in other
   * reservations, the reservation will scale up to 1000 slots with 200
   * baseline, 500 idle slots and 300 autoscaling slots. 3. if there are no idle
   * slots available in other reservations, it will scale up to 1000 slots with
   * 200 baseline and 800 autoscaling slots. Please note, in this mode, the
   * ignore_idle_slots field must be set to false. Otherwise the request will be
   * rejected with error code `google.rpc.Code.INVALID_ARGUMENT`.
   */
  public const SCALING_MODE_ALL_SLOTS = 'ALL_SLOTS';
  protected $autoscaleType = Autoscale::class;
  protected $autoscaleDataType = '';
  /**
   * Optional. Job concurrency target which sets a soft upper bound on the
   * number of jobs that can run concurrently in this reservation. This is a
   * soft target due to asynchronous nature of the system and various
   * optimizations for small queries. Default value is 0 which means that
   * concurrency target will be automatically computed by the system. NOTE: this
   * field is exposed as target job concurrency in the Information Schema, DDL
   * and BigQuery CLI.
   *
   * @var string
   */
  public $concurrency;
  /**
   * Output only. Creation time of the reservation.
   *
   * @var string
   */
  public $creationTime;
  /**
   * Optional. Edition of the reservation.
   *
   * @var string
   */
  public $edition;
  /**
   * Optional. If false, any query or pipeline job using this reservation will
   * use idle slots from other reservations within the same admin project. If
   * true, a query or pipeline job using this reservation will execute with the
   * slot capacity specified in the slot_capacity field at most.
   *
   * @var bool
   */
  public $ignoreIdleSlots;
  /**
   * Optional. The labels associated with this reservation. You can use these to
   * organize and group your reservations. You can set this property when you
   * create or update a reservation.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Optional. The overall max slots for the reservation, covering slot_capacity
   * (baseline), idle slots (if ignore_idle_slots is false) and scaled slots. If
   * present, the reservation won't use more than the specified number of slots,
   * even if there is demand and supply (from idle slots). NOTE: capping a
   * reservation's idle slot usage is best effort and its usage may exceed the
   * max_slots value. However, in terms of autoscale.current_slots (which
   * accounts for the additional added slots), it will never exceed the
   * max_slots - baseline. This field must be set together with the scaling_mode
   * enum value, otherwise the request will be rejected with error code
   * `google.rpc.Code.INVALID_ARGUMENT`. If the max_slots and scaling_mode are
   * set, the autoscale or autoscale.max_slots field must be unset. Otherwise
   * the request will be rejected with error code
   * `google.rpc.Code.INVALID_ARGUMENT`. However, the autoscale field may still
   * be in the output. The autopscale.max_slots will always show as 0 and the
   * autoscaler.current_slots will represent the current slots from autoscaler
   * excluding idle slots. For example, if the max_slots is 1000 and
   * scaling_mode is AUTOSCALE_ONLY, then in the output, the
   * autoscaler.max_slots will be 0 and the autoscaler.current_slots may be any
   * value between 0 and 1000. If the max_slots is 1000, scaling_mode is
   * ALL_SLOTS, the baseline is 100 and idle slots usage is 200, then in the
   * output, the autoscaler.max_slots will be 0 and the autoscaler.current_slots
   * will not be higher than 700. If the max_slots is 1000, scaling_mode is
   * IDLE_SLOTS_ONLY, then in the output, the autoscaler field will be null. If
   * the max_slots and scaling_mode are set, then the ignore_idle_slots field
   * must be aligned with the scaling_mode enum value.(See details in
   * ScalingMode comments). Otherwise the request will be rejected with error
   * code `google.rpc.Code.INVALID_ARGUMENT`. Please note, the max_slots is for
   * user to manage the part of slots greater than the baseline. Therefore, we
   * don't allow users to set max_slots smaller or equal to the baseline as it
   * will not be meaningful. If the field is present and
   * slot_capacity>=max_slots, requests will be rejected with error code
   * `google.rpc.Code.INVALID_ARGUMENT`. Please note that if max_slots is set to
   * 0, we will treat it as unset. Customers can set max_slots to 0 and set
   * scaling_mode to SCALING_MODE_UNSPECIFIED to disable the max_slots feature.
   *
   * @var string
   */
  public $maxSlots;
  /**
   * Applicable only for reservations located within one of the BigQuery multi-
   * regions (US or EU). If set to true, this reservation is placed in the
   * organization's secondary region which is designated for disaster recovery
   * purposes. If false, this reservation is placed in the organization's
   * default region. NOTE: this is a preview feature. Project must be allow-
   * listed in order to set this field.
   *
   * @deprecated
   * @var bool
   */
  public $multiRegionAuxiliary;
  /**
   * Identifier. The resource name of the reservation, e.g.,
   * `projects/locations/reservations/team1-prod`. The reservation_id must only
   * contain lower case alphanumeric characters or dashes. It must start with a
   * letter and must not end with a dash. Its maximum length is 64 characters.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The location where the reservation was originally created.
   * This is set only during the failover reservation's creation. All billing
   * charges for the failover reservation will be applied to this location.
   *
   * @var string
   */
  public $originalPrimaryLocation;
  /**
   * Output only. The current location of the reservation's primary replica.
   * This field is only set for reservations using the managed disaster recovery
   * feature.
   *
   * @var string
   */
  public $primaryLocation;
  protected $replicationStatusType = ReplicationStatus::class;
  protected $replicationStatusDataType = '';
  /**
   * Optional. The reservation group that this reservation belongs to. You can
   * set this property when you create or update a reservation. Reservations do
   * not need to belong to a reservation group. Format: projects/{project}/locat
   * ions/{location}/reservationGroups/{reservation_group} or just
   * {reservation_group}
   *
   * @var string
   */
  public $reservationGroup;
  /**
   * Optional. The scaling mode for the reservation. If the field is present but
   * max_slots is not present, requests will be rejected with error code
   * `google.rpc.Code.INVALID_ARGUMENT`.
   *
   * @var string
   */
  public $scalingMode;
  protected $schedulingPolicyType = SchedulingPolicy::class;
  protected $schedulingPolicyDataType = '';
  /**
   * Optional. The current location of the reservation's secondary replica. This
   * field is only set for reservations using the managed disaster recovery
   * feature. Users can set this in create reservation calls to create a
   * failover reservation or in update reservation calls to convert a non-
   * failover reservation to a failover reservation(or vice versa).
   *
   * @var string
   */
  public $secondaryLocation;
  /**
   * Optional. Baseline slots available to this reservation. A slot is a unit of
   * computational power in BigQuery, and serves as the unit of parallelism.
   * Queries using this reservation might use more slots during runtime if
   * ignore_idle_slots is set to false, or autoscaling is enabled. The total
   * slot_capacity of the reservation and its siblings may exceed the total
   * slot_count of capacity commitments. In that case, the exceeding slots will
   * be charged with the autoscale SKU. You can increase the number of baseline
   * slots in a reservation every few minutes. If you want to decrease your
   * baseline slots, you are limited to once an hour if you have recently
   * changed your baseline slot capacity and your baseline slots exceed your
   * committed slots. Otherwise, you can decrease your baseline slots every few
   * minutes.
   *
   * @var string
   */
  public $slotCapacity;
  /**
   * Output only. Last update time of the reservation.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. The configuration parameters for the auto scaling feature.
   *
   * @param Autoscale $autoscale
   */
  public function setAutoscale(Autoscale $autoscale)
  {
    $this->autoscale = $autoscale;
  }
  /**
   * @return Autoscale
   */
  public function getAutoscale()
  {
    return $this->autoscale;
  }
  /**
   * Optional. Job concurrency target which sets a soft upper bound on the
   * number of jobs that can run concurrently in this reservation. This is a
   * soft target due to asynchronous nature of the system and various
   * optimizations for small queries. Default value is 0 which means that
   * concurrency target will be automatically computed by the system. NOTE: this
   * field is exposed as target job concurrency in the Information Schema, DDL
   * and BigQuery CLI.
   *
   * @param string $concurrency
   */
  public function setConcurrency($concurrency)
  {
    $this->concurrency = $concurrency;
  }
  /**
   * @return string
   */
  public function getConcurrency()
  {
    return $this->concurrency;
  }
  /**
   * Output only. Creation time of the reservation.
   *
   * @param string $creationTime
   */
  public function setCreationTime($creationTime)
  {
    $this->creationTime = $creationTime;
  }
  /**
   * @return string
   */
  public function getCreationTime()
  {
    return $this->creationTime;
  }
  /**
   * Optional. Edition of the reservation.
   *
   * Accepted values: EDITION_UNSPECIFIED, STANDARD, ENTERPRISE, ENTERPRISE_PLUS
   *
   * @param self::EDITION_* $edition
   */
  public function setEdition($edition)
  {
    $this->edition = $edition;
  }
  /**
   * @return self::EDITION_*
   */
  public function getEdition()
  {
    return $this->edition;
  }
  /**
   * Optional. If false, any query or pipeline job using this reservation will
   * use idle slots from other reservations within the same admin project. If
   * true, a query or pipeline job using this reservation will execute with the
   * slot capacity specified in the slot_capacity field at most.
   *
   * @param bool $ignoreIdleSlots
   */
  public function setIgnoreIdleSlots($ignoreIdleSlots)
  {
    $this->ignoreIdleSlots = $ignoreIdleSlots;
  }
  /**
   * @return bool
   */
  public function getIgnoreIdleSlots()
  {
    return $this->ignoreIdleSlots;
  }
  /**
   * Optional. The labels associated with this reservation. You can use these to
   * organize and group your reservations. You can set this property when you
   * create or update a reservation.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Optional. The overall max slots for the reservation, covering slot_capacity
   * (baseline), idle slots (if ignore_idle_slots is false) and scaled slots. If
   * present, the reservation won't use more than the specified number of slots,
   * even if there is demand and supply (from idle slots). NOTE: capping a
   * reservation's idle slot usage is best effort and its usage may exceed the
   * max_slots value. However, in terms of autoscale.current_slots (which
   * accounts for the additional added slots), it will never exceed the
   * max_slots - baseline. This field must be set together with the scaling_mode
   * enum value, otherwise the request will be rejected with error code
   * `google.rpc.Code.INVALID_ARGUMENT`. If the max_slots and scaling_mode are
   * set, the autoscale or autoscale.max_slots field must be unset. Otherwise
   * the request will be rejected with error code
   * `google.rpc.Code.INVALID_ARGUMENT`. However, the autoscale field may still
   * be in the output. The autopscale.max_slots will always show as 0 and the
   * autoscaler.current_slots will represent the current slots from autoscaler
   * excluding idle slots. For example, if the max_slots is 1000 and
   * scaling_mode is AUTOSCALE_ONLY, then in the output, the
   * autoscaler.max_slots will be 0 and the autoscaler.current_slots may be any
   * value between 0 and 1000. If the max_slots is 1000, scaling_mode is
   * ALL_SLOTS, the baseline is 100 and idle slots usage is 200, then in the
   * output, the autoscaler.max_slots will be 0 and the autoscaler.current_slots
   * will not be higher than 700. If the max_slots is 1000, scaling_mode is
   * IDLE_SLOTS_ONLY, then in the output, the autoscaler field will be null. If
   * the max_slots and scaling_mode are set, then the ignore_idle_slots field
   * must be aligned with the scaling_mode enum value.(See details in
   * ScalingMode comments). Otherwise the request will be rejected with error
   * code `google.rpc.Code.INVALID_ARGUMENT`. Please note, the max_slots is for
   * user to manage the part of slots greater than the baseline. Therefore, we
   * don't allow users to set max_slots smaller or equal to the baseline as it
   * will not be meaningful. If the field is present and
   * slot_capacity>=max_slots, requests will be rejected with error code
   * `google.rpc.Code.INVALID_ARGUMENT`. Please note that if max_slots is set to
   * 0, we will treat it as unset. Customers can set max_slots to 0 and set
   * scaling_mode to SCALING_MODE_UNSPECIFIED to disable the max_slots feature.
   *
   * @param string $maxSlots
   */
  public function setMaxSlots($maxSlots)
  {
    $this->maxSlots = $maxSlots;
  }
  /**
   * @return string
   */
  public function getMaxSlots()
  {
    return $this->maxSlots;
  }
  /**
   * Applicable only for reservations located within one of the BigQuery multi-
   * regions (US or EU). If set to true, this reservation is placed in the
   * organization's secondary region which is designated for disaster recovery
   * purposes. If false, this reservation is placed in the organization's
   * default region. NOTE: this is a preview feature. Project must be allow-
   * listed in order to set this field.
   *
   * @deprecated
   * @param bool $multiRegionAuxiliary
   */
  public function setMultiRegionAuxiliary($multiRegionAuxiliary)
  {
    $this->multiRegionAuxiliary = $multiRegionAuxiliary;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getMultiRegionAuxiliary()
  {
    return $this->multiRegionAuxiliary;
  }
  /**
   * Identifier. The resource name of the reservation, e.g.,
   * `projects/locations/reservations/team1-prod`. The reservation_id must only
   * contain lower case alphanumeric characters or dashes. It must start with a
   * letter and must not end with a dash. Its maximum length is 64 characters.
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
   * Output only. The location where the reservation was originally created.
   * This is set only during the failover reservation's creation. All billing
   * charges for the failover reservation will be applied to this location.
   *
   * @param string $originalPrimaryLocation
   */
  public function setOriginalPrimaryLocation($originalPrimaryLocation)
  {
    $this->originalPrimaryLocation = $originalPrimaryLocation;
  }
  /**
   * @return string
   */
  public function getOriginalPrimaryLocation()
  {
    return $this->originalPrimaryLocation;
  }
  /**
   * Output only. The current location of the reservation's primary replica.
   * This field is only set for reservations using the managed disaster recovery
   * feature.
   *
   * @param string $primaryLocation
   */
  public function setPrimaryLocation($primaryLocation)
  {
    $this->primaryLocation = $primaryLocation;
  }
  /**
   * @return string
   */
  public function getPrimaryLocation()
  {
    return $this->primaryLocation;
  }
  /**
   * Output only. The Disaster Recovery(DR) replication status of the
   * reservation. This is only available for the primary replicas of DR/failover
   * reservations and provides information about the both the staleness of the
   * secondary and the last error encountered while trying to replicate changes
   * from the primary to the secondary. If this field is blank, it means that
   * the reservation is either not a DR reservation or the reservation is a DR
   * secondary or that any replication operations on the reservation have
   * succeeded.
   *
   * @param ReplicationStatus $replicationStatus
   */
  public function setReplicationStatus(ReplicationStatus $replicationStatus)
  {
    $this->replicationStatus = $replicationStatus;
  }
  /**
   * @return ReplicationStatus
   */
  public function getReplicationStatus()
  {
    return $this->replicationStatus;
  }
  /**
   * Optional. The reservation group that this reservation belongs to. You can
   * set this property when you create or update a reservation. Reservations do
   * not need to belong to a reservation group. Format: projects/{project}/locat
   * ions/{location}/reservationGroups/{reservation_group} or just
   * {reservation_group}
   *
   * @param string $reservationGroup
   */
  public function setReservationGroup($reservationGroup)
  {
    $this->reservationGroup = $reservationGroup;
  }
  /**
   * @return string
   */
  public function getReservationGroup()
  {
    return $this->reservationGroup;
  }
  /**
   * Optional. The scaling mode for the reservation. If the field is present but
   * max_slots is not present, requests will be rejected with error code
   * `google.rpc.Code.INVALID_ARGUMENT`.
   *
   * Accepted values: SCALING_MODE_UNSPECIFIED, AUTOSCALE_ONLY, IDLE_SLOTS_ONLY,
   * ALL_SLOTS
   *
   * @param self::SCALING_MODE_* $scalingMode
   */
  public function setScalingMode($scalingMode)
  {
    $this->scalingMode = $scalingMode;
  }
  /**
   * @return self::SCALING_MODE_*
   */
  public function getScalingMode()
  {
    return $this->scalingMode;
  }
  /**
   * Optional. The scheduling policy to use for jobs and queries running under
   * this reservation. The scheduling policy controls how the reservation's
   * resources are distributed. This feature is not yet generally available.
   *
   * @param SchedulingPolicy $schedulingPolicy
   */
  public function setSchedulingPolicy(SchedulingPolicy $schedulingPolicy)
  {
    $this->schedulingPolicy = $schedulingPolicy;
  }
  /**
   * @return SchedulingPolicy
   */
  public function getSchedulingPolicy()
  {
    return $this->schedulingPolicy;
  }
  /**
   * Optional. The current location of the reservation's secondary replica. This
   * field is only set for reservations using the managed disaster recovery
   * feature. Users can set this in create reservation calls to create a
   * failover reservation or in update reservation calls to convert a non-
   * failover reservation to a failover reservation(or vice versa).
   *
   * @param string $secondaryLocation
   */
  public function setSecondaryLocation($secondaryLocation)
  {
    $this->secondaryLocation = $secondaryLocation;
  }
  /**
   * @return string
   */
  public function getSecondaryLocation()
  {
    return $this->secondaryLocation;
  }
  /**
   * Optional. Baseline slots available to this reservation. A slot is a unit of
   * computational power in BigQuery, and serves as the unit of parallelism.
   * Queries using this reservation might use more slots during runtime if
   * ignore_idle_slots is set to false, or autoscaling is enabled. The total
   * slot_capacity of the reservation and its siblings may exceed the total
   * slot_count of capacity commitments. In that case, the exceeding slots will
   * be charged with the autoscale SKU. You can increase the number of baseline
   * slots in a reservation every few minutes. If you want to decrease your
   * baseline slots, you are limited to once an hour if you have recently
   * changed your baseline slot capacity and your baseline slots exceed your
   * committed slots. Otherwise, you can decrease your baseline slots every few
   * minutes.
   *
   * @param string $slotCapacity
   */
  public function setSlotCapacity($slotCapacity)
  {
    $this->slotCapacity = $slotCapacity;
  }
  /**
   * @return string
   */
  public function getSlotCapacity()
  {
    return $this->slotCapacity;
  }
  /**
   * Output only. Last update time of the reservation.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Reservation::class, 'Google_Service_BigQueryReservation_Reservation');
