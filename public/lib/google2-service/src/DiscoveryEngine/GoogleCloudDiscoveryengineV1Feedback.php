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

class GoogleCloudDiscoveryengineV1Feedback extends \Google\Collection
{
  protected $collection_key = 'reasons';
  /**
   * @var string
   */
  public $comment;
  protected $conversationInfoType = GoogleCloudDiscoveryengineV1FeedbackConversationInfo::class;
  protected $conversationInfoDataType = '';
  /**
   * @var string
   */
  public $feedbackType;
  /**
   * @var string
   */
  public $llmModelVersion;
  /**
   * @var string[]
   */
  public $reasons;

  /**
   * @param string
   */
  public function setComment($comment)
  {
    $this->comment = $comment;
  }
  /**
   * @return string
   */
  public function getComment()
  {
    return $this->comment;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1FeedbackConversationInfo
   */
  public function setConversationInfo(GoogleCloudDiscoveryengineV1FeedbackConversationInfo $conversationInfo)
  {
    $this->conversationInfo = $conversationInfo;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1FeedbackConversationInfo
   */
  public function getConversationInfo()
  {
    return $this->conversationInfo;
  }
  /**
   * @param string
   */
  public function setFeedbackType($feedbackType)
  {
    $this->feedbackType = $feedbackType;
  }
  /**
   * @return string
   */
  public function getFeedbackType()
  {
    return $this->feedbackType;
  }
  /**
   * @param string
   */
  public function setLlmModelVersion($llmModelVersion)
  {
    $this->llmModelVersion = $llmModelVersion;
  }
  /**
   * @return string
   */
  public function getLlmModelVersion()
  {
    return $this->llmModelVersion;
  }
  /**
   * @param string[]
   */
  public function setReasons($reasons)
  {
    $this->reasons = $reasons;
  }
  /**
   * @return string[]
   */
  public function getReasons()
  {
    return $this->reasons;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1Feedback::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1Feedback');
