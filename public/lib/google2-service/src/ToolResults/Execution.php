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

namespace Google\Service\ToolResults;

class Execution extends \Google\Collection
{
  /**
   * Should never be in this state. Exists for proto deserialization backward
   * compatibility.
   */
  public const STATE_unknownState = 'unknownState';
  /**
   * The Execution/Step is created, ready to run, but not running yet. If an
   * Execution/Step is created without initial state, it is assumed that the
   * Execution/Step is in PENDING state.
   */
  public const STATE_pending = 'pending';
  /**
   * The Execution/Step is in progress.
   */
  public const STATE_inProgress = 'inProgress';
  /**
   * The finalized, immutable state. Steps/Executions in this state cannot be
   * modified.
   */
  public const STATE_complete = 'complete';
  protected $collection_key = 'dimensionDefinitions';
  protected $completionTimeType = Timestamp::class;
  protected $completionTimeDataType = '';
  protected $creationTimeType = Timestamp::class;
  protected $creationTimeDataType = '';
  protected $dimensionDefinitionsType = MatrixDimensionDefinition::class;
  protected $dimensionDefinitionsDataType = 'array';
  /**
   * A unique identifier within a History for this Execution. Returns
   * INVALID_ARGUMENT if this field is set or overwritten by the caller. - In
   * response always set - In create/update request: never set
   *
   * @var string
   */
  public $executionId;
  protected $outcomeType = Outcome::class;
  protected $outcomeDataType = '';
  protected $specificationType = Specification::class;
  protected $specificationDataType = '';
  /**
   * The initial state is IN_PROGRESS. The only legal state transitions is from
   * IN_PROGRESS to COMPLETE. A PRECONDITION_FAILED will be returned if an
   * invalid transition is requested. The state can only be set to COMPLETE
   * once. A FAILED_PRECONDITION will be returned if the state is set to
   * COMPLETE multiple times. If the state is set to COMPLETE, all the in-
   * progress steps within the execution will be set as COMPLETE. If the outcome
   * of the step is not set, the outcome will be set to INCONCLUSIVE. - In
   * response always set - In create/update request: optional
   *
   * @var string
   */
  public $state;
  /**
   * TestExecution Matrix ID that the TestExecutionService uses. - In response:
   * present if set by create - In create: optional - In update: never set
   *
   * @var string
   */
  public $testExecutionMatrixId;

  /**
   * The time when the Execution status transitioned to COMPLETE. This value
   * will be set automatically when state transitions to COMPLETE. - In
   * response: set if the execution state is COMPLETE. - In create/update
   * request: never set
   *
   * @param Timestamp $completionTime
   */
  public function setCompletionTime(Timestamp $completionTime)
  {
    $this->completionTime = $completionTime;
  }
  /**
   * @return Timestamp
   */
  public function getCompletionTime()
  {
    return $this->completionTime;
  }
  /**
   * The time when the Execution was created. This value will be set
   * automatically when CreateExecution is called. - In response: always set -
   * In create/update request: never set
   *
   * @param Timestamp $creationTime
   */
  public function setCreationTime(Timestamp $creationTime)
  {
    $this->creationTime = $creationTime;
  }
  /**
   * @return Timestamp
   */
  public function getCreationTime()
  {
    return $this->creationTime;
  }
  /**
   * The dimensions along which different steps in this execution may vary. This
   * must remain fixed over the life of the execution. Returns INVALID_ARGUMENT
   * if this field is set in an update request. Returns INVALID_ARGUMENT if the
   * same name occurs in more than one dimension_definition. Returns
   * INVALID_ARGUMENT if the size of the list is over 100. - In response:
   * present if set by create - In create request: optional - In update request:
   * never set
   *
   * @param MatrixDimensionDefinition[] $dimensionDefinitions
   */
  public function setDimensionDefinitions($dimensionDefinitions)
  {
    $this->dimensionDefinitions = $dimensionDefinitions;
  }
  /**
   * @return MatrixDimensionDefinition[]
   */
  public function getDimensionDefinitions()
  {
    return $this->dimensionDefinitions;
  }
  /**
   * A unique identifier within a History for this Execution. Returns
   * INVALID_ARGUMENT if this field is set or overwritten by the caller. - In
   * response always set - In create/update request: never set
   *
   * @param string $executionId
   */
  public function setExecutionId($executionId)
  {
    $this->executionId = $executionId;
  }
  /**
   * @return string
   */
  public function getExecutionId()
  {
    return $this->executionId;
  }
  /**
   * Classify the result, for example into SUCCESS or FAILURE - In response:
   * present if set by create/update request - In create/update request:
   * optional
   *
   * @param Outcome $outcome
   */
  public function setOutcome(Outcome $outcome)
  {
    $this->outcome = $outcome;
  }
  /**
   * @return Outcome
   */
  public function getOutcome()
  {
    return $this->outcome;
  }
  /**
   * Lightweight information about execution request. - In response: present if
   * set by create - In create: optional - In update: optional
   *
   * @param Specification $specification
   */
  public function setSpecification(Specification $specification)
  {
    $this->specification = $specification;
  }
  /**
   * @return Specification
   */
  public function getSpecification()
  {
    return $this->specification;
  }
  /**
   * The initial state is IN_PROGRESS. The only legal state transitions is from
   * IN_PROGRESS to COMPLETE. A PRECONDITION_FAILED will be returned if an
   * invalid transition is requested. The state can only be set to COMPLETE
   * once. A FAILED_PRECONDITION will be returned if the state is set to
   * COMPLETE multiple times. If the state is set to COMPLETE, all the in-
   * progress steps within the execution will be set as COMPLETE. If the outcome
   * of the step is not set, the outcome will be set to INCONCLUSIVE. - In
   * response always set - In create/update request: optional
   *
   * Accepted values: unknownState, pending, inProgress, complete
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
   * TestExecution Matrix ID that the TestExecutionService uses. - In response:
   * present if set by create - In create: optional - In update: never set
   *
   * @param string $testExecutionMatrixId
   */
  public function setTestExecutionMatrixId($testExecutionMatrixId)
  {
    $this->testExecutionMatrixId = $testExecutionMatrixId;
  }
  /**
   * @return string
   */
  public function getTestExecutionMatrixId()
  {
    return $this->testExecutionMatrixId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Execution::class, 'Google_Service_ToolResults_Execution');
