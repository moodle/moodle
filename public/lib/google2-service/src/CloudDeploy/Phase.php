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

class Phase extends \Google\Model
{
  /**
   * The Phase has an unspecified state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The Phase is waiting for an earlier Phase(s) to complete.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * The Phase is in progress.
   */
  public const STATE_IN_PROGRESS = 'IN_PROGRESS';
  /**
   * The Phase has succeeded.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The Phase has failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The Phase was aborted.
   */
  public const STATE_ABORTED = 'ABORTED';
  /**
   * The Phase was skipped.
   */
  public const STATE_SKIPPED = 'SKIPPED';
  protected $childRolloutJobsType = ChildRolloutJobs::class;
  protected $childRolloutJobsDataType = '';
  protected $deploymentJobsType = DeploymentJobs::class;
  protected $deploymentJobsDataType = '';
  /**
   * Output only. The ID of the Phase.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. Additional information on why the Phase was skipped, if
   * available.
   *
   * @var string
   */
  public $skipMessage;
  /**
   * Output only. Current state of the Phase.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. ChildRollout job composition.
   *
   * @param ChildRolloutJobs $childRolloutJobs
   */
  public function setChildRolloutJobs(ChildRolloutJobs $childRolloutJobs)
  {
    $this->childRolloutJobs = $childRolloutJobs;
  }
  /**
   * @return ChildRolloutJobs
   */
  public function getChildRolloutJobs()
  {
    return $this->childRolloutJobs;
  }
  /**
   * Output only. Deployment job composition.
   *
   * @param DeploymentJobs $deploymentJobs
   */
  public function setDeploymentJobs(DeploymentJobs $deploymentJobs)
  {
    $this->deploymentJobs = $deploymentJobs;
  }
  /**
   * @return DeploymentJobs
   */
  public function getDeploymentJobs()
  {
    return $this->deploymentJobs;
  }
  /**
   * Output only. The ID of the Phase.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Output only. Additional information on why the Phase was skipped, if
   * available.
   *
   * @param string $skipMessage
   */
  public function setSkipMessage($skipMessage)
  {
    $this->skipMessage = $skipMessage;
  }
  /**
   * @return string
   */
  public function getSkipMessage()
  {
    return $this->skipMessage;
  }
  /**
   * Output only. Current state of the Phase.
   *
   * Accepted values: STATE_UNSPECIFIED, PENDING, IN_PROGRESS, SUCCEEDED,
   * FAILED, ABORTED, SKIPPED
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
class_alias(Phase::class, 'Google_Service_CloudDeploy_Phase');
