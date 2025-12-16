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

class EnterpriseCrmFrontendsEventbusProtoTriggerConfig extends \Google\Collection
{
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
  public const TRIGGER_TYPE_UNKNOWN = 'UNKNOWN';
  public const TRIGGER_TYPE_CLOUD_PUBSUB = 'CLOUD_PUBSUB';
  public const TRIGGER_TYPE_GOOPS = 'GOOPS';
  public const TRIGGER_TYPE_SFDC_SYNC = 'SFDC_SYNC';
  public const TRIGGER_TYPE_CRON = 'CRON';
  public const TRIGGER_TYPE_API = 'API';
  public const TRIGGER_TYPE_MANIFOLD_TRIGGER = 'MANIFOLD_TRIGGER';
  public const TRIGGER_TYPE_DATALAYER_DATA_CHANGE = 'DATALAYER_DATA_CHANGE';
  public const TRIGGER_TYPE_SFDC_CHANNEL = 'SFDC_CHANNEL';
  public const TRIGGER_TYPE_CLOUD_PUBSUB_EXTERNAL = 'CLOUD_PUBSUB_EXTERNAL';
  public const TRIGGER_TYPE_SFDC_CDC_CHANNEL = 'SFDC_CDC_CHANNEL';
  public const TRIGGER_TYPE_SFDC_PLATFORM_EVENTS_CHANNEL = 'SFDC_PLATFORM_EVENTS_CHANNEL';
  public const TRIGGER_TYPE_CLOUD_SCHEDULER = 'CLOUD_SCHEDULER';
  public const TRIGGER_TYPE_INTEGRATION_CONNECTOR_TRIGGER = 'INTEGRATION_CONNECTOR_TRIGGER';
  public const TRIGGER_TYPE_PRIVATE_TRIGGER = 'PRIVATE_TRIGGER';
  public const TRIGGER_TYPE_EVENTARC_TRIGGER = 'EVENTARC_TRIGGER';
  protected $collection_key = 'startTasks';
  protected $alertConfigType = EnterpriseCrmEventbusProtoWorkflowAlertConfig::class;
  protected $alertConfigDataType = 'array';
  protected $cloudSchedulerConfigType = EnterpriseCrmEventbusProtoCloudSchedulerConfig::class;
  protected $cloudSchedulerConfigDataType = '';
  /**
   * User-provided description intended to give more business context about the
   * task.
   *
   * @var string
   */
  public $description;
  /**
   * Required. The list of client ids which are enabled to execute the workflow
   * using this trigger. In other words, these clients have the workflow
   * execution privledges for this trigger. For API trigger, the client id in
   * the incoming request is validated against the list of enabled clients. For
   * non-API triggers, one workflow execution is triggered on behalf of each
   * enabled client.
   *
   * @var string[]
   */
  public $enabledClients;
  /**
   * Optional Error catcher id of the error catch flow which will be executed
   * when execution error happens in the task
   *
   * @var string
   */
  public $errorCatcherId;
  protected $inputVariablesType = EnterpriseCrmFrontendsEventbusProtoTriggerConfigVariables::class;
  protected $inputVariablesDataType = '';
  /**
   * The user created label for a particular trigger.
   *
   * @var string
   */
  public $label;
  /**
   * Dictates how next tasks will be executed.
   *
   * @var string
   */
  public $nextTasksExecutionPolicy;
  protected $outputVariablesType = EnterpriseCrmFrontendsEventbusProtoTriggerConfigVariables::class;
  protected $outputVariablesDataType = '';
  /**
   * Optional. If set to true, any upcoming requests for this trigger config
   * will be paused and the executions will be resumed later when the flag is
   * reset. The workflow to which this trigger config belongs has to be in
   * ACTIVE status for the executions to be paused or resumed.
   *
   * @var bool
   */
  public $pauseWorkflowExecutions;
  protected $positionType = EnterpriseCrmEventbusProtoCoordinate::class;
  protected $positionDataType = '';
  /**
   * Configurable properties of the trigger, not to be confused with workflow
   * parameters. E.g. "name" is a property for API triggers and "subscription"
   * is a property for Cloud Pubsub triggers.
   *
   * @var string[]
   */
  public $properties;
  protected $startTasksType = EnterpriseCrmEventbusProtoNextTask::class;
  protected $startTasksDataType = 'array';
  protected $triggerCriteriaType = EnterpriseCrmEventbusProtoTriggerCriteria::class;
  protected $triggerCriteriaDataType = '';
  /**
   * The backend trigger ID.
   *
   * @var string
   */
  public $triggerId;
  /**
   * Optional. Name of the trigger This is added to identify the type of
   * trigger. This is avoid the logic on triggerId to identify the trigger_type
   * and push the same to monitoring.
   *
   * @var string
   */
  public $triggerName;
  /**
   * Required. A number to uniquely identify each trigger config within the
   * workflow on UI.
   *
   * @var string
   */
  public $triggerNumber;
  /**
   * @var string
   */
  public $triggerType;

