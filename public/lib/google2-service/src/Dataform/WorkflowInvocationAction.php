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

namespace Google\Service\Dataform;

class WorkflowInvocationAction extends \Google\Model
{
  /**
   * The action has not yet been considered for invocation.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * The action is currently running.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * Execution of the action was skipped because upstream dependencies did not
   * all complete successfully. A terminal state.
   */
  public const STATE_SKIPPED = 'SKIPPED';
  /**
   * Execution of the action was disabled as per the configuration of the
   * corresponding compilation result action. A terminal state.
   */
  public const STATE_DISABLED = 'DISABLED';
  /**
   * The action succeeded. A terminal state.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The action was cancelled. A terminal state.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  /**
   * The action failed. A terminal state.
   */
  public const STATE_FAILED = 'FAILED';
  protected $bigqueryActionType = BigQueryAction::class;
  protected $bigqueryActionDataType = '';
  protected $canonicalTargetType = Target::class;
  protected $canonicalTargetDataType = '';
  protected $dataPreparationActionType = DataPreparationAction::class;
  protected $dataPreparationActionDataType = '';
  /**
   * Output only. If and only if action's state is FAILED a failure reason is
   * set.
   *
   * @var string
   */
  public $failureReason;
  /**
   * Output only. All the metadata information that is used internally to serve
   * the resource. For example: timestamps, flags, status fields, etc. The
   * format of this field is a JSON string.
   *
   * @var string
   */
  public $internalMetadata;
  protected $invocationTimingType = Interval::class;
  protected $invocationTimingDataType = '';
  protected $notebookActionType = NotebookAction::class;
  protected $notebookActionDataType = '';
  /**
   * Output only. This action's current state.
   *
   * @var string
   */
  public $state;
  protected $targetType = Target::class;
  protected $targetDataType = '';

  /**
   * Output only. The workflow action's bigquery action details.
   *
   * @param BigQueryAction $bigqueryAction
   */
  public function setBigqueryAction(BigQueryAction $bigqueryAction)
  {
    $this->bigqueryAction = $bigqueryAction;
  }
  /**
   * @return BigQueryAction
   */
  public function getBigqueryAction()
  {
    return $this->bigqueryAction;
  }
  /**
   * Output only. The action's identifier if the project had been compiled
   * without any overrides configured. Unique within the compilation result.
   *
   * @param Target $canonicalTarget
   */
  public function setCanonicalTarget(Target $canonicalTarget)
  {
    $this->canonicalTarget = $canonicalTarget;
  }
  /**
   * @return Target
   */
  public function getCanonicalTarget()
  {
    return $this->canonicalTarget;
  }
  /**
   * Output only. The workflow action's data preparation action details.
   *
   * @param DataPreparationAction $dataPreparationAction
   */
  public function setDataPreparationAction(DataPreparationAction $dataPreparationAction)
  {
    $this->dataPreparationAction = $dataPreparationAction;
  }
  /**
   * @return DataPreparationAction
   */
  public function getDataPreparationAction()
  {
    return $this->dataPreparationAction;
  }
  /**
   * Output only. If and only if action's state is FAILED a failure reason is
   * set.
   *
   * @param string $failureReason
   */
  public function setFailureReason($failureReason)
  {
    $this->failureReason = $failureReason;
  }
  /**
   * @return string
   */
  public function getFailureReason()
  {
    return $this->failureReason;
  }
  /**
   * Output only. All the metadata information that is used internally to serve
   * the resource. For example: timestamps, flags, status fields, etc. The
   * format of this field is a JSON string.
   *
   * @param string $internalMetadata
   */
  public function setInternalMetadata($internalMetadata)
  {
    $this->internalMetadata = $internalMetadata;
  }
  /**
   * @return string
   */
  public function getInternalMetadata()
  {
    return $this->internalMetadata;
  }
  /**
   * Output only. This action's timing details. `start_time` will be set if the
   * action is in [RUNNING, SUCCEEDED, CANCELLED, FAILED] state. `end_time` will
   * be set if the action is in [SUCCEEDED, CANCELLED, FAILED] state.
   *
   * @param Interval $invocationTiming
   */
  public function setInvocationTiming(Interval $invocationTiming)
  {
    $this->invocationTiming = $invocationTiming;
  }
  /**
   * @return Interval
   */
  public function getInvocationTiming()
  {
    return $this->invocationTiming;
  }
  /**
   * Output only. The workflow action's notebook action details.
   *
   * @param NotebookAction $notebookAction
   */
  public function setNotebookAction(NotebookAction $notebookAction)
  {
    $this->notebookAction = $notebookAction;
  }
  /**
   * @return NotebookAction
   */
  public function getNotebookAction()
  {
    return $this->notebookAction;
  }
  /**
   * Output only. This action's current state.
   *
   * Accepted values: PENDING, RUNNING, SKIPPED, DISABLED, SUCCEEDED, CANCELLED,
   * FAILED
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
   * Output only. This action's identifier. Unique within the workflow
   * invocation.
   *
   * @param Target $target
   */
  public function setTarget(Target $target)
  {
    $this->target = $target;
  }
  /**
   * @return Target
   */
  public function getTarget()
  {
    return $this->target;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WorkflowInvocationAction::class, 'Google_Service_Dataform_WorkflowInvocationAction');
