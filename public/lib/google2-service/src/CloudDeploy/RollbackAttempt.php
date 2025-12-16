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

namespace Google\Service\CloudDeploy;

class RollbackAttempt extends \Google\Model
{
  /**
   * The `repair` has an unspecified state.
   */
  public const STATE_REPAIR_STATE_UNSPECIFIED = 'REPAIR_STATE_UNSPECIFIED';
  /**
   * The `repair` action has succeeded.
   */
  public const STATE_REPAIR_STATE_SUCCEEDED = 'REPAIR_STATE_SUCCEEDED';
  /**
   * The `repair` action was cancelled.
   */
  public const STATE_REPAIR_STATE_CANCELLED = 'REPAIR_STATE_CANCELLED';
  /**
   * The `repair` action has failed.
   */
  public const STATE_REPAIR_STATE_FAILED = 'REPAIR_STATE_FAILED';
  /**
   * The `repair` action is in progress.
   */
  public const STATE_REPAIR_STATE_IN_PROGRESS = 'REPAIR_STATE_IN_PROGRESS';
  /**
   * The `repair` action is pending.
   */
  public const STATE_REPAIR_STATE_PENDING = 'REPAIR_STATE_PENDING';
  /**
   * The `repair` action was aborted.
   */
  public const STATE_REPAIR_STATE_ABORTED = 'REPAIR_STATE_ABORTED';
  /**
   * Output only. The phase to which the rollout will be rolled back to.
   *
   * @var string
   */
  public $destinationPhase;
  /**
   * Output only. If active rollout exists on the target, abort this rollback.
   *
   * @var bool
   */
  public $disableRollbackIfRolloutPending;
  /**
   * Output only. ID of the rollback `Rollout` to create.
   *
   * @var string
   */
  public $rolloutId;
  /**
   * Output only. Valid state of this rollback action.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Description of the state of the Rollback.
   *
   * @var string
   */
  public $stateDesc;

  /**
   * Output only. The phase to which the rollout will be rolled back to.
   *
   * @param string $destinationPhase
   */
  public function setDestinationPhase($destinationPhase)
  {
    $this->destinationPhase = $destinationPhase;
  }
  /**
   * @return string
   */
  public function getDestinationPhase()
  {
    return $this->destinationPhase;
  }
  /**
   * Output only. If active rollout exists on the target, abort this rollback.
   *
   * @param bool $disableRollbackIfRolloutPending
   */
  public function setDisableRollbackIfRolloutPending($disableRollbackIfRolloutPending)
  {
    $this->disableRollbackIfRolloutPending = $disableRollbackIfRolloutPending;
  }
  /**
   * @return bool
   */
  public function getDisableRollbackIfRolloutPending()
  {
    return $this->disableRollbackIfRolloutPending;
  }
  /**
   * Output only. ID of the rollback `Rollout` to create.
   *
   * @param string $rolloutId
   */
  public function setRolloutId($rolloutId)
  {
    $this->rolloutId = $rolloutId;
  }
  /**
   * @return string
   */
  public function getRolloutId()
  {
    return $this->rolloutId;
  }
  /**
   * Output only. Valid state of this rollback action.
   *
   * Accepted values: REPAIR_STATE_UNSPECIFIED, REPAIR_STATE_SUCCEEDED,
   * REPAIR_STATE_CANCELLED, REPAIR_STATE_FAILED, REPAIR_STATE_IN_PROGRESS,
   * REPAIR_STATE_PENDING, REPAIR_STATE_ABORTED
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
  /**
   * Output only. Description of the state of the Rollback.
   *
   * @param string $stateDesc
   */
  public function setStateDesc($stateDesc)
  {
    $this->stateDesc = $stateDesc;
  }
  /**
   * @return string
   */
  public function getStateDesc()
  {
    return $this->stateDesc;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RollbackAttempt::class, 'Google_Service_CloudDeploy_RollbackAttempt');
