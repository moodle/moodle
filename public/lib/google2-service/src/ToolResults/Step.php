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

class Step extends \Google\Collection
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
  protected $collection_key = 'labels';
  protected $completionTimeType = Timestamp::class;
  protected $completionTimeDataType = '';
  protected $creationTimeType = Timestamp::class;
  protected $creationTimeDataType = '';
  /**
   * A description of this tool For example: mvn clean package -D skipTests=true
   * - In response: present if set by create/update request - In create/update
   * request: optional
   *
   * @var string
   */
  public $description;
  protected $deviceUsageDurationType = Duration::class;
  protected $deviceUsageDurationDataType = '';
  protected $dimensionValueType = StepDimensionValueEntry::class;
  protected $dimensionValueDataType = 'array';
  /**
   * Whether any of the outputs of this step are images whose thumbnails can be
   * fetched with ListThumbnails. - In response: always set - In create/update
   * request: never set
   *
   * @var bool
   */
  public $hasImages;
  protected $labelsType = StepLabelsEntry::class;
  protected $labelsDataType = 'array';
  protected $multiStepType = MultiStep::class;
  protected $multiStepDataType = '';
  /**
   * A short human-readable name to display in the UI. Maximum of 100
   * characters. For example: Clean build A PRECONDITION_FAILED will be returned
   * upon creating a new step if it shares its name and dimension_value with an
   * existing step. If two steps represent a similar action, but have different
   * dimension values, they should share the same name. For instance, if the
   * same set of tests is run on two different platforms, the two steps should
   * have the same name. - In response: always set - In create request: always
   * set - In update request: never set
   *
   * @var string
   */
  public $name;
  protected $outcomeType = Outcome::class;
  protected $outcomeDataType = '';
  protected $runDurationType = Duration::class;
  protected $runDurationDataType = '';
  /**
   * The initial state is IN_PROGRESS. The only legal state transitions are *
   * IN_PROGRESS -> COMPLETE A PRECONDITION_FAILED will be returned if an
   * invalid transition is requested. It is valid to create Step with a state
   * set to COMPLETE. The state can only be set to COMPLETE once. A
   * PRECONDITION_FAILED will be returned if the state is set to COMPLETE
   * multiple times. - In response: always set - In create/update request:
   * optional
   *
   * @var string
   */
  public $state;
  /**
   * A unique identifier within a Execution for this Step. Returns
   * INVALID_ARGUMENT if this field is set or overwritten by the caller. - In
   * response: always set - In create/update request: never set
   *
   * @var string
   */
  public $stepId;
  protected $testExecutionStepType = TestExecutionStep::class;
  protected $testExecutionStepDataType = '';
  protected $toolExecutionStepType = ToolExecutionStep::class;
  protected $toolExecutionStepDataType = '';

  /**
   * The time when the step status was set to complete. This value will be set
   * automatically when state transitions to COMPLETE. - In response: set if the
   * execution state is COMPLETE. - In create/update request: never set
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
   * The time when the step was created. - In response: always set - In
   * create/update request: never set
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
   * A description of this tool For example: mvn clean package -D skipTests=true
   * - In response: present if set by create/update request - In create/update
   * request: optional
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * How much the device resource is used to perform the test. This is the
   * device usage used for billing purpose, which is different from the
   * run_duration, for example, infrastructure failure won't be charged for
   * device usage. PRECONDITION_FAILED will be returned if one attempts to set a
   * device_usage on a step which already has this field set. - In response:
   * present if previously set. - In create request: optional - In update
   * request: optional
   *
   * @param Duration $deviceUsageDuration
   */
  public function setDeviceUsageDuration(Duration $deviceUsageDuration)
  {
    $this->deviceUsageDuration = $deviceUsageDuration;
  }
  /**
   * @return Duration
   */
  public function getDeviceUsageDuration()
  {
    return $this->deviceUsageDuration;
  }
  /**
   * If the execution containing this step has any dimension_definition set,
   * then this field allows the child to specify the values of the dimensions.
   * The keys must exactly match the dimension_definition of the execution. For
   * example, if the execution has `dimension_definition = ['attempt',
   * 'device']` then a step must define values for those dimensions, eg.
   * `dimension_value = ['attempt': '1', 'device': 'Nexus 6']` If a step does
   * not participate in one dimension of the matrix, the value for that
   * dimension should be empty string. For example, if one of the tests is
   * executed by a runner which does not support retries, the step could have
   * `dimension_value = ['attempt': '', 'device': 'Nexus 6']` If the step does
   * not participate in any dimensions of the matrix, it may leave
   * dimension_value unset. A PRECONDITION_FAILED will be returned if any of the
   * keys do not exist in the dimension_definition of the execution. A
   * PRECONDITION_FAILED will be returned if another step in this execution
   * already has the same name and dimension_value, but differs on other data
   * fields, for example, step field is different. A PRECONDITION_FAILED will be
   * returned if dimension_value is set, and there is a dimension_definition in
   * the execution which is not specified as one of the keys. - In response:
   * present if set by create - In create request: optional - In update request:
   * never set
   *
   * @param StepDimensionValueEntry[] $dimensionValue
   */
  public function setDimensionValue($dimensionValue)
  {
    $this->dimensionValue = $dimensionValue;
  }
  /**
   * @return StepDimensionValueEntry[]
   */
  public function getDimensionValue()
  {
    return $this->dimensionValue;
  }
  /**
   * Whether any of the outputs of this step are images whose thumbnails can be
   * fetched with ListThumbnails. - In response: always set - In create/update
   * request: never set
   *
   * @param bool $hasImages
   */
  public function setHasImages($hasImages)
  {
    $this->hasImages = $hasImages;
  }
  /**
   * @return bool
   */
  public function getHasImages()
  {
    return $this->hasImages;
  }
  /**
   * Arbitrary user-supplied key/value pairs that are associated with the step.
   * Users are responsible for managing the key namespace such that keys don't
   * accidentally collide. An INVALID_ARGUMENT will be returned if the number of
   * labels exceeds 100 or if the length of any of the keys or values exceeds
   * 100 characters. - In response: always set - In create request: optional -
   * In update request: optional; any new key/value pair will be added to the
   * map, and any new value for an existing key will update that key's value
   *
   * @param StepLabelsEntry[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return StepLabelsEntry[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Details when multiple steps are run with the same configuration as a group.
   * These details can be used identify which group this step is part of. It
   * also identifies the groups 'primary step' which indexes all the group
   * members. - In response: present if previously set. - In create request:
   * optional, set iff this step was performed more than once. - In update
   * request: optional
   *
   * @param MultiStep $multiStep
   */
  public function setMultiStep(MultiStep $multiStep)
  {
    $this->multiStep = $multiStep;
  }
  /**
   * @return MultiStep
   */
  public function getMultiStep()
  {
    return $this->multiStep;
  }
  /**
   * A short human-readable name to display in the UI. Maximum of 100
   * characters. For example: Clean build A PRECONDITION_FAILED will be returned
   * upon creating a new step if it shares its name and dimension_value with an
   * existing step. If two steps represent a similar action, but have different
   * dimension values, they should share the same name. For instance, if the
   * same set of tests is run on two different platforms, the two steps should
   * have the same name. - In response: always set - In create request: always
   * set - In update request: never set
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
   * Classification of the result, for example into SUCCESS or FAILURE - In
   * response: present if set by create/update request - In create/update
   * request: optional
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
   * How long it took for this step to run. If unset, this is set to the
   * difference between creation_time and completion_time when the step is set
   * to the COMPLETE state. In some cases, it is appropriate to set this value
   * separately: For instance, if a step is created, but the operation it
   * represents is queued for a few minutes before it executes, it would be
   * appropriate not to include the time spent queued in its run_duration.
   * PRECONDITION_FAILED will be returned if one attempts to set a run_duration
   * on a step which already has this field set. - In response: present if
   * previously set; always present on COMPLETE step - In create request:
   * optional - In update request: optional
   *
   * @param Duration $runDuration
   */
  public function setRunDuration(Duration $runDuration)
  {
    $this->runDuration = $runDuration;
  }
  /**
   * @return Duration
   */
  public function getRunDuration()
  {
    return $this->runDuration;
  }
  /**
   * The initial state is IN_PROGRESS. The only legal state transitions are *
   * IN_PROGRESS -> COMPLETE A PRECONDITION_FAILED will be returned if an
   * invalid transition is requested. It is valid to create Step with a state
   * set to COMPLETE. The state can only be set to COMPLETE once. A
   * PRECONDITION_FAILED will be returned if the state is set to COMPLETE
   * multiple times. - In response: always set - In create/update request:
   * optional
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
   * A unique identifier within a Execution for this Step. Returns
   * INVALID_ARGUMENT if this field is set or overwritten by the caller. - In
   * response: always set - In create/update request: never set
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
  /**
   * An execution of a test runner.
   *
   * @param TestExecutionStep $testExecutionStep
   */
  public function setTestExecutionStep(TestExecutionStep $testExecutionStep)
  {
    $this->testExecutionStep = $testExecutionStep;
  }
  /**
   * @return TestExecutionStep
   */
  public function getTestExecutionStep()
  {
    return $this->testExecutionStep;
  }
  /**
   * An execution of a tool (used for steps we don't explicitly support).
   *
   * @param ToolExecutionStep $toolExecutionStep
   */
  public function setToolExecutionStep(ToolExecutionStep $toolExecutionStep)
  {
    $this->toolExecutionStep = $toolExecutionStep;
  }
  /**
   * @return ToolExecutionStep
   */
  public function getToolExecutionStep()
  {
    return $this->toolExecutionStep;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Step::class, 'Google_Service_ToolResults_Step');
