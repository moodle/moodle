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

class EnterpriseCrmEventbusProtoTaskUiModuleConfig extends \Google\Model
{
  /**
   * Default
   */
  public const MODULE_ID_UNSPECIFIED_TASK_MODULE = 'UNSPECIFIED_TASK_MODULE';
  /**
   * Supports editing label of a task config.
   */
  public const MODULE_ID_LABEL = 'LABEL';
  /**
   * Supports editing error handling settings such as retry strategy.
   */
  public const MODULE_ID_ERROR_HANDLING = 'ERROR_HANDLING';
  /**
   * Supports adding, removing and editing task parameter values in a table with
   * little assistance or restriction.
   */
  public const MODULE_ID_TASK_PARAM_TABLE = 'TASK_PARAM_TABLE';
  /**
   * Supports editing values of declared input parameters of a task. Think of it
   * as a "strongly typed" upgrade to the TASK_PARAM_TABLE.
   */
  public const MODULE_ID_TASK_PARAM_FORM = 'TASK_PARAM_FORM';
  /**
   * Supports editing preconditions of a task config.
   */
  public const MODULE_ID_PRECONDITION = 'PRECONDITION';
  /**
   * Supports adding, editing, and deleting the scripts associated with a script
   * task, as well as modifying the input/output parameters.
   */
  public const MODULE_ID_SCRIPT_EDITOR = 'SCRIPT_EDITOR';
  /**
   * Supports editing task parameters associated with an RPC/stubby task.
   */
  public const MODULE_ID_RPC = 'RPC';
  /**
   * Contains readonly task information, including input/output type info.
   */
  public const MODULE_ID_TASK_SUMMARY = 'TASK_SUMMARY';
  /**
   * Configures a SuspensionTask.
   */
  public const MODULE_ID_SUSPENSION = 'SUSPENSION';
  /**
   * Configures a GenericStubbyTypedTask.
   */
  public const MODULE_ID_RPC_TYPED = 'RPC_TYPED';
  /**
   * Configures a SubWorkflowExecutorTask.
   */
  public const MODULE_ID_SUB_WORKFLOW = 'SUB_WORKFLOW';
  /**
   * Supports navigating to Apps Script editor
   */
  public const MODULE_ID_APPS_SCRIPT_NAVIGATOR = 'APPS_SCRIPT_NAVIGATOR';
  /**
   * Configures a SubWorkflowForEachLoopTask.
   */
  public const MODULE_ID_SUB_WORKFLOW_FOR_EACH_LOOP = 'SUB_WORKFLOW_FOR_EACH_LOOP';
  /**
   * Configures a FieldMappingTask.
   */
  public const MODULE_ID_FIELD_MAPPING = 'FIELD_MAPPING';
  /**
   * Contains embedded in-product documentation for a task.
   */
  public const MODULE_ID_README = 'README';
  /**
   * UI widget for the rest caller task.
   */
  public const MODULE_ID_REST_CALLER = 'REST_CALLER';
  /**
   * Configures a SubWorkflowScatterGatherTask.
   */
  public const MODULE_ID_SUB_WORKFLOW_SCATTER_GATHER = 'SUB_WORKFLOW_SCATTER_GATHER';
  /**
   * Configures a CloudSql Task.
   */
  public const MODULE_ID_CLOUD_SQL = 'CLOUD_SQL';
  /**
   * Configure a GenericConnectorTask.
   */
  public const MODULE_ID_GENERIC_CONNECTOR_TASK = 'GENERIC_CONNECTOR_TASK';
  /**
   * ID of the config module.
   *
   * @var string
   */
  public $moduleId;

  /**
   * ID of the config module.
   *
   * Accepted values: UNSPECIFIED_TASK_MODULE, LABEL, ERROR_HANDLING,
   * TASK_PARAM_TABLE, TASK_PARAM_FORM, PRECONDITION, SCRIPT_EDITOR, RPC,
   * TASK_SUMMARY, SUSPENSION, RPC_TYPED, SUB_WORKFLOW, APPS_SCRIPT_NAVIGATOR,
   * SUB_WORKFLOW_FOR_EACH_LOOP, FIELD_MAPPING, README, REST_CALLER,
   * SUB_WORKFLOW_SCATTER_GATHER, CLOUD_SQL, GENERIC_CONNECTOR_TASK
   *
   * @param self::MODULE_ID_* $moduleId
   */
  public function setModuleId($moduleId)
  {
    $this->moduleId = $moduleId;
  }
  /**
   * @return self::MODULE_ID_*
   */
  public function getModuleId()
  {
    return $this->moduleId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseCrmEventbusProtoTaskUiModuleConfig::class, 'Google_Service_Integrations_EnterpriseCrmEventbusProtoTaskUiModuleConfig');
