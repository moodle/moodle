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

class FutureReservationCommitmentInfo extends \Google\Model
{
  public const COMMITMENT_PLAN_INVALID = 'INVALID';
  public const COMMITMENT_PLAN_THIRTY_SIX_MONTH = 'THIRTY_SIX_MONTH';
  public const COMMITMENT_PLAN_TWELVE_MONTH = 'TWELVE_MONTH';
  /**
   * All associated parent Committed Used Discount(s) end-date/term will be
   * extended to the end-time of this future reservation. Default is to extend
   * previous commitment(s) time to the end_time of the reservation.
   */
  public const PREVIOUS_COMMITMENT_TERMS_EXTEND = 'EXTEND';
  /**
   * No changes to associated parents Committed Used Discount(s) terms.
   */
  public const PREVIOUS_COMMITMENT_TERMS_PREVIOUSCOMMITMENTTERM_UNSPECIFIED = 'PREVIOUSCOMMITMENTTERM_UNSPECIFIED';
  /**
   * name of the commitment where capacity is being delivered to.
   *
   * @var string
   */
  public $commitmentName;
  /**
   * Indicates if a Commitment needs to be created as part of FR delivery. If
   * this field is not present, then no commitment needs to be created.
   *
   * @var string
   */
  public $commitmentPlan;
  /**
   * Only applicable if FR is delivering to the same reservation. If set, all
   * parent commitments will be extended to match the end date of the plan for
   * this commitment.
   *
   * @var string
   */
  public $previousCommitmentTerms;

  /**
   * name of the commitment where capacity is being delivered to.
   *
   * @param string $commitmentName
   */
  public function setCommitmentName($commitmentName)
  {
    $this->commitmentName = $commitmentName;
  }
  /**
   * @return string
   */
  public function getCommitmentName()
  {
    return $this->commitmentName;
  }
  /**
   * Indicates if a Commitment needs to be created as part of FR delivery. If
   * this field is not present, then no commitment needs to be created.
   *
   * Accepted values: INVALID, THIRTY_SIX_MONTH, TWELVE_MONTH
   *
   * @param self::COMMITMENT_PLAN_* $commitmentPlan
   */
  public function setCommitmentPlan($commitmentPlan)
  {
    $this->commitmentPlan = $commitmentPlan;
  }
  /**
   * @return self::COMMITMENT_PLAN_*
   */
  public function getCommitmentPlan()
  {
    return $this->commitmentPlan;
  }
  /**
   * Only applicable if FR is delivering to the same reservation. If set, all
   * parent commitments will be extended to match the end date of the plan for
   * this commitment.
   *
   * Accepted values: EXTEND, PREVIOUSCOMMITMENTTERM_UNSPECIFIED
   *
   * @param self::PREVIOUS_COMMITMENT_TERMS_* $previousCommitmentTerms
   */
  public function setPreviousCommitmentTerms($previousCommitmentTerms)
  {
    $this->previousCommitmentTerms = $previousCommitmentTerms;
  }
  /**
   * @return self::PREVIOUS_COMMITMENT_TERMS_*
   */
  public function getPreviousCommitmentTerms()
  {
    return $this->previousCommitmentTerms;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FutureReservationCommitmentInfo::class, 'Google_Service_Compute_FutureReservationCommitmentInfo');
