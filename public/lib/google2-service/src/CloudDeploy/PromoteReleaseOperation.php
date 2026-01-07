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

class PromoteReleaseOperation extends \Google\Model
{
  /**
   * Output only. The starting phase of the rollout created by this operation.
   *
   * @var string
   */
  public $phase;
  /**
   * Output only. The name of the rollout that initiates the `AutomationRun`.
   *
   * @var string
   */
  public $rollout;
  /**
   * Output only. The ID of the target that represents the promotion stage to
   * which the release will be promoted. The value of this field is the last
   * segment of a target name.
   *
   * @var string
   */
  public $targetId;
  /**
   * Output only. How long the operation will be paused.
   *
   * @var string
   */
  public $wait;

  /**
   * Output only. The starting phase of the rollout created by this operation.
   *
   * @param string $phase
   */
  public function setPhase($phase)
  {
    $this->phase = $phase;
  }
  /**
   * @return string
   */
  public function getPhase()
  {
    return $this->phase;
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
   * Output only. The ID of the target that represents the promotion stage to
   * which the release will be promoted. The value of this field is the last
   * segment of a target name.
   *
   * @param string $targetId
   */
  public function setTargetId($targetId)
  {
    $this->targetId = $targetId;
  }
  /**
   * @return string
   */
  public function getTargetId()
  {
    return $this->targetId;
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
class_alias(PromoteReleaseOperation::class, 'Google_Service_CloudDeploy_PromoteReleaseOperation');
