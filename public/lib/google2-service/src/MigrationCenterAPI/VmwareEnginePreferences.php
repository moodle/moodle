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

namespace Google\Service\MigrationCenterAPI;

class VmwareEnginePreferences extends \Google\Model
{
  /**
   * Unspecified commitment plan.
   */
  public const COMMITMENT_PLAN_COMMITMENT_PLAN_UNSPECIFIED = 'COMMITMENT_PLAN_UNSPECIFIED';
  /**
   * No commitment plan (on-demand usage).
   */
  public const COMMITMENT_PLAN_ON_DEMAND = 'ON_DEMAND';
  /**
   * 1 year commitment (monthly payments).
   */
  public const COMMITMENT_PLAN_COMMITMENT_1_YEAR_MONTHLY_PAYMENTS = 'COMMITMENT_1_YEAR_MONTHLY_PAYMENTS';
  /**
   * 3 year commitment (monthly payments).
   */
  public const COMMITMENT_PLAN_COMMITMENT_3_YEAR_MONTHLY_PAYMENTS = 'COMMITMENT_3_YEAR_MONTHLY_PAYMENTS';
  /**
   * 1 year commitment (upfront payment).
   */
  public const COMMITMENT_PLAN_COMMITMENT_1_YEAR_UPFRONT_PAYMENT = 'COMMITMENT_1_YEAR_UPFRONT_PAYMENT';
  /**
   * 3 years commitment (upfront payment).
   */
  public const COMMITMENT_PLAN_COMMITMENT_3_YEAR_UPFRONT_PAYMENT = 'COMMITMENT_3_YEAR_UPFRONT_PAYMENT';
  /**
   * Commitment plan to consider when calculating costs for virtual machine
   * insights and recommendations. If you are unsure which value to set, a 3
   * year commitment plan is often a good value to start with.
   *
   * @var string
   */
  public $commitmentPlan;
  /**
   * CPU overcommit ratio. Acceptable values are between 1.0 and 8.0, with 0.1
   * increment.
   *
   * @var 
   */
  public $cpuOvercommitRatio;
  /**
   * Memory overcommit ratio. Acceptable values are 1.0, 1.25, 1.5, 1.75 and
   * 2.0.
   *
   * @var 
   */
  public $memoryOvercommitRatio;
  /**
   * The Deduplication and Compression ratio is based on the logical (Used
   * Before) space required to store data before applying deduplication and
   * compression, in relation to the physical (Used After) space required after
   * applying deduplication and compression. Specifically, the ratio is the Used
   * Before space divided by the Used After space. For example, if the Used
   * Before space is 3 GB, but the physical Used After space is 1 GB, the
   * deduplication and compression ratio is 3x. Acceptable values are between
   * 1.0 and 4.0.
   *
   * @var 
   */
  public $storageDeduplicationCompressionRatio;

  /**
   * Commitment plan to consider when calculating costs for virtual machine
   * insights and recommendations. If you are unsure which value to set, a 3
   * year commitment plan is often a good value to start with.
   *
   * Accepted values: COMMITMENT_PLAN_UNSPECIFIED, ON_DEMAND,
   * COMMITMENT_1_YEAR_MONTHLY_PAYMENTS, COMMITMENT_3_YEAR_MONTHLY_PAYMENTS,
   * COMMITMENT_1_YEAR_UPFRONT_PAYMENT, COMMITMENT_3_YEAR_UPFRONT_PAYMENT
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
  public function setCpuOvercommitRatio($cpuOvercommitRatio)
  {
    $this->cpuOvercommitRatio = $cpuOvercommitRatio;
  }
  public function getCpuOvercommitRatio()
  {
    return $this->cpuOvercommitRatio;
  }
  public function setMemoryOvercommitRatio($memoryOvercommitRatio)
  {
    $this->memoryOvercommitRatio = $memoryOvercommitRatio;
  }
  public function getMemoryOvercommitRatio()
  {
    return $this->memoryOvercommitRatio;
  }
  public function setStorageDeduplicationCompressionRatio($storageDeduplicationCompressionRatio)
  {
    $this->storageDeduplicationCompressionRatio = $storageDeduplicationCompressionRatio;
  }
  public function getStorageDeduplicationCompressionRatio()
  {
    return $this->storageDeduplicationCompressionRatio;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VmwareEnginePreferences::class, 'Google_Service_MigrationCenterAPI_VmwareEnginePreferences');
