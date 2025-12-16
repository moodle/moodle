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

class EnterpriseCrmFrontendsEventbusProtoRollbackStrategy extends \Google\Collection
{
  protected $collection_key = 'taskNumbersToRollback';
  protected $parametersType = EnterpriseCrmFrontendsEventbusProtoEventParameters::class;
  protected $parametersDataType = '';
  /**
   * Required. This is the name of the task that needs to be executed upon
   * rollback of this task.
   *
   * @var string
   */
  public $rollbackTaskImplementationClassName;
  /**
   * Required. These are the tasks numbers of the tasks whose
   * `rollback_strategy.rollback_task_implementation_class_name` needs to be
   * executed upon failure of this task.
   *
   * @var string[]
   */
  public $taskNumbersToRollback;

  /**
   * Optional. The customized parameters the user can pass to this task.
   *
   * @param EnterpriseCrmFrontendsEventbusProtoEventParameters $parameters
   */
  public function setParameters(EnterpriseCrmFrontendsEventbusProtoEventParameters $parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return EnterpriseCrmFrontendsEventbusProtoEventParameters
   */
  public function getParameters()
  {
    return $this->parameters;
  }
  /**
   * Required. This is the name of the task that needs to be executed upon
   * rollback of this task.
   *
   * @param string $rollbackTaskImplementationClassName
   */
  public function setRollbackTaskImplementationClassName($rollbackTaskImplementationClassName)
  {
    $this->rollbackTaskImplementationClassName = $rollbackTaskImplementationClassName;
  }
  /**
   * @return string
   */
  public function getRollbackTaskImplementationClassName()
  {
    return $this->rollbackTaskImplementationClassName;
  }
  /**
   * Required. These are the tasks numbers of the tasks whose
   * `rollback_strategy.rollback_task_implementation_class_name` needs to be
   * executed upon failure of this task.
   *
   * @param string[] $taskNumbersToRollback
   */
  public function setTaskNumbersToRollback($taskNumbersToRollback)
  {
    $this->taskNumbersToRollback = $taskNumbersToRollback;
  }
  /**
   * @return string[]
   */
  public function getTaskNumbersToRollback()
  {
    return $this->taskNumbersToRollback;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseCrmFrontendsEventbusProtoRollbackStrategy::class, 'Google_Service_Integrations_EnterpriseCrmFrontendsEventbusProtoRollbackStrategy');
