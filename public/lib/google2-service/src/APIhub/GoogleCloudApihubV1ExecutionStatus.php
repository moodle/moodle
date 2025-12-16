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

namespace Google\Service\APIhub;

class GoogleCloudApihubV1ExecutionStatus extends \Google\Model
{
  /**
   * Default unspecified execution state.
   */
  public const CURRENT_EXECUTION_STATE_CURRENT_EXECUTION_STATE_UNSPECIFIED = 'CURRENT_EXECUTION_STATE_UNSPECIFIED';
  /**
   * The plugin instance is executing.
   */
  public const CURRENT_EXECUTION_STATE_RUNNING = 'RUNNING';
  /**
   * The plugin instance is not running an execution.
   */
  public const CURRENT_EXECUTION_STATE_NOT_RUNNING = 'NOT_RUNNING';
  /**
   * Output only. The current state of the execution.
   *
   * @var string
   */
  public $currentExecutionState;
  protected $lastExecutionType = GoogleCloudApihubV1LastExecution::class;
  protected $lastExecutionDataType = '';

  /**
   * Output only. The current state of the execution.
   *
   * Accepted values: CURRENT_EXECUTION_STATE_UNSPECIFIED, RUNNING, NOT_RUNNING
   *
   * @param self::CURRENT_EXECUTION_STATE_* $currentExecutionState
   */
  public function setCurrentExecutionState($currentExecutionState)
  {
    $this->currentExecutionState = $currentExecutionState;
  }
  /**
   * @return self::CURRENT_EXECUTION_STATE_*
   */
  public function getCurrentExecutionState()
  {
    return $this->currentExecutionState;
  }
  /**
   * Output only. The last execution of the plugin instance.
   *
   * @param GoogleCloudApihubV1LastExecution $lastExecution
   */
  public function setLastExecution(GoogleCloudApihubV1LastExecution $lastExecution)
  {
    $this->lastExecution = $lastExecution;
  }
  /**
   * @return GoogleCloudApihubV1LastExecution
   */
  public function getLastExecution()
  {
    return $this->lastExecution;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1ExecutionStatus::class, 'Google_Service_APIhub_GoogleCloudApihubV1ExecutionStatus');
