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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1StreamAssistResponse extends \Google\Collection
{
  protected $collection_key = 'invocationTools';
  protected $answerType = GoogleCloudDiscoveryengineV1AssistAnswer::class;
  protected $answerDataType = '';
  /**
   * A global unique ID that identifies the current pair of request and stream
   * of responses. Used for feedback and support.
   *
   * @var string
   */
  public $assistToken;
  /**
   * The tool names of the tools that were invoked.
   *
   * @var string[]
   */
  public $invocationTools;
  protected $sessionInfoType = GoogleCloudDiscoveryengineV1StreamAssistResponseSessionInfo::class;
  protected $sessionInfoDataType = '';

  /**
   * Assist answer resource object containing parts of the assistant's final
   * answer for the user's query. Not present if the current response doesn't
   * add anything to previously sent AssistAnswer.replies. Observe
   * AssistAnswer.state to see if more parts are to be expected. While the state
   * is `IN_PROGRESS`, the AssistAnswer.replies field in each response will
   * contain replies (reply fragments) to be appended to the ones received in
   * previous responses. AssistAnswer.name won't be filled. If the state is
   * `SUCCEEDED`, `FAILED` or `SKIPPED`, the response is the last response and
   * AssistAnswer.name will have a value.
   *
   * @param GoogleCloudDiscoveryengineV1AssistAnswer $answer
   */
  public function setAnswer(GoogleCloudDiscoveryengineV1AssistAnswer $answer)
  {
    $this->answer = $answer;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1AssistAnswer
   */
  public function getAnswer()
  {
    return $this->answer;
  }
  /**
   * A global unique ID that identifies the current pair of request and stream
   * of responses. Used for feedback and support.
   *
   * @param string $assistToken
   */
  public function setAssistToken($assistToken)
  {
    $this->assistToken = $assistToken;
  }
  /**
   * @return string
   */
  public function getAssistToken()
  {
    return $this->assistToken;
  }
  /**
   * The tool names of the tools that were invoked.
   *
   * @param string[] $invocationTools
   */
  public function setInvocationTools($invocationTools)
  {
    $this->invocationTools = $invocationTools;
  }
  /**
   * @return string[]
   */
  public function getInvocationTools()
  {
    return $this->invocationTools;
  }
  /**
   * Session information. Only included in the final StreamAssistResponse of the
   * response stream.
   *
   * @param GoogleCloudDiscoveryengineV1StreamAssistResponseSessionInfo $sessionInfo
   */
  public function setSessionInfo(GoogleCloudDiscoveryengineV1StreamAssistResponseSessionInfo $sessionInfo)
  {
    $this->sessionInfo = $sessionInfo;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1StreamAssistResponseSessionInfo
   */
  public function getSessionInfo()
  {
    return $this->sessionInfo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1StreamAssistResponse::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1StreamAssistResponse');
