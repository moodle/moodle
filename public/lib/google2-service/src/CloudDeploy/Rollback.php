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

class Rollback extends \Google\Model
{
  /**
   * Optional. The starting phase ID for the `Rollout`. If unspecified, the
   * `Rollout` will start in the stable phase.
   *
   * @var string
   */
  public $destinationPhase;
  /**
   * Optional. If pending rollout exists on the target, the rollback operation
   * will be aborted.
   *
   * @var bool
   */
  public $disableRollbackIfRolloutPending;

  /**
   * Optional. The starting phase ID for the `Rollout`. If unspecified, the
   * `Rollout` will start in the stable phase.
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
   * Optional. If pending rollout exists on the target, the rollback operation
   * will be aborted.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Rollback::class, 'Google_Service_CloudDeploy_Rollback');
