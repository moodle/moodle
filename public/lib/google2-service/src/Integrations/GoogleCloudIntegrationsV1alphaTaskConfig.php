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

class GoogleCloudIntegrationsV1alphaTaskConfig extends \Google\Collection
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
  public const JSON_VALIDATION_OPTION_JSON_VALIDATION_OPTION_UNSPECIFIED = 'JSON_VALIDATION_OPTION_UNSPECIFIED';
  /**
   * Do not run any validation against JSON schemas.
   */
  public const JSON_VALIDATION_OPTION_SKIP = 'SKIP';
  /**
   * Validate all potential input JSON parameters against schemas specified in
   * IntegrationParameter.
   */
  public const JSON_VALIDATION_OPTION_PRE_EXECUTION = 'PRE_EXECUTION';
  /**
   * Validate all potential output JSON parameters against schemas specified in
   * IntegrationParameter.
   */
  public const JSON_VALIDATION_OPTION_POST_EXECUTION = 'POST_EXECUTION';
  /**
   * Perform both PRE_EXECUTION and POST_EXECUTION validations.
   */
  public const JSON_VALIDATION_OPTION_PRE_POST_EXECUTION = 'PRE_POST_EXECUTION';
  /**
   * Default.
   */
  public const NEXT_TASKS_EXECUTION_POLICY_NEXT_TASKS_EXECUTION_POLICY_UNSPECIFIED = 'NEXT_TASKS_EXECUTION_POLICY_UNSPECIFIED';
  /**
   * Execute all the tasks that satisfy their associated condition.
   */
  public const NEXT_TASKS_EXECUTION_POLICY_RUN_ALL_MATCH = 'RUN_ALL_MATCH';
  /**
   * Execute the first task that satisfies the associated condition.
   */
  public const NEXT_TASKS_EXECUTION_POLICY_RUN_FIRST_MATCH = 'RUN_FIRST_MATCH';
  /**
   * Default. If the strategy is not set explicitly, it will default to
   * `WHEN_ALL_SUCCEED`.
   */
  public const TASK_EXECUTION_STRATEGY_TASK_EXECUTION_STRATEGY_UNSPECIFIED = 'TASK_EXECUTION_STRATEGY_UNSPECIFIED';
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
  protected $collection_key = 'nextTasks';
  protected $conditionalFailurePoliciesType = GoogleCloudIntegrationsV1alphaConditionalFailurePolicies::class;
  protected $conditionalFailurePoliciesDataType = '';
  /**
   * Optional. User-provided description intended to give additional business
   * context about the task.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. User-provided label that is attached to this TaskConfig in the
   * UI.
   *
   * @var string
   */
  public $displayName;
  /**
   * Optional. Optional Error catcher id of the error catch flow which will be
   * executed when execution error happens in the task
   *
   * @var string
   */
  public $errorCatcherId;
  /**
   * Optional. External task type of the task
   *
   * @var string
   */
  public $externalTaskType;
  protected $failurePolicyType = GoogleCloudIntegrationsV1alphaFailurePolicy::class;
  protected $failurePolicyDataType = '';
  /**
   * Optional. If set, overrides the option configured in the Task
   * implementation class.
   *
   * @var string
   */
  public $jsonValidationOption;
  protected $nextTasksType = GoogleCloudIntegrationsV1alphaNextTask::class;
  protected $nextTasksDataType = 'array';
  /**
   * Optional. The policy dictating the execution of the next set of tasks for
   * the current task.
   *
   * @var string
   */
  public $nextTasksExecutionPolicy;
  protected $parametersType = GoogleCloudIntegrationsV1alphaEventParameter::class;
  protected $parametersDataType = 'map';
  protected $positionType = GoogleCloudIntegrationsV1alphaCoordinate::class;
  protected $positionDataType = '';
  protected $successPolicyType = GoogleCloudIntegrationsV1alphaSuccessPolicy::class;
  protected $successPolicyDataType = '';
  protected $synchronousCallFailurePolicyType = GoogleCloudIntegrationsV1alphaFailurePolicy::class;
  protected $synchronousCallFailurePolicyDataType = '';
  /**
   * Optional. The name for the task.
   *
   * @var string
   */
  public $task;
  /**
   * Optional. The policy dictating the execution strategy of this task.
   *
   * @var string
   */
  public $taskExecutionStrategy;
  /**
   * Required. The identifier of this task within its parent event config,
   * specified by the client. This should be unique among all the tasks belong
   * to the same event config. We use this field as the identifier to find next
   * tasks (via field `next_tasks.task_id`).
   *
   * @var string
   */
  public $taskId;
  /**
   * Optional. Used to define task-template name if task is of type task-
   * template
   *
   * @var string
   */
  public $taskTemplate;

  /**
   * Optional. The list of conditional failure policies that will be applied to
   * the task in order.
   *
   * @param GoogleCloudIntegrationsV1alphaConditionalFailurePolicies $conditionalFailurePolicies
   */
  public function setConditionalFailurePolicies(GoogleCloudIntegrationsV1alphaConditionalFailurePolicies $conditionalFailurePolicies)
  {
    $this->conditionalFailurePolicies = $conditionalFailurePolicies;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaConditionalFailurePolicies
   */
  public function getConditionalFailurePolicies()
  {
    return $this->conditionalFailurePolicies;
  }
  /**
   * Optional. User-provided description intended to give additional business
   * context about the task.
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
   * Optional. User-provided label that is attached to this TaskConfig in the
   * UI.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Optional. Optional Error catcher id of the error catch flow which will be
   * executed when execution error happens in the task
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
   * Optional. External task type of the task
   *
   * Accepted values: EXTERNAL_TASK_TYPE_UNSPECIFIED, NORMAL_TASK, ERROR_TASK
   *
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
   * @param GoogleCloudIntegrationsV1alphaFailurePolicy $failurePolicy
   */
  public function setFailurePolicy(GoogleCloudIntegrationsV1alphaFailurePolicy $failurePolicy)
  {
    $this->failurePolicy = $failurePolicy;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaFailurePolicy
   */
  public function getFailurePolicy()
  {
    return $this->failurePolicy;
  }
  /**
   * Optional. If set, overrides the option configured in the Task
   * implementation class.
   *
   * Accepted values: JSON_VALIDATION_OPTION_UNSPECIFIED, SKIP, PRE_EXECUTION,
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
   * Optional. The set of tasks that are next in line to be executed as per the
   * execution graph defined for the parent event, specified by
   * `event_config_id`. Each of these next tasks are executed only if the
   * condition associated with them evaluates to true.
   *
   * @param GoogleCloudIntegrationsV1alphaNextTask[] $nextTasks
   */
  public function setNextTasks($nextTasks)
  {
    $this->nextTasks = $nextTasks;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaNextTask[]
   */
  public function getNextTasks()
  {
    return $this->nextTasks;
  }
  /**
   * Optional. The policy dictating the execution of the next set of tasks for
   * the current task.
   *
   * Accepted values: NEXT_TASKS_EXECUTION_POLICY_UNSPECIFIED, RUN_ALL_MATCH,
   * RUN_FIRST_MATCH
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
   * Optional. The customized parameters the user can pass to this task.
   *
   * @param GoogleCloudIntegrationsV1alphaEventParameter[] $parameters
   */
  public function setParameters($parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaEventParameter[]
   */
  public function getParameters()
  {
    return $this->parameters;
  }
  /**
   * Optional. Informs the front-end application where to draw this error
   * catcher config on the UI.
   *
   * @param GoogleCloudIntegrationsV1alphaCoordinate $position
   */
  public function setPosition(GoogleCloudIntegrationsV1alphaCoordinate $position)
  {
    $this->position = $position;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaCoordinate
   */
  public function getPosition()
  {
    return $this->position;
  }
  /**
   * Optional. Determines what action to take upon successful task completion.
   *
   * @param GoogleCloudIntegrationsV1alphaSuccessPolicy $successPolicy
   */
  public function setSuccessPolicy(GoogleCloudIntegrationsV1alphaSuccessPolicy $successPolicy)
  {
    $this->successPolicy = $successPolicy;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaSuccessPolicy
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
   * @param GoogleCloudIntegrationsV1alphaFailurePolicy $synchronousCallFailurePolicy
   */
  public function setSynchronousCallFailurePolicy(GoogleCloudIntegrationsV1alphaFailurePolicy $synchronousCallFailurePolicy)
  {
    $this->synchronousCallFailurePolicy = $synchronousCallFailurePolicy;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaFailurePolicy
   */
  public function getSynchronousCallFailurePolicy()
  {
    return $this->synchronousCallFailurePolicy;
  }
  /**
   * Optional. The name for the task.
   *
   * @param string $task
   */
  public function setTask($task)
  {
    $this->task = $task;
  }
  /**
   * @return string
   */
  public function getTask()
  {
    return $this->task;
  }
  /**
   * Optional. The policy dictating the execution strategy of this task.
   *
   * Accepted values: TASK_EXECUTION_STRATEGY_UNSPECIFIED, WHEN_ALL_SUCCEED,
   * WHEN_ANY_SUCCEED, WHEN_ALL_TASKS_AND_CONDITIONS_SUCCEED
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
   * Required. The identifier of this task within its parent event config,
   * specified by the client. This should be unique among all the tasks belong
   * to the same event config. We use this field as the identifier to find next
   * tasks (via field `next_tasks.task_id`).
   *
   * @param string $taskId
   */
  public function setTaskId($taskId)
  {
    $this->taskId = $taskId;
  }
  /**
   * @return string
   */
  public function getTaskId()
  {
    return $this->taskId;
  }
  /**
   * Optional. Used to define task-template name if task is of type task-
   * template
   *
   * @param string $taskTemplate
   */
  public function setTaskTemplate($taskTemplate)
  {
    $this->taskTemplate = $taskTemplate;
  }
  /**
   * @return string
   */
  public function getTaskTemplate()
  {
    return $this->taskTemplate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudIntegrationsV1alphaTaskConfig::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaTaskConfig');
