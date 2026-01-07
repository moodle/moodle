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

class AdvanceRolloutOperation extends \Google\Model
{
  /**
   * Output only. The phase the rollout will be advanced to.
   *
   * @var string
   */
  public $destinationPhase;
  /**
   * Output only. The name of the rollout that initiates the `AutomationRun`.
   *
   * @var string
   */
  public $rollout;
  /**
   * Output only. The phase of a deployment that initiated the operation.
   *
   * @var string
   */
  public $sourcePhase;
  /**
   * Output only. How long the operation will be paused.
   *
   * @var string
   */
  public $wait;

  /**
   * Output only. The phase the rollout will be advanced to.
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
   * Output only. The name of the rollout that initiates the `AutomationRun`.
   *
   * @param string $rollout
   */
  public function setRollout($rollout)
  {
    $this->rollout = $rollout;
  }
  /**
   * @return string
   */
  public function getRollout()
  {
    return $this->rollout;
  }
  /**
   * Output only. The phase of a deployment that initiated the operation.
   *
   * @param string $sourcePhase
   */
  public function setSourcePhase($sourcePhase)
  {
    $this->sourcePhase = $sourcePhase;
  }
  /**
   * @return string
   */
  public function getSourcePhase()
  {
    return $this->sourcePhase;
  }
  /**
   * Output only. How long the operation will be paused.
   *
   * @param string $wait
   */
  public function setWait($wait)
  {
    $this->wait = $wait;
  }
  /**
   * @return string
   */
  public function getWait()
  {
    return $this->wait;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AdvanceRolloutOperation::class, 'Google_Service_CloudDeploy_AdvanceRolloutOperation');
