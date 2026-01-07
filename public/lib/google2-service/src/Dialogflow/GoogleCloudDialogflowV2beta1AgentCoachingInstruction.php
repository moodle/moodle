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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowV2beta1AgentCoachingInstruction extends \Google\Model
{
  /**
   * Optional. The action that human agent should take. For example, "apologize
   * for the slow shipping". If the users only want to use agent coaching for
   * intent detection, agent_action can be empty
   *
   * @var string
   */
  public $agentAction;
  /**
   * Optional. The condition of the instruction. For example, "the customer
   * wants to cancel an order". If the users want the instruction to be
   * triggered unconditionally, the condition can be empty.
   *
   * @var string
   */
  public $condition;
  /**
   * Optional. The detailed description of this instruction.
   *
   * @var string
   */
  public $displayDetails;
  /**
   * Optional. Display name for the instruction.
   *
   * @var string
   */
  public $displayName;
  protected $duplicateCheckResultType = GoogleCloudDialogflowV2beta1AgentCoachingInstructionDuplicateCheckResult::class;
  protected $duplicateCheckResultDataType = '';
  /**
   * Optional. The action that system should take. For example, "call
   * GetOrderTime with order_number={order number provided by the customer}". If
   * the users don't have plugins or don't want to trigger plugins, the
   * system_action can be empty
   *
   * @var string
   */
  public $systemAction;

  /**
   * Optional. The action that human agent should take. For example, "apologize
   * for the slow shipping". If the users only want to use agent coaching for
   * intent detection, agent_action can be empty
   *
   * @param string $agentAction
   */
  public function setAgentAction($agentAction)
  {
    $this->agentAction = $agentAction;
  }
  /**
   * @return string
   */
  public function getAgentAction()
  {
    return $this->agentAction;
  }
  /**
   * Optional. The condition of the instruction. For example, "the customer
   * wants to cancel an order". If the users want the instruction to be
   * triggered unconditionally, the condition can be empty.
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
   * Optional. The detailed description of this instruction.
   *
   * @param string $displayDetails
   */
  public function setDisplayDetails($displayDetails)
  {
    $this->displayDetails = $displayDetails;
  }
  /**
   * @return string
   */
  public function getDisplayDetails()
  {
    return $this->displayDetails;
  }
  /**
   * Optional. Display name for the instruction.
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
   * Output only. Duplication check for the AgentCoachingInstruction.
   *
   * @param GoogleCloudDialogflowV2beta1AgentCoachingInstructionDuplicateCheckResult $duplicateCheckResult
   */
  public function setDuplicateCheckResult(GoogleCloudDialogflowV2beta1AgentCoachingInstructionDuplicateCheckResult $duplicateCheckResult)
  {
    $this->duplicateCheckResult = $duplicateCheckResult;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1AgentCoachingInstructionDuplicateCheckResult
   */
  public function getDuplicateCheckResult()
  {
    return $this->duplicateCheckResult;
  }
  /**
   * Optional. The action that system should take. For example, "call
   * GetOrderTime with order_number={order number provided by the customer}". If
   * the users don't have plugins or don't want to trigger plugins, the
   * system_action can be empty
   *
   * @param string $systemAction
   */
  public function setSystemAction($systemAction)
  {
    $this->systemAction = $systemAction;
  }
  /**
   * @return string
   */
  public function getSystemAction()
  {
    return $this->systemAction;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2beta1AgentCoachingInstruction::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2beta1AgentCoachingInstruction');
