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

namespace Google\Service\WorkflowExecutions;

class StepEntry extends \Google\Model
{
  /**
   * Invalid state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The step entry is in progress.
   */
  public const STATE_STATE_IN_PROGRESS = 'STATE_IN_PROGRESS';
  /**
   * The step entry finished successfully.
   */
  public const STATE_STATE_SUCCEEDED = 'STATE_SUCCEEDED';
  /**
   * The step entry failed with an error.
   */
  public const STATE_STATE_FAILED = 'STATE_FAILED';
  /**
   * The step entry is cancelled.
   */
  public const STATE_STATE_CANCELLED = 'STATE_CANCELLED';
  /**
   * Invalid step type.
   */
  public const STEP_TYPE_STEP_TYPE_UNSPECIFIED = 'STEP_TYPE_UNSPECIFIED';
  /**
   * The step entry assigns new variable(s).
   */
  public const STEP_TYPE_STEP_ASSIGN = 'STEP_ASSIGN';
  /**
   * The step entry calls a standard library routine.
   */
  public const STEP_TYPE_STEP_STD_LIB_CALL = 'STEP_STD_LIB_CALL';
  /**
   * The step entry calls a connector.
   */
  public const STEP_TYPE_STEP_CONNECTOR_CALL = 'STEP_CONNECTOR_CALL';
  /**
   * The step entry calls a subworklfow.
   */
  public const STEP_TYPE_STEP_SUBWORKFLOW_CALL = 'STEP_SUBWORKFLOW_CALL';
  /**
   * The step entry calls a subworkflow/stdlib.
   */
  public const STEP_TYPE_STEP_CALL = 'STEP_CALL';
  /**
   * The step entry executes a switch-case block.
   */
  public const STEP_TYPE_STEP_SWITCH = 'STEP_SWITCH';
  /**
   * The step entry executes a condition inside a switch.
   */
  public const STEP_TYPE_STEP_CONDITION = 'STEP_CONDITION';
  /**
   * The step entry executes a for loop.
   */
  public const STEP_TYPE_STEP_FOR = 'STEP_FOR';
  /**
   * The step entry executes a iteration of a for loop.
   */
  public const STEP_TYPE_STEP_FOR_ITERATION = 'STEP_FOR_ITERATION';
  /**
   * The step entry executes a parallel for loop.
   */
  public const STEP_TYPE_STEP_PARALLEL_FOR = 'STEP_PARALLEL_FOR';
  /**
   * The step entry executes a series of parallel branch(es).
   */
  public const STEP_TYPE_STEP_PARALLEL_BRANCH = 'STEP_PARALLEL_BRANCH';
  /**
   * The step entry executes a branch of a parallel branch.
   */
  public const STEP_TYPE_STEP_PARALLEL_BRANCH_ENTRY = 'STEP_PARALLEL_BRANCH_ENTRY';
  /**
   * The step entry executes a try/retry/except block.
   */
  public const STEP_TYPE_STEP_TRY_RETRY_EXCEPT = 'STEP_TRY_RETRY_EXCEPT';
  /**
   * The step entry executes the try part of a try/retry/except block.
   */
  public const STEP_TYPE_STEP_TRY = 'STEP_TRY';
  /**
   * The step entry executes the retry part of a try/retry/except block.
   */
  public const STEP_TYPE_STEP_RETRY = 'STEP_RETRY';
  /**
   * The step entry executes the except part of a try/retry/except block.
   */
  public const STEP_TYPE_STEP_EXCEPT = 'STEP_EXCEPT';
  /**
   * The step entry returns.
   */
  public const STEP_TYPE_STEP_RETURN = 'STEP_RETURN';
  /**
   * The step entry raises an error.
   */
  public const STEP_TYPE_STEP_RAISE = 'STEP_RAISE';
  /**
   * The step entry jumps to another step.
   */
  public const STEP_TYPE_STEP_GOTO = 'STEP_GOTO';
  /**
   * Output only. The creation time of the step entry.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The numeric ID of this step entry, used for navigation.
   *
   * @var string
   */
  public $entryId;
  protected $exceptionType = Exception::class;
  protected $exceptionDataType = '';
  /**
   * Output only. The full resource name of the step entry. Each step entry has
   * a unique entry ID, which is a monotonically increasing counter. Step entry
   * names have the format: `projects/{project}/locations/{location}/workflows/{
   * workflow}/executions/{execution}/stepEntries/{step_entry}`.
   *
   * @var string
   */
  public $name;
  protected $navigationInfoType = NavigationInfo::class;
  protected $navigationInfoDataType = '';
  /**
   * Output only. The name of the routine this step entry belongs to. A routine
   * name is the subworkflow name defined in the YAML source code. The top level
   * routine name is `main`.
   *
   * @var string
   */
  public $routine;
  /**
   * Output only. The state of the step entry.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The name of the step this step entry belongs to.
   *
   * @var string
   */
  public $step;
  protected $stepEntryMetadataType = StepEntryMetadata::class;
  protected $stepEntryMetadataDataType = '';
  /**
   * Output only. The type of the step this step entry belongs to.
   *
   * @var string
   */
  public $stepType;
  /**
   * Output only. The most recently updated time of the step entry.
   *
   * @var string
   */
  public $updateTime;
  protected $variableDataType = VariableData::class;
  protected $variableDataDataType = '';