  /**
   * An alert threshold configuration for the [trigger + client + workflow]
   * tuple. If these values are not specified in the trigger config, default
   * values will be populated by the system. Note that there must be exactly one
   * alert threshold configured per [client + trigger + workflow] when
   * published.
   *
   * @param EnterpriseCrmEventbusProtoWorkflowAlertConfig[] $alertConfig
   */
  public function setAlertConfig($alertConfig)
  {
    $this->alertConfig = $alertConfig;
  }
  /**
   * @return EnterpriseCrmEventbusProtoWorkflowAlertConfig[]
   */
  public function getAlertConfig()
  {
    return $this->alertConfig;
  }
  /**
   * @param EnterpriseCrmEventbusProtoCloudSchedulerConfig $cloudSchedulerConfig
   */
  public function setCloudSchedulerConfig(EnterpriseCrmEventbusProtoCloudSchedulerConfig $cloudSchedulerConfig)
  {
    $this->cloudSchedulerConfig = $cloudSchedulerConfig;
  }
  /**
   * @return EnterpriseCrmEventbusProtoCloudSchedulerConfig
   */
  public function getCloudSchedulerConfig()
  {
    return $this->cloudSchedulerConfig;
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
   * Required. The list of client ids which are enabled to execute the workflow
   * using this trigger. In other words, these clients have the workflow
   * execution privledges for this trigger. For API trigger, the client id in
   * the incoming request is validated against the list of enabled clients. For
   * non-API triggers, one workflow execution is triggered on behalf of each
   * enabled client.
   *
   * @param string[] $enabledClients
   */
  public function setEnabledClients($enabledClients)
  {
    $this->enabledClients = $enabledClients;
  }
  /**
   * @return string[]
   */
  public function getEnabledClients()
  {
    return $this->enabledClients;
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
   * Optional. List of input variables for the api trigger.
   *
   * @param EnterpriseCrmFrontendsEventbusProtoTriggerConfigVariables $inputVariables
   */
  public function setInputVariables(EnterpriseCrmFrontendsEventbusProtoTriggerConfigVariables $inputVariables)
  {
    $this->inputVariables = $inputVariables;
  }
  /**
   * @return EnterpriseCrmFrontendsEventbusProtoTriggerConfigVariables
   */
  public function getInputVariables()
  {
    return $this->inputVariables;
  }
  /**
   * The user created label for a particular trigger.
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
   * Dictates how next tasks will be executed.
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
   * Optional. List of output variables for the api trigger.
   *
   * @param EnterpriseCrmFrontendsEventbusProtoTriggerConfigVariables $outputVariables
   */
  public function setOutputVariables(EnterpriseCrmFrontendsEventbusProtoTriggerConfigVariables $outputVariables)
  {
    $this->outputVariables = $outputVariables;
  }
  /**
   * @return EnterpriseCrmFrontendsEventbusProtoTriggerConfigVariables
   */
  public function getOutputVariables()
  {
    return $this->outputVariables;
  }
  /**
   * Optional. If set to true, any upcoming requests for this trigger config
   * will be paused and the executions will be resumed later when the flag is
   * reset. The workflow to which this trigger config belongs has to be in
   * ACTIVE status for the executions to be paused or resumed.
   *
   * @param bool $pauseWorkflowExecutions
   */
  public function setPauseWorkflowExecutions($pauseWorkflowExecutions)
  {
    $this->pauseWorkflowExecutions = $pauseWorkflowExecutions;
  }
  /**
   * @return bool
   */
  public function getPauseWorkflowExecutions()
  {
    return $this->pauseWorkflowExecutions;
  }
  /**
   * Optional. Informs the front-end application where to draw this trigger
   * config on the UI.
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
   * Configurable properties of the trigger, not to be confused with workflow
   * parameters. E.g. "name" is a property for API triggers and "subscription"
   * is a property for Cloud Pubsub triggers.
   *
   * @param string[] $properties
   */
  public function setProperties($properties)
  {
    $this->properties = $properties;
  }
  /**
   * @return string[]
   */
  public function getProperties()
  {
    return $this->properties;
  }
  /**
   * Set of tasks numbers from where the workflow execution is started by this
   * trigger. If this is empty, then workflow is executed with default start
   * tasks. In the list of start tasks, none of two tasks can have direct
   * ancestor-descendant relationships (i.e. in a same workflow execution
   * graph).
   *
   * @param EnterpriseCrmEventbusProtoNextTask[] $startTasks
   */
  public function setStartTasks($startTasks)
  {
    $this->startTasks = $startTasks;
  }
  /**
   * @return EnterpriseCrmEventbusProtoNextTask[]
   */
  public function getStartTasks()
  {
    return $this->startTasks;
  }
  /**
   * Optional. When set, Eventbus will run the task specified in the
   * trigger_criteria and validate the result using the
   * trigger_criteria.condition, and only execute the workflow when result is
   * true.
   *
   * @param EnterpriseCrmEventbusProtoTriggerCriteria $triggerCriteria
   */
  public function setTriggerCriteria(EnterpriseCrmEventbusProtoTriggerCriteria $triggerCriteria)
  {
    $this->triggerCriteria = $triggerCriteria;
  }
  /**
   * @return EnterpriseCrmEventbusProtoTriggerCriteria
   */
  public function getTriggerCriteria()
  {
    return $this->triggerCriteria;
  }
  /**
   * The backend trigger ID.
   *
   * @param string $triggerId
   */
  public function setTriggerId($triggerId)
  {
    $this->triggerId = $triggerId;
  }
  /**
   * @return string
   */
  public function getTriggerId()
  {
    return $this->triggerId;
  }
  /**
   * Optional. Name of the trigger This is added to identify the type of
   * trigger. This is avoid the logic on triggerId to identify the trigger_type
   * and push the same to monitoring.
   *
   * @param string $triggerName
   */
  public function setTriggerName($triggerName)
  {
    $this->triggerName = $triggerName;
  }
  /**
   * @return string
   */
  public function getTriggerName()
  {
    return $this->triggerName;
  }
  /**
   * Required. A number to uniquely identify each trigger config within the
   * workflow on UI.
   *
   * @param string $triggerNumber
   */
  public function setTriggerNumber($triggerNumber)
  {
    $this->triggerNumber = $triggerNumber;
  }
  /**
   * @return string
   */
  public function getTriggerNumber()
  {
    return $this->triggerNumber;
  }
  /**
   * @param self::TRIGGER_TYPE_* $triggerType
   */
  public function setTriggerType($triggerType)
  {
    $this->triggerType = $triggerType;
  }
  /**
   * @return self::TRIGGER_TYPE_*
   */
  public function getTriggerType()
  {
    return $this->triggerType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseCrmFrontendsEventbusProtoTriggerConfig::class, 'Google_Service_Integrations_EnterpriseCrmFrontendsEventbusProtoTriggerConfig');
