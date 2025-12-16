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

class GoogleCloudDialogflowV2ToolCallResult extends \Google\Model
{
  /**
   * Optional. The name of the tool's action associated with this call.
   *
   * @var string
   */
  public $action;
  /**
   * Optional. The answer record associated with this tool call result.
   *
   * @var string
   */
  public $answerRecord;
  /**
   * Only populated if the response content is utf-8 encoded.
   *
   * @var string
   */
  public $content;
  /**
   * Output only. Create time of the tool call result.
   *
   * @var string
   */
  public $createTime;
  protected $errorType = GoogleCloudDialogflowV2ToolCallResultError::class;
  protected $errorDataType = '';
  /**
   * Only populated if the response content is not utf-8 encoded. (by definition
   * byte fields are base64 encoded).
   *
   * @var string
   */
  public $rawContent;
  /**
   * Optional. The tool associated with this call. Format:
   * `projects//locations//tools/`.
   *
   * @var string
   */
  public $tool;

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
   * Optional. The answer record associated with this tool call result.
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
   * Only populated if the response content is utf-8 encoded.
   *
   * @param string $content
   */
  public function setContent($content)
  {
    $this->content = $content;
  }
  /**
   * @return string
   */
  public function getContent()
  {
    return $this->content;
  }
  /**
   * Output only. Create time of the tool call result.
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
   * The tool call's error.
   *
   * @param GoogleCloudDialogflowV2ToolCallResultError $error
   */
  public function setError(GoogleCloudDialogflowV2ToolCallResultError $error)
  {
    $this->error = $error;
  }
  /**
   * @return GoogleCloudDialogflowV2ToolCallResultError
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * Only populated if the response content is not utf-8 encoded. (by definition
   * byte fields are base64 encoded).
   *
   * @param string $rawContent
   */
  public function setRawContent($rawContent)
  {
    $this->rawContent = $rawContent;
  }
  /**
   * @return string
   */
  public function getRawContent()
  {
    return $this->rawContent;
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2ToolCallResult::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2ToolCallResult');