  /**
   * Output only. The creation time of the step entry.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Output only. The numeric ID of this step entry, used for navigation.
   *
   * @param string $entryId
   */
  public function setEntryId($entryId)
  {
    $this->entryId = $entryId;
  }
  /**
   * @return string
   */
  public function getEntryId()
  {
    return $this->entryId;
  }
  /**
   * Output only. The exception thrown by the step entry.
   *
   * @param Exception $exception
   */
  public function setException(Exception $exception)
  {
    $this->exception = $exception;
  }
  /**
   * @return Exception
   */
  public function getException()
  {
    return $this->exception;
  }
  /**
   * Output only. The full resource name of the step entry. Each step entry has
   * a unique entry ID, which is a monotonically increasing counter. Step entry
   * names have the format: `projects/{project}/locations/{location}/workflows/{
   * workflow}/executions/{execution}/stepEntries/{step_entry}`.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. The NavigationInfo associated with this step.
   *
   * @param NavigationInfo $navigationInfo
   */
  public function setNavigationInfo(NavigationInfo $navigationInfo)
  {
    $this->navigationInfo = $navigationInfo;
  }
  /**
   * @return NavigationInfo
   */
  public function getNavigationInfo()
  {
    return $this->navigationInfo;
  }
  /**
   * Output only. The name of the routine this step entry belongs to. A routine
   * name is the subworkflow name defined in the YAML source code. The top level
   * routine name is `main`.
   *
   * @param string $routine
   */
  public function setRoutine($routine)
  {
    $this->routine = $routine;
  }
  /**
   * @return string
   */
  public function getRoutine()
  {
    return $this->routine;
  }
  /**
   * Output only. The state of the step entry.
   *
   * Accepted values: STATE_UNSPECIFIED, STATE_IN_PROGRESS, STATE_SUCCEEDED,
   * STATE_FAILED, STATE_CANCELLED
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
   * Output only. The name of the step this step entry belongs to.
   *
   * @param string $step
   */
  public function setStep($step)
  {
    $this->step = $step;
  }
  /**
   * @return string
   */
  public function getStep()
  {
    return $this->step;
  }
  /**
   * Output only. The StepEntryMetadata associated with this step.
   *
   * @param StepEntryMetadata $stepEntryMetadata
   */
  public function setStepEntryMetadata(StepEntryMetadata $stepEntryMetadata)
  {
    $this->stepEntryMetadata = $stepEntryMetadata;
  }
  /**
   * @return StepEntryMetadata
   */
  public function getStepEntryMetadata()
  {
    return $this->stepEntryMetadata;
  }
  /**
   * Output only. The type of the step this step entry belongs to.
   *
   * Accepted values: STEP_TYPE_UNSPECIFIED, STEP_ASSIGN, STEP_STD_LIB_CALL,
   * STEP_CONNECTOR_CALL, STEP_SUBWORKFLOW_CALL, STEP_CALL, STEP_SWITCH,
   * STEP_CONDITION, STEP_FOR, STEP_FOR_ITERATION, STEP_PARALLEL_FOR,
   * STEP_PARALLEL_BRANCH, STEP_PARALLEL_BRANCH_ENTRY, STEP_TRY_RETRY_EXCEPT,
   * STEP_TRY, STEP_RETRY, STEP_EXCEPT, STEP_RETURN, STEP_RAISE, STEP_GOTO
   *
   * @param self::STEP_TYPE_* $stepType
   */
  public function setStepType($stepType)
  {
    $this->stepType = $stepType;
  }
  /**
   * @return self::STEP_TYPE_*
   */
  public function getStepType()
  {
    return $this->stepType;
  }
  /**
   * Output only. The most recently updated time of the step entry.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
  /**
   * Output only. The VariableData associated with this step.
   *
   * @param VariableData $variableData
   */
  public function setVariableData(VariableData $variableData)
  {
    $this->variableData = $variableData;
  }
  /**
   * @return VariableData
   */
  public function getVariableData()
  {
    return $this->variableData;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StepEntry::class, 'Google_Service_WorkflowExecutions_StepEntry');
