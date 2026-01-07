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

class GoogleCloudDialogflowV2beta1SetSuggestionFeatureConfigOperationMetadata extends \Google\Model
{
  /**
   * Participant role not set.
   */
  public const PARTICIPANT_ROLE_ROLE_UNSPECIFIED = 'ROLE_UNSPECIFIED';
  /**
   * Participant is a human agent.
   */
  public const PARTICIPANT_ROLE_HUMAN_AGENT = 'HUMAN_AGENT';
  /**
   * Participant is an automated agent, such as a Dialogflow agent.
   */
  public const PARTICIPANT_ROLE_AUTOMATED_AGENT = 'AUTOMATED_AGENT';
  /**
   * Participant is an end user that has called or chatted with Dialogflow
   * services.
   */
  public const PARTICIPANT_ROLE_END_USER = 'END_USER';
  /**
   * Unspecified feature type.
   */
  public const SUGGESTION_FEATURE_TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Run article suggestion model for chat.
   */
  public const SUGGESTION_FEATURE_TYPE_ARTICLE_SUGGESTION = 'ARTICLE_SUGGESTION';
  /**
   * Run FAQ model.
   */
  public const SUGGESTION_FEATURE_TYPE_FAQ = 'FAQ';
  /**
   * Run smart reply model for chat.
   */
  public const SUGGESTION_FEATURE_TYPE_SMART_REPLY = 'SMART_REPLY';
  /**
   * Run Dialogflow assist model for chat, which will return automated agent
   * response as suggestion.
   */
  public const SUGGESTION_FEATURE_TYPE_DIALOGFLOW_ASSIST = 'DIALOGFLOW_ASSIST';
  /**
   * Run conversation summarization model for chat.
   */
  public const SUGGESTION_FEATURE_TYPE_CONVERSATION_SUMMARIZATION = 'CONVERSATION_SUMMARIZATION';
  /**
   * Run knowledge search with text input from agent or text generated query.
   */
  public const SUGGESTION_FEATURE_TYPE_KNOWLEDGE_SEARCH = 'KNOWLEDGE_SEARCH';
  /**
   * Run knowledge assist with automatic query generation.
   */
  public const SUGGESTION_FEATURE_TYPE_KNOWLEDGE_ASSIST = 'KNOWLEDGE_ASSIST';
  /**
   * The resource name of the conversation profile. Format:
   * `projects//locations//conversationProfiles/`
   *
   * @var string
   */
  public $conversationProfile;
  /**
   * Timestamp whe the request was created. The time is measured on server side.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. The participant role to add or update the suggestion feature
   * config. Only HUMAN_AGENT or END_USER can be used.
   *
   * @var string
   */
  public $participantRole;
  /**
   * Required. The type of the suggestion feature to add or update.
   *
   * @var string
   */
  public $suggestionFeatureType;

  /**
   * The resource name of the conversation profile. Format:
   * `projects//locations//conversationProfiles/`
   *
   * @param string $conversationProfile
   */
  public function setConversationProfile($conversationProfile)
  {
    $this->conversationProfile = $conversationProfile;
  }
  /**
   * @return string
   */
  public function getConversationProfile()
  {
    return $this->conversationProfile;
  }
  /**
   * Timestamp whe the request was created. The time is measured on server side.
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
   * Required. The participant role to add or update the suggestion feature
   * config. Only HUMAN_AGENT or END_USER can be used.
   *
   * Accepted values: ROLE_UNSPECIFIED, HUMAN_AGENT, AUTOMATED_AGENT, END_USER
   *
   * @param self::PARTICIPANT_ROLE_* $participantRole
   */
  public function setParticipantRole($participantRole)
  {
    $this->participantRole = $participantRole;
  }
  /**
   * @return self::PARTICIPANT_ROLE_*
   */
  public function getParticipantRole()
  {
    return $this->participantRole;
  }
  /**
   * Required. The type of the suggestion feature to add or update.
   *
   * Accepted values: TYPE_UNSPECIFIED, ARTICLE_SUGGESTION, FAQ, SMART_REPLY,
   * DIALOGFLOW_ASSIST, CONVERSATION_SUMMARIZATION, KNOWLEDGE_SEARCH,
   * KNOWLEDGE_ASSIST
   *
   * @param self::SUGGESTION_FEATURE_TYPE_* $suggestionFeatureType
   */
  public function setSuggestionFeatureType($suggestionFeatureType)
  {
    $this->suggestionFeatureType = $suggestionFeatureType;
  }
  /**
   * @return self::SUGGESTION_FEATURE_TYPE_*
   */
  public function getSuggestionFeatureType()
  {
    return $this->suggestionFeatureType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2beta1SetSuggestionFeatureConfigOperationMetadata::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2beta1SetSuggestionFeatureConfigOperationMetadata');
