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

class GoogleCloudDialogflowCxV3Action extends \Google\Model
{
  protected $agentUtteranceType = GoogleCloudDialogflowCxV3AgentUtterance::class;
  protected $agentUtteranceDataType = '';
  protected $flowInvocationType = GoogleCloudDialogflowCxV3FlowInvocation::class;
  protected $flowInvocationDataType = '';
  protected $flowTransitionType = GoogleCloudDialogflowCxV3FlowTransition::class;
  protected $flowTransitionDataType = '';
  protected $playbookInvocationType = GoogleCloudDialogflowCxV3PlaybookInvocation::class;
  protected $playbookInvocationDataType = '';
  protected $playbookTransitionType = GoogleCloudDialogflowCxV3PlaybookTransition::class;
  protected $playbookTransitionDataType = '';
  protected $toolUseType = GoogleCloudDialogflowCxV3ToolUse::class;
  protected $toolUseDataType = '';
  protected $userUtteranceType = GoogleCloudDialogflowCxV3UserUtterance::class;
  protected $userUtteranceDataType = '';

  /**
   * Optional. Action performed by the agent as a message.
   *
   * @param GoogleCloudDialogflowCxV3AgentUtterance $agentUtterance
   */
  public function setAgentUtterance(GoogleCloudDialogflowCxV3AgentUtterance $agentUtterance)
  {
    $this->agentUtterance = $agentUtterance;
  }
  /**
   * @return GoogleCloudDialogflowCxV3AgentUtterance
   */
  public function getAgentUtterance()
  {
    return $this->agentUtterance;
  }
  /**
   * Optional. Action performed on behalf of the agent by invoking a CX flow.
   *
   * @param GoogleCloudDialogflowCxV3FlowInvocation $flowInvocation
   */
  public function setFlowInvocation(GoogleCloudDialogflowCxV3FlowInvocation $flowInvocation)
  {
    $this->flowInvocation = $flowInvocation;
  }
  /**
   * @return GoogleCloudDialogflowCxV3FlowInvocation
   */
  public function getFlowInvocation()
  {
    return $this->flowInvocation;
  }
  /**
   * Optional. Action performed on behalf of the agent by transitioning to a
   * target CX flow.
   *
   * @param GoogleCloudDialogflowCxV3FlowTransition $flowTransition
   */
  public function setFlowTransition(GoogleCloudDialogflowCxV3FlowTransition $flowTransition)
  {
    $this->flowTransition = $flowTransition;
  }
  /**
   * @return GoogleCloudDialogflowCxV3FlowTransition
   */
  public function getFlowTransition()
  {
    return $this->flowTransition;
  }
  /**
   * Optional. Action performed on behalf of the agent by invoking a child
   * playbook.
   *
   * @param GoogleCloudDialogflowCxV3PlaybookInvocation $playbookInvocation
   */
  public function setPlaybookInvocation(GoogleCloudDialogflowCxV3PlaybookInvocation $playbookInvocation)
  {
    $this->playbookInvocation = $playbookInvocation;
  }
  /**
   * @return GoogleCloudDialogflowCxV3PlaybookInvocation
   */
  public function getPlaybookInvocation()
  {
    return $this->playbookInvocation;
  }
  /**
   * Optional. Action performed on behalf of the agent by transitioning to a
   * target playbook.
   *
   * @param GoogleCloudDialogflowCxV3PlaybookTransition $playbookTransition
   */
  public function setPlaybookTransition(GoogleCloudDialogflowCxV3PlaybookTransition $playbookTransition)
  {
    $this->playbookTransition = $playbookTransition;
  }
  /**
   * @return GoogleCloudDialogflowCxV3PlaybookTransition
   */
  public function getPlaybookTransition()
  {
    return $this->playbookTransition;
  }
  /**
   * Optional. Action performed on behalf of the agent by calling a plugin tool.
   *
   * @param GoogleCloudDialogflowCxV3ToolUse $toolUse
   */
  public function setToolUse(GoogleCloudDialogflowCxV3ToolUse $toolUse)
  {
    $this->toolUse = $toolUse;
  }
  /**
   * @return GoogleCloudDialogflowCxV3ToolUse
   */
  public function getToolUse()
  {
    return $this->toolUse;
  }
  /**
   * Optional. Agent obtained a message from the customer.
   *
   * @param GoogleCloudDialogflowCxV3UserUtterance $userUtterance
   */
  public function setUserUtterance(GoogleCloudDialogflowCxV3UserUtterance $userUtterance)
  {
    $this->userUtterance = $userUtterance;
  }
  /**
   * @return GoogleCloudDialogflowCxV3UserUtterance
   */
  public function getUserUtterance()
  {
    return $this->userUtterance;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3Action::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3Action');
