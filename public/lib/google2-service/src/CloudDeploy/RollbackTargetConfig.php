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

class RollbackTargetConfig extends \Google\Model
{
  protected $rolloutType = Rollout::class;
  protected $rolloutDataType = '';
  /**
   * Optional. The starting phase ID for the `Rollout`. If unspecified, the
   * `Rollout` will start in the stable phase.
   *
   * @var string
   */
  public $startingPhaseId;

  /**
   * Optional. The rollback `Rollout` to create.
   *
   * @param Rollout $rollout
   */
  public function setRollout(Rollout $rollout)
  {
    $this->rollout = $rollout;
  }
  /**
   * @return Rollout
   */
  public function getRollout()
  {
    return $this->rollout;
  }
  /**
   * Optional. The starting phase ID for the `Rollout`. If unspecified, the
   * `Rollout` will start in the stable phase.
   *
   * @param string $startingPhaseId
   */
  public function setStartingPhaseId($startingPhaseId)
  {
    $this->startingPhaseId = $startingPhaseId;
  }
  /**
   * @return string
   */
  public function getStartingPhaseId()
  {
    return $this->startingPhaseId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RollbackTargetConfig::class, 'Google_Service_CloudDeploy_RollbackTargetConfig');
