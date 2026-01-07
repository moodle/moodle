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

namespace Google\Service\Integrations;

class EnterpriseCrmFrontendsEventbusProtoTaskConfig extends \Google\Collection
{
  /**
   * Default value. External task type is not specified
   */
  public const EXTERNAL_TASK_TYPE_EXTERNAL_TASK_TYPE_UNSPECIFIED = 'EXTERNAL_TASK_TYPE_UNSPECIFIED';
  /**
   * Tasks belongs to the normal task flows
   */
  public const EXTERNAL_TASK_TYPE_NORMAL_TASK = 'NORMAL_TASK';
  /**
   * Task belongs to the error catch task flows
   */
  public const EXTERNAL_TASK_TYPE_ERROR_TASK = 'ERROR_TASK';
  /**
   * As per the default behavior, no validation will be run. Will not override
   * any option set in a Task.
   */
  public const JSON_VALIDATION_OPTION_UNSPECIFIED_JSON_VALIDATION_OPTION = 'UNSPECIFIED_JSON_VALIDATION_OPTION';
  /**
   * Do not run any validation against JSON schemas.
   */
  public const JSON_VALIDATION_OPTION_SKIP = 'SKIP';
  /**
   * Validate all potential input JSON parameters against schemas specified in
   * WorkflowParameters.
   */
  public const JSON_VALIDATION_OPTION_PRE_EXECUTION = 'PRE_EXECUTION';
  /**
   * Validate all potential output JSON parameters against schemas specified in
   * WorkflowParameters.
   */
  public const JSON_VALIDATION_OPTION_POST_EXECUTION = 'POST_EXECUTION';
  /**
   * Perform both PRE_EXECUTION and POST_EXECUTION validations.
   */
  public const JSON_VALIDATION_OPTION_PRE_POST_EXECUTION = 'PRE_POST_EXECUTION';
  /**
   * Default
   */
  public const NEXT_TASKS_EXECUTION_POLICY_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Execute all the tasks that satisfy their associated condition.
   */
  public const NEXT_TASKS_EXECUTION_POLICY_RUN_ALL_MATCH = 'RUN_ALL_MATCH';
  /**
   * Execute the first task that satisfies the associated condition.
   */
  public const NEXT_TASKS_EXECUTION_POLICY_RUN_FIRST_MATCH = 'RUN_FIRST_MATCH';
  /**
   * Wait until all of its previous tasks finished execution, then verify at
   * least one of the edge conditions is met, and execute if possible. This
   * should be considered as WHEN_ALL_TASKS_SUCCEED.
   */
  public const TASK_EXECUTION_STRATEGY_WHEN_ALL_SUCCEED = 'WHEN_ALL_SUCCEED';
  /**
   * Start execution as long as any of its previous tasks finished execution and
   * the corresponding edge condition is met (since we will execute if only that
   * succeeding edge condition is met).
   */
  public const TASK_EXECUTION_STRATEGY_WHEN_ANY_SUCCEED = 'WHEN_ANY_SUCCEED';
  /**
   * Wait until all of its previous tasks finished execution, then verify the
   * all edge conditions are met and execute if possible.
   */
  public const TASK_EXECUTION_STRATEGY_WHEN_ALL_TASKS_AND_CONDITIONS_SUCCEED = 'WHEN_ALL_TASKS_AND_CONDITIONS_SUCCEED';
  /**
   * Normal IP task
   */
  public const TASK_TYPE_TASK = 'TASK';
  /**
   * Task is of As-Is Template type
   */
  public const TASK_TYPE_ASIS_TEMPLATE = 'ASIS_TEMPLATE';
  /**
   * Task is of I/O template type with a different underlying task
   */
  public const TASK_TYPE_IO_TEMPLATE = 'IO_TEMPLATE';
  protected $collection_key = 'nextTasks';
  protected $alertConfigsType = EnterpriseCrmEventbusProtoTaskAlertConfig::class;
  protected $alertConfigsDataType = 'array';
  protected $conditionalFailurePoliciesType = EnterpriseCrmEventbusProtoConditionalFailurePolicies::class;
  protected $conditionalFailurePoliciesDataType = '';
  /**
   * Auto-generated.
   *
   * @var string
   */
  public $createTime;
  /**
   * The creator's email address. Auto-generated from the user's email.
   *
   * @var string
   */
  public $creatorEmail;
  /**
   * User-provided description intended to give more business context about the
   * task.
   *
   * @var string
   */
  public $description;
  /**
   * If this config contains a TypedTask, allow validation to succeed if an
   * input is read from the output of another TypedTask whose output type is
   * declared as a superclass of the requested input type. For instance, if the
   * previous task declares an output of type Message, any task with this flag
   * enabled will pass validation when attempting to read any proto Message type
   * from the resultant Event parameter.
   *
   * @var bool
   */
  public $disableStrictTypeValidation;
  /**
   * Optional Error catcher id of the error catch flow which will be executed
   * when execution error happens in the task
   *
   * @var string
   */
  public $errorCatcherId;
  /**
   * @var string
   */
  public $externalTaskType;
  protected $failurePolicyType = EnterpriseCrmEventbusProtoFailurePolicy::class;
  protected $failurePolicyDataType = '';
  /**
   * The number of edges leading into this TaskConfig.
   *
   * @var int
   */
  public $incomingEdgeCount;
  /**
   * If set, overrides the option configured in the Task implementation class.
   *
   * @var string
   */
  public $jsonValidationOption;
  /**
   * User-provided label that is attached to this TaskConfig in the UI.
   *
   * @var string
   */
  public $label;
  /**
   * Auto-generated.
   *
   * @var string
   */
  public $lastModifiedTime;
  protected $nextTasksType = EnterpriseCrmEventbusProtoNextTask::class;
  protected $nextTasksDataType = 'array';
  /**
   * The policy dictating the execution of the next set of tasks for the current
   * task.
   *
   * @var string
   */
  public $nextTasksExecutionPolicy;
  protected $parametersType = EnterpriseCrmFrontendsEventbusProtoParameterEntry::class;
  protected $parametersDataType = 'map';
  protected $positionType = EnterpriseCrmEventbusProtoCoordinate::class;
  protected $positionDataType = '';
  /**
   * Optional. Standard filter expression evaluated before execution.
   * Independent of other conditions and tasks. Can be used to enable rollout.
   * e.g. "rollout(5)" will only allow 5% of incoming traffic to task.
   *
   * @var string
   */
  public $precondition;
  /**
   * Optional. User-provided label that is attached to precondition in the UI.
   *
   * @var string
   */
  public $preconditionLabel;
  protected $rollbackStrategyType = EnterpriseCrmFrontendsEventbusProtoRollbackStrategy::class;
  protected $rollbackStrategyDataType = '';
  protected $successPolicyType = EnterpriseCrmEventbusProtoSuccessPolicy::class;
  protected $successPolicyDataType = '';
  protected $synchronousCallFailurePolicyType = EnterpriseCrmEventbusProtoFailurePolicy::class;
  protected $synchronousCallFailurePolicyDataType = '';
  protected $taskEntityType = EnterpriseCrmFrontendsEventbusProtoTaskEntity::class;
  protected $taskEntityDataType = '';
  /**
   * The policy dictating the execution strategy of this task.
   *
   * @var string
   */
  public $taskExecutionStrategy;
  /**
   * The name for the task.
   *
   * @var string
   */
  public $taskName;
  /**
   * REQUIRED: the identifier of this task within its parent event config,
   * specified by the client. This should be unique among all the tasks belong
   * to the same event config. We use this field as the identifier to find next
   * tasks (via field `next_tasks.task_number`).
   *
   * @var string
   */
  public $taskNumber;
  /**
   * A string template that allows user to configure task parameters (with
   * either literal default values or tokens which will be resolved at execution
   * time) for the task. It will eventually replace the old "parameters" field.
   *
   * @var string
   */
  public $taskSpec;
  /**
   * Used to define task-template name if task is of type task-template
   *
   * @var string
   */
  public $taskTemplateName;
  /**
   * Defines the type of the task
   *
   * @var string
   */
  public $taskType;

