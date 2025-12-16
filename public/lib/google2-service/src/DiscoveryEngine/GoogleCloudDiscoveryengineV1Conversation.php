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

class GoogleCloudDiscoveryengineV1Conversation extends \Google\Collection
{
  /**
   * Unknown.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Conversation is currently open.
   */
  public const STATE_IN_PROGRESS = 'IN_PROGRESS';
  /**
   * Conversation has been completed.
   */
  public const STATE_COMPLETED = 'COMPLETED';
  protected $collection_key = 'messages';
  /**
   * Output only. The time the conversation finished.
   *
   * @var string
   */
  public $endTime;
  protected $messagesType = GoogleCloudDiscoveryengineV1ConversationMessage::class;
  protected $messagesDataType = 'array';
  /**
   * Immutable. Fully qualified name `projects/{project}/locations/global/collec
   * tions/{collection}/dataStore/conversations` or `projects/{project}/location
   * s/global/collections/{collection}/engines/conversations`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The time the conversation started.
   *
   * @var string
   */
  public $startTime;
  /**
   * The state of the Conversation.
   *
   * @var string
   */
  public $state;
  /**
   * A unique identifier for tracking users.
   *
   * @var string
   */
  public $userPseudoId;

  /**
   * Output only. The time the conversation finished.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Conversation messages.
   *
   * @param GoogleCloudDiscoveryengineV1ConversationMessage[] $messages
   */
  public function setMessages($messages)
  {
    $this->messages = $messages;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1ConversationMessage[]
   */
  public function getMessages()
  {
    return $this->messages;
  }
  /**
   * Immutable. Fully qualified name `projects/{project}/locations/global/collec
   * tions/{collection}/dataStore/conversations` or `projects/{project}/location
   * s/global/collections/{collection}/engines/conversations`.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. The time the conversation started.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * The state of the Conversation.
   *
   * Accepted values: STATE_UNSPECIFIED, IN_PROGRESS, COMPLETED
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
   * A unique identifier for tracking users.
   *
   * @param string $userPseudoId
   */
  public function setUserPseudoId($userPseudoId)
  {
    $this->userPseudoId = $userPseudoId;
  }
  /**
   * @return string
   */
  public function getUserPseudoId()
  {
    return $this->userPseudoId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1Conversation::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1Conversation');
