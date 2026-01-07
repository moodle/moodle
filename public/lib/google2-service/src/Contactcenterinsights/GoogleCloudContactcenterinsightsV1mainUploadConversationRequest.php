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

namespace Google\Service\Contactcenterinsights;

class GoogleCloudContactcenterinsightsV1mainUploadConversationRequest extends \Google\Model
{
  protected $conversationType = GoogleCloudContactcenterinsightsV1mainConversation::class;
  protected $conversationDataType = '';
  /**
   * Optional. A unique ID for the new conversation. This ID will become the
   * final component of the conversation's resource name. If no ID is specified,
   * a server-generated ID will be used. This value should be 4-64 characters
   * and must match the regular expression `^[a-z0-9-]{4,64}$`. Valid characters
   * are `a-z-`
   *
   * @var string
   */
  public $conversationId;
  /**
   * Required. The parent resource of the conversation.
   *
   * @var string
   */
  public $parent;
  protected $redactionConfigType = GoogleCloudContactcenterinsightsV1mainRedactionConfig::class;
  protected $redactionConfigDataType = '';
  protected $speechConfigType = GoogleCloudContactcenterinsightsV1mainSpeechConfig::class;
  protected $speechConfigDataType = '';

  /**
   * Required. The conversation resource to create.
   *
   * @param GoogleCloudContactcenterinsightsV1mainConversation $conversation
   */
  public function setConversation(GoogleCloudContactcenterinsightsV1mainConversation $conversation)
  {
    $this->conversation = $conversation;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainConversation
   */
  public function getConversation()
  {
    return $this->conversation;
  }
  /**
   * Optional. A unique ID for the new conversation. This ID will become the
   * final component of the conversation's resource name. If no ID is specified,
   * a server-generated ID will be used. This value should be 4-64 characters
   * and must match the regular expression `^[a-z0-9-]{4,64}$`. Valid characters
   * are `a-z-`
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
   * Required. The parent resource of the conversation.
   *
   * @param string $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }
  /**
   * @return string
   */
  public function getParent()
  {
    return $this->parent;
  }
  /**
   * Optional. DLP settings for transcript redaction. Will default to the config
   * specified in Settings.
   *
   * @param GoogleCloudContactcenterinsightsV1mainRedactionConfig $redactionConfig
   */
  public function setRedactionConfig(GoogleCloudContactcenterinsightsV1mainRedactionConfig $redactionConfig)
  {
    $this->redactionConfig = $redactionConfig;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainRedactionConfig
   */
  public function getRedactionConfig()
  {
    return $this->redactionConfig;
  }
  /**
   * Optional. Speech-to-Text configuration. Will default to the config
   * specified in Settings.
   *
   * @param GoogleCloudContactcenterinsightsV1mainSpeechConfig $speechConfig
   */
  public function setSpeechConfig(GoogleCloudContactcenterinsightsV1mainSpeechConfig $speechConfig)
  {
    $this->speechConfig = $speechConfig;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1mainSpeechConfig
   */
  public function getSpeechConfig()
  {
    return $this->speechConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1mainUploadConversationRequest::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1mainUploadConversationRequest');
