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

class GoogleCloudDiscoveryengineV1ConversationMessage extends \Google\Model
{
  /**
   * Output only. Message creation timestamp.
   *
   * @var string
   */
  public $createTime;
  protected $replyType = GoogleCloudDiscoveryengineV1Reply::class;
  protected $replyDataType = '';
  protected $userInputType = GoogleCloudDiscoveryengineV1TextInput::class;
  protected $userInputDataType = '';

  /**
   * Output only. Message creation timestamp.
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
   * Search reply.
   *
   * @param GoogleCloudDiscoveryengineV1Reply $reply
   */
  public function setReply(GoogleCloudDiscoveryengineV1Reply $reply)
  {
    $this->reply = $reply;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1Reply
   */
  public function getReply()
  {
    return $this->reply;
  }
  /**
   * User text input.
   *
   * @param GoogleCloudDiscoveryengineV1TextInput $userInput
   */
  public function setUserInput(GoogleCloudDiscoveryengineV1TextInput $userInput)
  {
    $this->userInput = $userInput;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1TextInput
   */
  public function getUserInput()
  {
    return $this->userInput;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1ConversationMessage::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1ConversationMessage');
