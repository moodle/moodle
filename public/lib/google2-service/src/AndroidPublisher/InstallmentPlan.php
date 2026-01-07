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

namespace Google\Service\AndroidPublisher;

class InstallmentPlan extends \Google\Model
{
  /**
   * Total number of payments the user is initially committed for.
   *
   * @var int
   */
  public $initialCommittedPaymentsCount;
  protected $pendingCancellationType = PendingCancellation::class;
  protected $pendingCancellationDataType = '';
  /**
   * Total number of committed payments remaining to be paid for in this renewal
   * cycle.
   *
   * @var int
   */
  public $remainingCommittedPaymentsCount;
  /**
   * Total number of payments the user will be committed for after each
   * commitment period. Empty means the installment plan will fall back to a
   * normal auto-renew subscription after initial commitment.
   *
   * @var int
   */
  public $subsequentCommittedPaymentsCount;

  /**
   * Total number of payments the user is initially committed for.
   *
   * @param int $initialCommittedPaymentsCount
   */
  public function setInitialCommittedPaymentsCount($initialCommittedPaymentsCount)
  {
    $this->initialCommittedPaymentsCount = $initialCommittedPaymentsCount;
  }
  /**
   * @return int
   */
  public function getInitialCommittedPaymentsCount()
  {
    return $this->initialCommittedPaymentsCount;
  }
  /**
   * If present, this installment plan is pending to be canceled. The
   * cancellation will happen only after the user finished all committed
   * payments.
   *
   * @param PendingCancellation $pendingCancellation
   */
  public function setPendingCancellation(PendingCancellation $pendingCancellation)
  {
    $this->pendingCancellation = $pendingCancellation;
  }
  /**
   * @return PendingCancellation
   */
  public function getPendingCancellation()
  {
    return $this->pendingCancellation;
  }
  /**
   * Total number of committed payments remaining to be paid for in this renewal
   * cycle.
   *
   * @param int $remainingCommittedPaymentsCount
   */
  public function setRemainingCommittedPaymentsCount($remainingCommittedPaymentsCount)
  {
    $this->remainingCommittedPaymentsCount = $remainingCommittedPaymentsCount;
  }
  /**
   * @return int
   */
  public function getRemainingCommittedPaymentsCount()
  {
    return $this->remainingCommittedPaymentsCount;
  }
  /**
   * Total number of payments the user will be committed for after each
   * commitment period. Empty means the installment plan will fall back to a
   * normal auto-renew subscription after initial commitment.
   *
   * @param int $subsequentCommittedPaymentsCount
   */
  public function setSubsequentCommittedPaymentsCount($subsequentCommittedPaymentsCount)
  {
    $this->subsequentCommittedPaymentsCount = $subsequentCommittedPaymentsCount;
  }
  /**
   * @return int
   */
  public function getSubsequentCommittedPaymentsCount()
  {
    return $this->subsequentCommittedPaymentsCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstallmentPlan::class, 'Google_Service_AndroidPublisher_InstallmentPlan');