  /**
   * Alert configurations on error rate, warning rate, number of runs,
   * durations, etc.
   *
   * @param EnterpriseCrmEventbusProtoTaskAlertConfig[] $alertConfigs
   */
  public function setAlertConfigs($alertConfigs)
  {
    $this->alertConfigs = $alertConfigs;
  }
  /**
   * @return EnterpriseCrmEventbusProtoTaskAlertConfig[]
   */
  public function getAlertConfigs()
  {
    return $this->alertConfigs;
  }
  /**
   * Optional. Determines the number of times the task will be retried on
   * failure and with what retry strategy. This is applicable for synchronous
   * calls to Eventbus alone (Post).
   *
   * @param EnterpriseCrmEventbusProtoConditionalFailurePolicies $conditionalFailurePolicies
   */
  public function setConditionalFailurePolicies(EnterpriseCrmEventbusProtoConditionalFailurePolicies $conditionalFailurePolicies)
  {
    $this->conditionalFailurePolicies = $conditionalFailurePolicies;
  }
  /**
   * @return EnterpriseCrmEventbusProtoConditionalFailurePolicies
   */
  public function getConditionalFailurePolicies()
  {
    return $this->conditionalFailurePolicies;
  }
  /**
   * Auto-generated.
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
   * The creator's email address. Auto-generated from the user's email.
   *
   * @param string $creatorEmail
   */
  public function setCreatorEmail($creatorEmail)
  {
    $this->creatorEmail = $creatorEmail;
  }
  /**
   * @return string
   */
  public function getCreatorEmail()
  {
    return $this->creatorEmail;
  }
  /**
   * User-provided description intended to give more business context about the
   * task.
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
   * If this config contains a TypedTask, allow validation to succeed if an
   * input is read from the output of another TypedTask whose output type is
   * declared as a superclass of the requested input type. For instance, if the
   * previous task declares an output of type Message, any task with this flag
   * enabled will pass validation when attempting to read any proto Message type
   * from the resultant Event parameter.
   *
   * @param bool $disableStrictTypeValidation
   */
  public function setDisableStrictTypeValidation($disableStrictTypeValidation)
  {
    $this->disableStrictTypeValidation = $disableStrictTypeValidation;
  }
  /**
   * @return bool
   */
  public function getDisableStrictTypeValidation()
  {
    return $this->disableStrictTypeValidation;
  }
  /**
   * Optional Error catcher id of the error catch flow which will be executed
   * when execution error happens in the task
   *
   * @param string $errorCatcherId
   */
  public function setErrorCatcherId($errorCatcherId)
  {
    $this->errorCatcherId = $errorCatcherId;
  }
  /**
   * @return string
   */
  public function getErrorCatcherId()
  {
    return $this->errorCatcherId;
  }
  /**
   * @param self::EXTERNAL_TASK_TYPE_* $externalTaskType
   */
  public function setExternalTaskType($externalTaskType)
  {
    $this->externalTaskType = $externalTaskType;
  }
  /**
   * @return self::EXTERNAL_TASK_TYPE_*
   */
  public function getExternalTaskType()
  {
    return $this->externalTaskType;
  }
  /**
   * Optional. Determines the number of times the task will be retried on
   * failure and with what retry strategy. This is applicable for asynchronous
   * calls to Eventbus alone (Post To Queue, Schedule etc.).
   *
   * @param EnterpriseCrmEventbusProtoFailurePolicy $failurePolicy
   */
  public function setFailurePolicy(EnterpriseCrmEventbusProtoFailurePolicy $failurePolicy)
  {
    $this->failurePolicy = $failurePolicy;
  }
  /**
   * @return EnterpriseCrmEventbusProtoFailurePolicy
   */
  public function getFailurePolicy()
  {
    return $this->failurePolicy;
  }
  /**
   * The number of edges leading into this TaskConfig.
   *
   * @param int $incomingEdgeCount
   */
  public function setIncomingEdgeCount($incomingEdgeCount)
  {
    $this->incomingEdgeCount = $incomingEdgeCount;
  }
  /**
   * @return int
   */
  public function getIncomingEdgeCount()
  {
    return $this->incomingEdgeCount;
  }
  /**
   * If set, overrides the option configured in the Task implementation class.
   *
   * Accepted values: UNSPECIFIED_JSON_VALIDATION_OPTION, SKIP, PRE_EXECUTION,
   * POST_EXECUTION, PRE_POST_EXECUTION
   *
   * @param self::JSON_VALIDATION_OPTION_* $jsonValidationOption
   */
  public function setJsonValidationOption($jsonValidationOption)
  {
    $this->jsonValidationOption = $jsonValidationOption;
  }
  /**
   * @return self::JSON_VALIDATION_OPTION_*
   */
  public function getJsonValidationOption()
  {
    return $this->jsonValidationOption;
  }
  /**
   * User-provided label that is attached to this TaskConfig in the UI.
   *
   * @param string $label
   */
  public function setLabel($label)
  {
    $this->label = $label;
  }
  /**
   * @return string
   */
  public function getLabel()
  {
    return $this->label;
  }
  /**
   * Auto-generated.
   *
   * @param string $lastModifiedTime
   */
  public function setLastModifiedTime($lastModifiedTime)
  {
    $this->lastModifiedTime = $lastModifiedTime;
  }
  /**
   * @return string
   */
  public function getLastModifiedTime()
  {
    return $this->lastModifiedTime;
  }
  /**
   * The set of tasks that are next in line to be executed as per the execution
   * graph defined for the parent event, specified by `event_config_id`. Each of
   * these next tasks are executed only if the condition associated with them
   * evaluates to true.
   *
   * @param EnterpriseCrmEventbusProtoNextTask[] $nextTasks
   */
  public function setNextTasks($nextTasks)
  {
    $this->nextTasks = $nextTasks;
  }
  /**
   * @return EnterpriseCrmEventbusProtoNextTask[]
   */
  public function getNextTasks()
  {
    return $this->nextTasks;
  }
  /**
   * The policy dictating the execution of the next set of tasks for the current
   * task.
   *
   * Accepted values: UNSPECIFIED, RUN_ALL_MATCH, RUN_FIRST_MATCH
   *
   * @param self::NEXT_TASKS_EXECUTION_POLICY_* $nextTasksExecutionPolicy
   */
  public function setNextTasksExecutionPolicy($nextTasksExecutionPolicy)
  {
    $this->nextTasksExecutionPolicy = $nextTasksExecutionPolicy;
  }
  /**
   * @return self::NEXT_TASKS_EXECUTION_POLICY_*
   */
  public function getNextTasksExecutionPolicy()
  {
    return $this->nextTasksExecutionPolicy;
  }
  /**
   * The customized parameters the user can pass to this task.
   *
   * @param EnterpriseCrmFrontendsEventbusProtoParameterEntry[] $parameters
   */
  public function setParameters($parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return EnterpriseCrmFrontendsEventbusProtoParameterEntry[]
   */
  public function getParameters()
  {
    return $this->parameters;
  }
  /**
   * Optional. Informs the front-end application where to draw this task config
   * on the UI.
   *
   * @param EnterpriseCrmEventbusProtoCoordinate $position
   */
  public function setPosition(EnterpriseCrmEventbusProtoCoordinate $position)
  {
    $this->position = $position;
  }
  /**
   * @return EnterpriseCrmEventbusProtoCoordinate
   */
  public function getPosition()
  {
    return $this->position;
  }
  /**
   * Optional. Standard filter expression evaluated before execution.
   * Independent of other conditions and tasks. Can be used to enable rollout.
   * e.g. "rollout(5)" will only allow 5% of incoming traffic to task.
   *
   * @param string $precondition
   */
  public function setPrecondition($precondition)
  {
    $this->precondition = $precondition;
  }
  /**
   * @return string
   */
  public function getPrecondition()
  {
    return $this->precondition;
  }
  /**
   * Optional. User-provided label that is attached to precondition in the UI.
   *
   * @param string $preconditionLabel
   */
  public function setPreconditionLabel($preconditionLabel)
  {
    $this->preconditionLabel = $preconditionLabel;
  }
  /**
   * @return string
   */
  public function getPreconditionLabel()
  {
    return $this->preconditionLabel;
  }
  /**
   * Optional. Contains information about what needs to be done upon failure
   * (either a permanent error or after it has been retried too many times).
   *
   * @param EnterpriseCrmFrontendsEventbusProtoRollbackStrategy $rollbackStrategy
   */
  public function setRollbackStrategy(EnterpriseCrmFrontendsEventbusProtoRollbackStrategy $rollbackStrategy)
  {
    $this->rollbackStrategy = $rollbackStrategy;
  }
  /**
   * @return EnterpriseCrmFrontendsEventbusProtoRollbackStrategy
   */
  public function getRollbackStrategy()
  {
    return $this->rollbackStrategy;
  }
  /**
   * Determines what action to take upon successful task completion.
   *
   * @param EnterpriseCrmEventbusProtoSuccessPolicy $successPolicy
   */
  public function setSuccessPolicy(EnterpriseCrmEventbusProtoSuccessPolicy $successPolicy)
  {
    $this->successPolicy = $successPolicy;
  }
  /**
   * @return EnterpriseCrmEventbusProtoSuccessPolicy
   */
  public function getSuccessPolicy()
  {
    return $this->successPolicy;
  }
  /**
   * Optional. Determines the number of times the task will be retried on
   * failure and with what retry strategy. This is applicable for synchronous
   * calls to Eventbus alone (Post).
   *
   * @param EnterpriseCrmEventbusProtoFailurePolicy $synchronousCallFailurePolicy
   */
  public function setSynchronousCallFailurePolicy(EnterpriseCrmEventbusProtoFailurePolicy $synchronousCallFailurePolicy)
  {
    $this->synchronousCallFailurePolicy = $synchronousCallFailurePolicy;
  }
  /**
   * @return EnterpriseCrmEventbusProtoFailurePolicy
   */
  public function getSynchronousCallFailurePolicy()
  {
    return $this->synchronousCallFailurePolicy;
  }
  /**
   * Copy of the task entity that this task config is an instance of.
   *
   * @param EnterpriseCrmFrontendsEventbusProtoTaskEntity $taskEntity
   */
  public function setTaskEntity(EnterpriseCrmFrontendsEventbusProtoTaskEntity $taskEntity)
  {
    $this->taskEntity = $taskEntity;
  }
  /**
   * @return EnterpriseCrmFrontendsEventbusProtoTaskEntity
   */
  public function getTaskEntity()
  {
    return $this->taskEntity;
  }
  /**
   * The policy dictating the execution strategy of this task.
   *
   * Accepted values: WHEN_ALL_SUCCEED, WHEN_ANY_SUCCEED,
   * WHEN_ALL_TASKS_AND_CONDITIONS_SUCCEED
   *
   * @param self::TASK_EXECUTION_STRATEGY_* $taskExecutionStrategy
   */
  public function setTaskExecutionStrategy($taskExecutionStrategy)
  {
    $this->taskExecutionStrategy = $taskExecutionStrategy;
  }
  /**
   * @return self::TASK_EXECUTION_STRATEGY_*
   */
  public function getTaskExecutionStrategy()
  {
    return $this->taskExecutionStrategy;
  }
  /**
   * The name for the task.
   *
   * @param string $taskName
   */
  public function setTaskName($taskName)
  {
    $this->taskName = $taskName;
  }
  /**
   * @return string
   */
  public function getTaskName()
  {
    return $this->taskName;
  }
  /**
   * REQUIRED: the identifier of this task within its parent event config,
   * specified by the client. This should be unique among all the tasks belong
   * to the same event config. We use this field as the identifier to find next
   * tasks (via field `next_tasks.task_number`).
   *
   * @param string $taskNumber
   */
  public function setTaskNumber($taskNumber)
  {
    $this->taskNumber = $taskNumber;
  }
  /**
   * @return string
   */
  public function getTaskNumber()
  {
    return $this->taskNumber;
  }
  /**
   * A string template that allows user to configure task parameters (with
   * either literal default values or tokens which will be resolved at execution
   * time) for the task. It will eventually replace the old "parameters" field.
   *
   * @param string $taskSpec
   */
  public function setTaskSpec($taskSpec)
  {
    $this->taskSpec = $taskSpec;
  }
  /**
   * @return string
   */
  public function getTaskSpec()
  {
    return $this->taskSpec;
  }
  /**
   * Used to define task-template name if task is of type task-template
   *
   * @param string $taskTemplateName
   */
  public function setTaskTemplateName($taskTemplateName)
  {
    $this->taskTemplateName = $taskTemplateName;
  }
  /**
   * @return string
   */
  public function getTaskTemplateName()
  {
    return $this->taskTemplateName;
  }
  /**
   * Defines the type of the task
   *
   * Accepted values: TASK, ASIS_TEMPLATE, IO_TEMPLATE
   *
   * @param self::TASK_TYPE_* $taskType
   */
  public function setTaskType($taskType)
  {
    $this->taskType = $taskType;
  }
  /**
   * @return self::TASK_TYPE_*
   */
  public function getTaskType()
  {
    return $this->taskType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseCrmFrontendsEventbusProtoTaskConfig::class, 'Google_Service_Integrations_EnterpriseCrmFrontendsEventbusProtoTaskConfig');
