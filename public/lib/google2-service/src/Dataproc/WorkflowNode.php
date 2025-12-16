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

namespace Google\Service\Dataproc;

class WorkflowNode extends \Google\Collection
{
  /**
   * State is unspecified.
   */
  public const STATE_NODE_STATE_UNSPECIFIED = 'NODE_STATE_UNSPECIFIED';
  /**
   * The node is awaiting prerequisite node to finish.
   */
  public const STATE_BLOCKED = 'BLOCKED';
  /**
   * The node is runnable but not running.
   */
  public const STATE_RUNNABLE = 'RUNNABLE';
  /**
   * The node is running.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The node completed successfully.
   */
  public const STATE_COMPLETED = 'COMPLETED';
  /**
   * The node failed. A node can be marked FAILED because its ancestor or peer
   * failed.
   */
  public const STATE_FAILED = 'FAILED';
  protected $collection_key = 'prerequisiteStepIds';
  /**
   * Output only. The error detail.
   *
   * @var string
   */
  public $error;
  /**
   * Output only. The job id; populated after the node enters RUNNING state.
   *
   * @var string
   */
  public $jobId;
  /**
   * Output only. Node's prerequisite nodes.
   *
   * @var string[]
   */
  public $prerequisiteStepIds;
  /**
   * Output only. The node state.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The name of the node.
   *
   * @var string
   */
  public $stepId;

  /**
   * Output only. The error detail.
   *
   * @param string $error
   */
  public function setError($error)
  {
    $this->error = $error;
  }
  /**
   * @return string
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * Output only. The job id; populated after the node enters RUNNING state.
   *
   * @param string $jobId
   */
  public function setJobId($jobId)
  {
    $this->jobId = $jobId;
  }
  /**
   * @return string
   */
  public function getJobId()
  {
    return $this->jobId;
  }
  /**
   * Output only. Node's prerequisite nodes.
   *
   * @param string[] $prerequisiteStepIds
   */
  public function setPrerequisiteStepIds($prerequisiteStepIds)
  {
    $this->prerequisiteStepIds = $prerequisiteStepIds;
  }
  /**
   * @return string[]
   */
  public function getPrerequisiteStepIds()
  {
    return $this->prerequisiteStepIds;
  }
  /**
   * Output only. The node state.
   *
   * Accepted values: NODE_STATE_UNSPECIFIED, BLOCKED, RUNNABLE, RUNNING,
   * COMPLETED, FAILED
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
   * Output only. The name of the node.
   *
   * @param string $stepId
   */
  public function setStepId($stepId)
  {
    $this->stepId = $stepId;
  }
  /**
   * @return string
   */
  public function getStepId()
  {
    return $this->stepId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WorkflowNode::class, 'Google_Service_Dataproc_WorkflowNode');
