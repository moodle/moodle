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

namespace Google\Service\WorkspaceEvents;

class SendMessageRequest extends \Google\Model
{
  protected $configurationType = SendMessageConfiguration::class;
  protected $configurationDataType = '';
  protected $messageType = Message::class;
  protected $messageDataType = '';
  /**
   * Optional metadata for the request.
   *
   * @var array[]
   */
  public $metadata;

  /**
   * Configuration for the send request.
   *
   * @param SendMessageConfiguration $configuration
   */
  public function setConfiguration(SendMessageConfiguration $configuration)
  {
    $this->configuration = $configuration;
  }
  /**
   * @return SendMessageConfiguration
   */
  public function getConfiguration()
  {
    return $this->configuration;
  }
  /**
   * Required. The message to send to the agent.
   *
   * @param Message $message
   */
  public function setMessage(Message $message)
  {
    $this->message = $message;
  }
  /**
   * @return Message
   */
  public function getMessage()
  {
    return $this->message;
  }
  /**
   * Optional metadata for the request.
   *
   * @param array[] $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return array[]
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SendMessageRequest::class, 'Google_Service_WorkspaceEvents_SendMessageRequest');
