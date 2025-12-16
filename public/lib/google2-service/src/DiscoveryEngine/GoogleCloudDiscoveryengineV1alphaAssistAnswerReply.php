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

class GoogleCloudDiscoveryengineV1alphaAssistAnswerReply extends \Google\Model
{
  protected $groundedContentType = GoogleCloudDiscoveryengineV1alphaAssistantGroundedContent::class;
  protected $groundedContentDataType = '';
  /**
   * Output only. When set, uniquely identifies a reply within the
   * `AssistAnswer` resource. During an AssistantService.StreamAssist call,
   * multiple `Reply` messages with the same ID can occur within the response
   * stream (across multiple AssistantService.StreamAssistResponse messages).
   * These represent parts of a single `Reply` message in the final
   * `AssistAnswer` resource.
   *
   * @var string
   */
  public $replyId;

  /**
   * Possibly grounded response text or media from the assistant.
   *
   * @param GoogleCloudDiscoveryengineV1alphaAssistantGroundedContent $groundedContent
   */
  public function setGroundedContent(GoogleCloudDiscoveryengineV1alphaAssistantGroundedContent $groundedContent)
  {
    $this->groundedContent = $groundedContent;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaAssistantGroundedContent
   */
  public function getGroundedContent()
  {
    return $this->groundedContent;
  }
  /**
   * Output only. When set, uniquely identifies a reply within the
   * `AssistAnswer` resource. During an AssistantService.StreamAssist call,
   * multiple `Reply` messages with the same ID can occur within the response
   * stream (across multiple AssistantService.StreamAssistResponse messages).
   * These represent parts of a single `Reply` message in the final
   * `AssistAnswer` resource.
   *
   * @param string $replyId
   */
  public function setReplyId($replyId)
  {
    $this->replyId = $replyId;
  }
  /**
   * @return string
   */
  public function getReplyId()
  {
    return $this->replyId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1alphaAssistAnswerReply::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaAssistAnswerReply');
