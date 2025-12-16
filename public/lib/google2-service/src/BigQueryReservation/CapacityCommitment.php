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

class CapacityCommitment extends \Google\Model
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
   * Invalid plan value. Requests with this value will be rejected with error
   * code `google.rpc.Code.INVALID_ARGUMENT`.
   */
  public const PLAN_COMMITMENT_PLAN_UNSPECIFIED = 'COMMITMENT_PLAN_UNSPECIFIED';
  /**
   * Flex commitments have committed period of 1 minute after becoming ACTIVE.
   * After that, they are not in a committed period anymore and can be removed
   * any time.
   */
  public const PLAN_FLEX = 'FLEX';
  /**
   * Same as FLEX, should only be used if flat-rate commitments are still
   * available.
   *
   * @deprecated
   */
  public const PLAN_FLEX_FLAT_RATE = 'FLEX_FLAT_RATE';
  /**
   * Trial commitments have a committed period of 182 days after becoming
   * ACTIVE. After that, they are converted to a new commitment based on the
   * `renewal_plan`. Default `renewal_plan` for Trial commitment is Flex so that
   * it can be deleted right after committed period ends.
   *
   * @deprecated
   */
  public const PLAN_TRIAL = 'TRIAL';
  /**
   * Monthly commitments have a committed period of 30 days after becoming
   * ACTIVE. After that, they are not in a committed period anymore and can be
   * removed any time.
   */
  public const PLAN_MONTHLY = 'MONTHLY';
  /**
   * Same as MONTHLY, should only be used if flat-rate commitments are still
   * available.
   *
   * @deprecated
   */
  public const PLAN_MONTHLY_FLAT_RATE = 'MONTHLY_FLAT_RATE';
  /**
   * Annual commitments have a committed period of 365 days after becoming
   * ACTIVE. After that they are converted to a new commitment based on the
   * renewal_plan.
   */
  public const PLAN_ANNUAL = 'ANNUAL';
  /**
   * Same as ANNUAL, should only be used if flat-rate commitments are still
   * available.
   *
   * @deprecated
   */
  public const PLAN_ANNUAL_FLAT_RATE = 'ANNUAL_FLAT_RATE';
  /**
   * 3-year commitments have a committed period of 1095(3 * 365) days after
   * becoming ACTIVE. After that they are converted to a new commitment based on
   * the renewal_plan.
   */
  public const PLAN_THREE_YEAR = 'THREE_YEAR';
  /**
   * Should only be used for `renewal_plan` and is only meaningful if edition is
   * specified to values other than EDITION_UNSPECIFIED. Otherwise
   * CreateCapacityCommitmentRequest or UpdateCapacityCommitmentRequest will be
   * rejected with error code `google.rpc.Code.INVALID_ARGUMENT`. If the
   * renewal_plan is NONE, capacity commitment will be removed at the end of its
   * commitment period.
   */
  public const PLAN_NONE = 'NONE';
  /**
   * Invalid plan value. Requests with this value will be rejected with error
   * code `google.rpc.Code.INVALID_ARGUMENT`.
   */
  public const RENEWAL_PLAN_COMMITMENT_PLAN_UNSPECIFIED = 'COMMITMENT_PLAN_UNSPECIFIED';
  /**
   * Flex commitments have committed period of 1 minute after becoming ACTIVE.
   * After that, they are not in a committed period anymore and can be removed
   * any time.
   */
  public const RENEWAL_PLAN_FLEX = 'FLEX';
  /**
   * Same as FLEX, should only be used if flat-rate commitments are still
   * available.
   *
   * @deprecated
   */
  public const RENEWAL_PLAN_FLEX_FLAT_RATE = 'FLEX_FLAT_RATE';
  /**
   * Trial commitments have a committed period of 182 days after becoming
   * ACTIVE. After that, they are converted to a new commitment based on the
   * `renewal_plan`. Default `renewal_plan` for Trial commitment is Flex so that
   * it can be deleted right after committed period ends.
   *
   * @deprecated
   */
  public const RENEWAL_PLAN_TRIAL = 'TRIAL';
  /**
   * Monthly commitments have a committed period of 30 days after becoming
   * ACTIVE. After that, they are not in a committed period anymore and can be
   * removed any time.
   */
  public const RENEWAL_PLAN_MONTHLY = 'MONTHLY';
  /**
   * Same as MONTHLY, should only be used if flat-rate commitments are still
   * available.
   *
   * @deprecated
   */
  public const RENEWAL_PLAN_MONTHLY_FLAT_RATE = 'MONTHLY_FLAT_RATE';
  /**
   * Annual commitments have a committed period of 365 days after becoming
   * ACTIVE. After that they are converted to a new commitment based on the
   * renewal_plan.
   */
  public const RENEWAL_PLAN_ANNUAL = 'ANNUAL';
  /**
   * Same as ANNUAL, should only be used if flat-rate commitments are still
   * available.
   *
   * @deprecated
   */
  public const RENEWAL_PLAN_ANNUAL_FLAT_RATE = 'ANNUAL_FLAT_RATE';
  /**
   * 3-year commitments have a committed period of 1095(3 * 365) days after
   * becoming ACTIVE. After that they are converted to a new commitment based on
   * the renewal_plan.
   */
  public const RENEWAL_PLAN_THREE_YEAR = 'THREE_YEAR';
  /**
   * Should only be used for `renewal_plan` and is only meaningful if edition is
   * specified to values other than EDITION_UNSPECIFIED. Otherwise
   * CreateCapacityCommitmentRequest or UpdateCapacityCommitmentRequest will be
   * rejected with error code `google.rpc.Code.INVALID_ARGUMENT`. If the
   * renewal_plan is NONE, capacity commitment will be removed at the end of its
   * commitment period.
   */
  public const RENEWAL_PLAN_NONE = 'NONE';
  /**
   * Invalid state value.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Capacity commitment is pending provisioning. Pending capacity commitment
   * does not contribute to the project's slot_capacity.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * Once slots are provisioned, capacity commitment becomes active. slot_count
   * is added to the project's slot_capacity.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * Capacity commitment is failed to be activated by the backend.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Output only. The end of the current commitment period. It is applicable
   * only for ACTIVE capacity commitments. Note after renewal,
   * commitment_end_time is the time the renewed commitment expires. So itwould
   * be at a time after commitment_start_time + committed period, because we
   * don't change commitment_start_time ,
   *
   * @var string
   */
  public $commitmentEndTime;
  /**
   * Output only. The start of the current commitment period. It is applicable
   * only for ACTIVE capacity commitments. Note after the commitment is renewed,
   * commitment_start_time won't be changed. It refers to the start time of the
   * original commitment.
   *
   * @var string
   */
  public $commitmentStartTime;
  /**
   * Optional. Edition of the capacity commitment.
   *
   * @var string
   */
  public $edition;
  protected $failureStatusType = Status::class;
  protected $failureStatusDataType = '';
  /**
   * Output only. If true, the commitment is a flat-rate commitment, otherwise,
   * it's an edition commitment.
   *
   * @var bool
   */
  public $isFlatRate;
  /**
   * Applicable only for commitments located within one of the BigQuery multi-
   * regions (US or EU). If set to true, this commitment is placed in the
   * organization's secondary region which is designated for disaster recovery
   * purposes. If false, this commitment is placed in the organization's default
   * region. NOTE: this is a preview feature. Project must be allow-listed in
   * order to set this field.
   *
   * @deprecated
   * @var bool
   */
  public $multiRegionAuxiliary;
  /**
   * Output only. The resource name of the capacity commitment, e.g.,
   * `projects/myproject/locations/US/capacityCommitments/123` The commitment_id
   * must only contain lower case alphanumeric characters or dashes. It must
   * start with a letter and must not end with a dash. Its maximum length is 64
   * characters.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Capacity commitment commitment plan.
   *
   * @var string
   */
  public $plan;
  /**
   * Optional. The plan this capacity commitment is converted to after
   * commitment_end_time passes. Once the plan is changed, committed period is
   * extended according to commitment plan. Only applicable for ANNUAL and TRIAL
   * commitments.
   *
   * @var string
   */
  public $renewalPlan;
  /**
   * Optional. Number of slots in this commitment.
   *
   * @var string
   */
  public $slotCount;
  /**
   * Output only. State of the commitment.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. The end of the current commitment period. It is applicable
   * only for ACTIVE capacity commitments. Note after renewal,
   * commitment_end_time is the time the renewed commitment expires. So itwould
   * be at a time after commitment_start_time + committed period, because we
   * don't change commitment_start_time ,
   *
   * @param string $commitmentEndTime
   */
  public function setCommitmentEndTime($commitmentEndTime)
  {
    $this->commitmentEndTime = $commitmentEndTime;
  }
  /**
   * @return string
   */
  public function getCommitmentEndTime()
  {
    return $this->commitmentEndTime;
  }
  /**
   * Output only. The start of the current commitment period. It is applicable
   * only for ACTIVE capacity commitments. Note after the commitment is renewed,
   * commitment_start_time won't be changed. It refers to the start time of the
   * original commitment.
   *
   * @param string $commitmentStartTime
   */
  public function setCommitmentStartTime($commitmentStartTime)
  {
    $this->commitmentStartTime = $commitmentStartTime;
  }
  /**
   * @return string
   */
  public function getCommitmentStartTime()
  {
    return $this->commitmentStartTime;
  }
  /**
   * Optional. Edition of the capacity commitment.
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
   * Output only. For FAILED commitment plan, provides the reason of failure.
   *
   * @param Status $failureStatus
   */
  public function setFailureStatus(Status $failureStatus)
  {
    $this->failureStatus = $failureStatus;
  }
  /**
   * @return Status
   */
  public function getFailureStatus()
  {
    return $this->failureStatus;
  }
  /**
   * Output only. If true, the commitment is a flat-rate commitment, otherwise,
   * it's an edition commitment.
   *
   * @param bool $isFlatRate
   */
  public function setIsFlatRate($isFlatRate)
  {
    $this->isFlatRate = $isFlatRate;
  }
  /**
   * @return bool
   */
  public function getIsFlatRate()
  {
    return $this->isFlatRate;
  }
  /**
   * Applicable only for commitments located within one of the BigQuery multi-
   * regions (US or EU). If set to true, this commitment is placed in the
   * organization's secondary region which is designated for disaster recovery
   * purposes. If false, this commitment is placed in the organization's default
   * region. NOTE: this is a preview feature. Project must be allow-listed in
   * order to set this field.
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
   * Output only. The resource name of the capacity commitment, e.g.,
   * `projects/myproject/locations/US/capacityCommitments/123` The commitment_id
   * must only contain lower case alphanumeric characters or dashes. It must
   * start with a letter and must not end with a dash. Its maximum length is 64
   * characters.
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
   * Optional. Capacity commitment commitment plan.
   *
   * Accepted values: COMMITMENT_PLAN_UNSPECIFIED, FLEX, FLEX_FLAT_RATE, TRIAL,
   * MONTHLY, MONTHLY_FLAT_RATE, ANNUAL, ANNUAL_FLAT_RATE, THREE_YEAR, NONE
   *
   * @param self::PLAN_* $plan
   */
  public function setPlan($plan)
  {
    $this->plan = $plan;
  }
  /**
   * @return self::PLAN_*
   */
  public function getPlan()
  {
    return $this->plan;
  }
  /**
   * Optional. The plan this capacity commitment is converted to after
   * commitment_end_time passes. Once the plan is changed, committed period is
   * extended according to commitment plan. Only applicable for ANNUAL and TRIAL
   * commitments.
   *
   * Accepted values: COMMITMENT_PLAN_UNSPECIFIED, FLEX, FLEX_FLAT_RATE, TRIAL,
   * MONTHLY, MONTHLY_FLAT_RATE, ANNUAL, ANNUAL_FLAT_RATE, THREE_YEAR, NONE
   *
   * @param self::RENEWAL_PLAN_* $renewalPlan
   */
  public function setRenewalPlan($renewalPlan)
  {
    $this->renewalPlan = $renewalPlan;
  }
  /**
   * @return self::RENEWAL_PLAN_*
   */
  public function getRenewalPlan()
  {
    return $this->renewalPlan;
  }
  /**
   * Optional. Number of slots in this commitment.
   *
   * @param string $slotCount
   */
  public function setSlotCount($slotCount)
  {
    $this->slotCount = $slotCount;
  }
  /**
   * @return string
   */
  public function getSlotCount()
  {
    return $this->slotCount;
  }
  /**
   * Output only. State of the commitment.
   *
   * Accepted values: STATE_UNSPECIFIED, PENDING, ACTIVE, FAILED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CapacityCommitment::class, 'Google_Service_BigQueryReservation_CapacityCommitment');
