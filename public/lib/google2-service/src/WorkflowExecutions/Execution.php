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

class Execution extends \Google\Model
{
  /**
   * No call logging level specified.
   */
  public const CALL_LOG_LEVEL_CALL_LOG_LEVEL_UNSPECIFIED = 'CALL_LOG_LEVEL_UNSPECIFIED';
  /**
   * Log all call steps within workflows, all call returns, and all exceptions
   * raised.
   */
  public const CALL_LOG_LEVEL_LOG_ALL_CALLS = 'LOG_ALL_CALLS';
  /**
   * Log only exceptions that are raised from call steps within workflows.
   */
  public const CALL_LOG_LEVEL_LOG_ERRORS_ONLY = 'LOG_ERRORS_ONLY';
  /**
   * Explicitly log nothing.
   */
  public const CALL_LOG_LEVEL_LOG_NONE = 'LOG_NONE';
  /**
   * The default/unset value.
   */
  public const EXECUTION_HISTORY_LEVEL_EXECUTION_HISTORY_LEVEL_UNSPECIFIED = 'EXECUTION_HISTORY_LEVEL_UNSPECIFIED';
  /**
   * Enable execution history basic feature for this execution.
   */
  public const EXECUTION_HISTORY_LEVEL_EXECUTION_HISTORY_BASIC = 'EXECUTION_HISTORY_BASIC';
  /**
   * Enable execution history detailed feature for this execution.
   */
  public const EXECUTION_HISTORY_LEVEL_EXECUTION_HISTORY_DETAILED = 'EXECUTION_HISTORY_DETAILED';
  /**
   * Invalid state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The execution is in progress.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The execution finished successfully.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The execution failed with an error.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The execution was stopped intentionally.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  /**
   * Execution data is unavailable. See the `state_error` field.
   */
  public const STATE_UNAVAILABLE = 'UNAVAILABLE';
  /**
   * Request has been placed in the backlog for processing at a later time.
   */
  public const STATE_QUEUED = 'QUEUED';
  /**
   * Input parameters of the execution represented as a JSON string. The size
   * limit is 32KB. *Note*: If you are using the REST API directly to run your
   * workflow, you must escape any JSON string value of `argument`. Example:
   * `'{"argument":"{\"firstName\":\"FIRST\",\"lastName\":\"LAST\"}"}'`
   *
   * @var string
   */
  public $argument;
  /**
   * The call logging level associated to this execution.
   *
   * @var string
   */
  public $callLogLevel;
  /**
   * Output only. Marks the creation of the execution.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. If set to true, the execution will not be backlogged when the
   * concurrency quota is exhausted. The backlog execution starts when the
   * concurrency quota becomes available.
   *
   * @var bool
   */
  public $disableConcurrencyQuotaOverflowBuffering;
  /**
   * Output only. Measures the duration of the execution.
   *
   * @var string
   */
  public $duration;
  /**
   * Output only. Marks the end of execution, successful or not.
   *
   * @var string
   */
  public $endTime;
  protected $errorType = Error::class;
  protected $errorDataType = '';
  /**
   * Optional. Describes the execution history level to apply to this execution.
   * If not specified, the execution history level is determined by its
   * workflow's execution history level. If the levels are different, the
   * executionHistoryLevel overrides the workflow's execution history level for
   * this execution.
   *
   * @var string
   */
  public $executionHistoryLevel;
  /**
   * Labels associated with this execution. Labels can contain at most 64
   * entries. Keys and values can be no longer than 63 characters and can only
   * contain lowercase letters, numeric characters, underscores, and dashes.
   * Label keys must start with a letter. International characters are allowed.
   * By default, labels are inherited from the workflow but are overridden by
   * any labels associated with the execution.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. The resource name of the execution. Format: projects/{project}
   * /locations/{location}/workflows/{workflow}/executions/{execution}
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Output of the execution represented as a JSON string. The
   * value can only be present if the execution's state is `SUCCEEDED`.
   *
   * @var string
   */
  public $result;
  /**
   * Output only. Marks the beginning of execution. Note that this will be the
   * same as `createTime` for executions that start immediately.
   *
   * @var string
   */
  public $startTime;
  /**
   * Output only. Current state of the execution.
   *
   * @var string
   */
  public $state;
  protected $stateErrorType = StateError::class;
  protected $stateErrorDataType = '';
  protected $statusType = Status::class;
  protected $statusDataType = '';
  /**
   * Output only. Revision of the workflow this execution is using.
   *
   * @var string
   */
  public $workflowRevisionId;

