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

class EnterpriseCrmEventbusProtoTriggerCriteria extends \Google\Model
{
  /**
   * Required. Standard filter expression, when true the workflow will be
   * executed. If there's no trigger_criteria_task_implementation_class_name
   * specified, the condition will be validated directly.
   *
   * @var string
   */
  public $condition;
  protected $parametersType = EnterpriseCrmEventbusProtoEventParameters::class;
  protected $parametersDataType = '';
  /**
   * Optional. Implementation class name. The class should implement the
   * “TypedTask” interface.
   *
   * @var string
   */
  public $triggerCriteriaTaskImplementationClassName;

  /**
   * Required. Standard filter expression, when true the workflow will be
   * executed. If there's no trigger_criteria_task_implementation_class_name
   * specified, the condition will be validated directly.
   *
   * @param string $condition
   */
  public function setCondition($condition)
  {
    $this->condition = $condition;
  }
  /**
   * @return string
   */
  public function getCondition()
  {
    return $this->condition;
  }
  /**
   * Optional. To be used in TaskConfig for the implementation class.
   *
   * @param EnterpriseCrmEventbusProtoEventParameters $parameters
   */
  public function setParameters(EnterpriseCrmEventbusProtoEventParameters $parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return EnterpriseCrmEventbusProtoEventParameters
   */
  public function getParameters()
  {
    return $this->parameters;
  }
  /**
   * Optional. Implementation class name. The class should implement the
   * “TypedTask” interface.
   *
   * @param string $triggerCriteriaTaskImplementationClassName
   */
  public function setTriggerCriteriaTaskImplementationClassName($triggerCriteriaTaskImplementationClassName)
  {
    $this->triggerCriteriaTaskImplementationClassName = $triggerCriteriaTaskImplementationClassName;
  }
  /**
   * @return string
   */
  public function getTriggerCriteriaTaskImplementationClassName()
  {
    return $this->triggerCriteriaTaskImplementationClassName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseCrmEventbusProtoTriggerCriteria::class, 'Google_Service_Integrations_EnterpriseCrmEventbusProtoTriggerCriteria');
