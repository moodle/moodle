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

class GoogleCloudDialogflowV2GeneratorSuggestionToolCallInfo extends \Google\Model
{
  protected $toolCallType = GoogleCloudDialogflowV2ToolCall::class;
  protected $toolCallDataType = '';
  protected $toolCallResultType = GoogleCloudDialogflowV2ToolCallResult::class;
  protected $toolCallResultDataType = '';

  /**
   * Required. Request for a tool call.
   *
   * @param GoogleCloudDialogflowV2ToolCall $toolCall
   */
  public function setToolCall(GoogleCloudDialogflowV2ToolCall $toolCall)
  {
    $this->toolCall = $toolCall;
  }
  /**
   * @return GoogleCloudDialogflowV2ToolCall
   */
  public function getToolCall()
  {
    return $this->toolCall;
  }
  /**
   * Required. Response for a tool call.
   *
   * @param GoogleCloudDialogflowV2ToolCallResult $toolCallResult
   */
  public function setToolCallResult(GoogleCloudDialogflowV2ToolCallResult $toolCallResult)
  {
    $this->toolCallResult = $toolCallResult;
  }
  /**
   * @return GoogleCloudDialogflowV2ToolCallResult
   */
  public function getToolCallResult()
  {
    return $this->toolCallResult;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2GeneratorSuggestionToolCallInfo::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2GeneratorSuggestionToolCallInfo');
