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

namespace Google\Service\WorkloadManager;

class SapInstanceProperties extends \Google\Collection
{
  protected $collection_key = 'numbers';
  protected $agentStatesType = AgentStates::class;
  protected $agentStatesDataType = '';
  /**
   * Optional. SAP Instance numbers. They are from '00' to '99'.
   *
   * @var string[]
   */
  public $numbers;

  /**
   * Optional. Sap Instance Agent status.
   *
   * @param AgentStates $agentStates
   */
  public function setAgentStates(AgentStates $agentStates)
  {
    $this->agentStates = $agentStates;
  }
  /**
   * @return AgentStates
   */
  public function getAgentStates()
  {
    return $this->agentStates;
  }
  /**
   * Optional. SAP Instance numbers. They are from '00' to '99'.
   *
   * @param string[] $numbers
   */
  public function setNumbers($numbers)
  {
    $this->numbers = $numbers;
  }
  /**
   * @return string[]
   */
  public function getNumbers()
  {
    return $this->numbers;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SapInstanceProperties::class, 'Google_Service_WorkloadManager_SapInstanceProperties');
