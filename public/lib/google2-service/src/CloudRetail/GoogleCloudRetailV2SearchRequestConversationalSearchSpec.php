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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailV2SearchRequestConversationalSearchSpec extends \Google\Model
{
  /**
   * This field specifies the conversation id, which maintains the state of the
   * conversation between client side and server side. Use the value from the
   * previous ConversationalSearchResult.conversation_id. For the initial
   * request, this should be empty.
   *
   * @var string
   */
  public $conversationId;
  /**
   * This field specifies whether the customer would like to do conversational
   * search. If this field is set to true, conversational related extra
   * information will be returned from server side, including follow-up
   * question, answer options, etc.
   *
   * @var bool
   */
  public $followupConversationRequested;
  protected $userAnswerType = GoogleCloudRetailV2SearchRequestConversationalSearchSpecUserAnswer::class;
  protected $userAnswerDataType = '';

  /**
   * This field specifies the conversation id, which maintains the state of the
   * conversation between client side and server side. Use the value from the
   * previous ConversationalSearchResult.conversation_id. For the initial
   * request, this should be empty.
   *
   * @param string $conversationId
   */
  public function setConversationId($conversationId)
  {
    $this->conversationId = $conversationId;
  }
  /**
   * @return string
   */
  public function getConversationId()
  {
    return $this->conversationId;
  }
  /**
   * This field specifies whether the customer would like to do conversational
   * search. If this field is set to true, conversational related extra
   * information will be returned from server side, including follow-up
   * question, answer options, etc.
   *
   * @param bool $followupConversationRequested
   */
  public function setFollowupConversationRequested($followupConversationRequested)
  {
    $this->followupConversationRequested = $followupConversationRequested;
  }
  /**
   * @return bool
   */
  public function getFollowupConversationRequested()
  {
    return $this->followupConversationRequested;
  }
  /**
   * This field specifies the current user answer during the conversational
   * search. This can be either user selected from suggested answers or user
   * input plain text.
   *
   * @param GoogleCloudRetailV2SearchRequestConversationalSearchSpecUserAnswer $userAnswer
   */
  public function setUserAnswer(GoogleCloudRetailV2SearchRequestConversationalSearchSpecUserAnswer $userAnswer)
  {
    $this->userAnswer = $userAnswer;
  }
  /**
   * @return GoogleCloudRetailV2SearchRequestConversationalSearchSpecUserAnswer
   */
  public function getUserAnswer()
  {
    return $this->userAnswer;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2SearchRequestConversationalSearchSpec::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2SearchRequestConversationalSearchSpec');
