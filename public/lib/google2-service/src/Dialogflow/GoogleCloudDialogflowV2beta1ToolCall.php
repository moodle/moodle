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

class GoogleCloudDialogflowV2beta1ToolCall extends \Google\Model
{
  /**
   * Default value.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The tool call has been triggered.
   */
  public const STATE_TRIGGERED = 'TRIGGERED';
  /**
   * The tool call requires confirmation from a human.
   */
  public const STATE_NEEDS_CONFIRMATION = 'NEEDS_CONFIRMATION';
  /**
   * Optional. The name of the tool's action associated with this call.
   *
   * @var string
   */
  public $action;
  /**
   * Optional. The answer record associated with this tool call.
   *
   * @var string
   */
  public $answerRecord;
  /**
   * Output only. Create time of the tool call.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. The action's input parameters.
   *
   * @var array[]
   */
  public $inputParameters;
  /**
   * Output only. State of the tool call
   *
   * @var string
   */
  public $state;
  /**
   * Optional. The tool associated with this call. Format:
   * `projects//locations//tools/`.
   *
   * @var string
   */
  public $tool;
  /**
   * Optional. A human readable description of the tool.
   *
   * @var string
   */
  public $toolDisplayDetails;
  /**
   * Optional. A human readable short name of the tool, to be shown on the UI.
   *
   * @var string
   */
  public $toolDisplayName;

  /**
   * Optional. The name of the tool's action associated with this call.
   *
   * @param string $action
   */
  public function setAction($action)
  {
    $this->action = $action;
  }
  /**
   * @return string
   */
  public function getAction()
  {
    return $this->action;
  }
  /**
   * Optional. The answer record associated with this tool call.
   *
   * @param string $answerRecord
   */
  public function setAnswerRecord($answerRecord)
  {
    $this->answerRecord = $answerRecord;
  }
  /**
   * @return string
   */
  public function getAnswerRecord()
  {
    return $this->answerRecord;
  }
  /**
   * Output only. Create time of the tool call.
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
   * Optional. The action's input parameters.
   *
   * @param array[] $inputParameters
   */
  public function setInputParameters($inputParameters)
  {
    $this->inputParameters = $inputParameters;
  }
  /**
   * @return array[]
   */
  public function getInputParameters()
  {
    return $this->inputParameters;
  }
  /**
   * Output only. State of the tool call
   *
   * Accepted values: STATE_UNSPECIFIED, TRIGGERED, NEEDS_CONFIRMATION
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Optional. The tool associated with this call. Format:
   * `projects//locations//tools/`.
   *
   * @param string $tool
   */
  public function setTool($tool)
  {
    $this->tool = $tool;
  }
  /**
   * @return string
   */
  public function getTool()
  {
    return $this->tool;
  }
  /**
   * Optional. A human readable description of the tool.
   *
   * @param string $toolDisplayDetails
   */
  public function setToolDisplayDetails($toolDisplayDetails)
  {
    $this->toolDisplayDetails = $toolDisplayDetails;
  }
  /**
   * @return string
   */
  public function getToolDisplayDetails()
  {
    return $this->toolDisplayDetails;
  }
  /**
   * Optional. A human readable short name of the tool, to be shown on the UI.
   *
   * @param string $toolDisplayName
   */
  public function setToolDisplayName($toolDisplayName)
  {
    $this->toolDisplayName = $toolDisplayName;
  }
  /**
   * @return string
   */
  public function getToolDisplayName()
  {
    return $this->toolDisplayName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2beta1ToolCall::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2beta1ToolCall');
