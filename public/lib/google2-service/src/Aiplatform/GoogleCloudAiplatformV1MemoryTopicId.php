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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1MemoryTopicId extends \Google\Model
{
  /**
   * Unspecified topic. This value should not be used.
   */
  public const MANAGED_MEMORY_TOPIC_MANAGED_TOPIC_ENUM_UNSPECIFIED = 'MANAGED_TOPIC_ENUM_UNSPECIFIED';
  /**
   * Significant personal information about the User like first names,
   * relationships, hobbies, important dates.
   */
  public const MANAGED_MEMORY_TOPIC_USER_PERSONAL_INFO = 'USER_PERSONAL_INFO';
  /**
   * Stated or implied likes, dislikes, preferred styles, or patterns.
   */
  public const MANAGED_MEMORY_TOPIC_USER_PREFERENCES = 'USER_PREFERENCES';
  /**
   * Important milestones or conclusions within the dialogue.
   */
  public const MANAGED_MEMORY_TOPIC_KEY_CONVERSATION_DETAILS = 'KEY_CONVERSATION_DETAILS';
  /**
   * Information that the user explicitly requested to remember or forget.
   */
  public const MANAGED_MEMORY_TOPIC_EXPLICIT_INSTRUCTIONS = 'EXPLICIT_INSTRUCTIONS';
  /**
   * Optional. The custom memory topic label.
   *
   * @var string
   */
  public $customMemoryTopicLabel;
  /**
   * Optional. The managed memory topic.
   *
   * @var string
   */
  public $managedMemoryTopic;

  /**
   * Optional. The custom memory topic label.
   *
   * @param string $customMemoryTopicLabel
   */
  public function setCustomMemoryTopicLabel($customMemoryTopicLabel)
  {
    $this->customMemoryTopicLabel = $customMemoryTopicLabel;
  }
  /**
   * @return string
   */
  public function getCustomMemoryTopicLabel()
  {
    return $this->customMemoryTopicLabel;
  }
  /**
   * Optional. The managed memory topic.
   *
   * Accepted values: MANAGED_TOPIC_ENUM_UNSPECIFIED, USER_PERSONAL_INFO,
   * USER_PREFERENCES, KEY_CONVERSATION_DETAILS, EXPLICIT_INSTRUCTIONS
   *
   * @param self::MANAGED_MEMORY_TOPIC_* $managedMemoryTopic
   */
  public function setManagedMemoryTopic($managedMemoryTopic)
  {
    $this->managedMemoryTopic = $managedMemoryTopic;
  }
  /**
   * @return self::MANAGED_MEMORY_TOPIC_*
   */
  public function getManagedMemoryTopic()
  {
    return $this->managedMemoryTopic;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1MemoryTopicId::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1MemoryTopicId');