  /**
   * Input parameters of the execution represented as a JSON string. The size
   * limit is 32KB. *Note*: If you are using the REST API directly to run your
   * workflow, you must escape any JSON string value of `argument`. Example:
   * `'{"argument":"{\"firstName\":\"FIRST\",\"lastName\":\"LAST\"}"}'`
   *
   * @param string $argument
   */
  public function setArgument($argument)
  {
    $this->argument = $argument;
  }
  /**
   * @return string
   */
  public function getArgument()
  {
    return $this->argument;
  }
  /**
   * The call logging level associated to this execution.
   *
   * Accepted values: CALL_LOG_LEVEL_UNSPECIFIED, LOG_ALL_CALLS,
   * LOG_ERRORS_ONLY, LOG_NONE
   *
   * @param self::CALL_LOG_LEVEL_* $callLogLevel
   */
  public function setCallLogLevel($callLogLevel)
  {
    $this->callLogLevel = $callLogLevel;
  }
  /**
   * @return self::CALL_LOG_LEVEL_*
   */
  public function getCallLogLevel()
  {
    return $this->callLogLevel;
  }
  /**
   * Output only. Marks the creation of the execution.
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
   * Optional. If set to true, the execution will not be backlogged when the
   * concurrency quota is exhausted. The backlog execution starts when the
   * concurrency quota becomes available.
   *
   * @param bool $disableConcurrencyQuotaOverflowBuffering
   */
  public function setDisableConcurrencyQuotaOverflowBuffering($disableConcurrencyQuotaOverflowBuffering)
  {
    $this->disableConcurrencyQuotaOverflowBuffering = $disableConcurrencyQuotaOverflowBuffering;
  }
  /**
   * @return bool
   */
  public function getDisableConcurrencyQuotaOverflowBuffering()
  {
    return $this->disableConcurrencyQuotaOverflowBuffering;
  }
  /**
   * Output only. Measures the duration of the execution.
   *
   * @param string $duration
   */
  public function setDuration($duration)
  {
    $this->duration = $duration;
  }
  /**
   * @return string
   */
  public function getDuration()
  {
    return $this->duration;
  }
  /**
   * Output only. Marks the end of execution, successful or not.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Output only. The error which caused the execution to finish prematurely.
   * The value is only present if the execution's state is `FAILED` or
   * `CANCELLED`.
   *
   * @param Error $error
   */
  public function setError(Error $error)
  {
    $this->error = $error;
  }
  /**
   * @return Error
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * Optional. Describes the execution history level to apply to this execution.
   * If not specified, the execution history level is determined by its
   * workflow's execution history level. If the levels are different, the
   * executionHistoryLevel overrides the workflow's execution history level for
   * this execution.
   *
   * Accepted values: EXECUTION_HISTORY_LEVEL_UNSPECIFIED,
   * EXECUTION_HISTORY_BASIC, EXECUTION_HISTORY_DETAILED
   *
   * @param self::EXECUTION_HISTORY_LEVEL_* $executionHistoryLevel
   */
  public function setExecutionHistoryLevel($executionHistoryLevel)
  {
    $this->executionHistoryLevel = $executionHistoryLevel;
  }
  /**
   * @return self::EXECUTION_HISTORY_LEVEL_*
   */
  public function getExecutionHistoryLevel()
  {
    return $this->executionHistoryLevel;
  }
  /**
   * Labels associated with this execution. Labels can contain at most 64
   * entries. Keys and values can be no longer than 63 characters and can only
   * contain lowercase letters, numeric characters, underscores, and dashes.
   * Label keys must start with a letter. International characters are allowed.
   * By default, labels are inherited from the workflow but are overridden by
   * any labels associated with the execution.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Output only. The resource name of the execution. Format: projects/{project}
   * /locations/{location}/workflows/{workflow}/executions/{execution}
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
   * Output only. Output of the execution represented as a JSON string. The
   * value can only be present if the execution's state is `SUCCEEDED`.
   *
   * @param string $result
   */
  public function setResult($result)
  {
    $this->result = $result;
  }
  /**
   * @return string
   */
  public function getResult()
  {
    return $this->result;
  }
  /**
   * Output only. Marks the beginning of execution. Note that this will be the
   * same as `createTime` for executions that start immediately.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * Output only. Current state of the execution.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, SUCCEEDED, FAILED, CANCELLED,
   * UNAVAILABLE, QUEUED
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
   * Output only. Error regarding the state of the Execution resource. For
   * example, this field will have error details if the execution data is
   * unavailable due to revoked KMS key permissions.
   *
   * @param StateError $stateError
   */
  public function setStateError(StateError $stateError)
  {
    $this->stateError = $stateError;
  }
  /**
   * @return StateError
   */
  public function getStateError()
  {
    return $this->stateError;
  }
  /**
   * Output only. Status tracks the current steps and progress data of this
   * execution.
   *
   * @param Status $status
   */
  public function setStatus(Status $status)
  {
    $this->status = $status;
  }
  /**
   * @return Status
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Output only. Revision of the workflow this execution is using.
   *
   * @param string $workflowRevisionId
   */
  public function setWorkflowRevisionId($workflowRevisionId)
  {
    $this->workflowRevisionId = $workflowRevisionId;
  }
  /**
   * @return string
   */
  public function getWorkflowRevisionId()
  {
    return $this->workflowRevisionId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Execution::class, 'Google_Service_WorkflowExecutions_Execution');
