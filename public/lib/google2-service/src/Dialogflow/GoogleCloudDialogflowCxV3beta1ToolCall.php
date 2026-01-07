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

class GoogleCloudDialogflowCxV3beta1ToolCall extends \Google\Model
{
  /**
   * Required. The name of the tool's action associated with this call.
   *
   * @var string
   */
  public $action;
  /**
   * Optional. The action's input parameters.
   *
   * @var array[]
   */
  public $inputParameters;
  /**
   * Required. The tool associated with this call. Format:
   * `projects//locations//agents//tools/`.
   *
   * @var string
   */
  public $tool;

  /**
   * Required. The name of the tool's action associated with this call.
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
   * Required. The tool associated with this call. Format:
   * `projects//locations//agents//tools/`.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3beta1ToolCall::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3beta1ToolCall');
