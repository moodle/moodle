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

class GoogleCloudDialogflowCxV3beta1WebhookResponseFulfillmentResponse extends \Google\Collection
{
  /**
   * Not specified. `APPEND` will be used.
   */
  public const MERGE_BEHAVIOR_MERGE_BEHAVIOR_UNSPECIFIED = 'MERGE_BEHAVIOR_UNSPECIFIED';
  /**
   * `messages` will be appended to the list of messages waiting to be sent to
   * the user.
   */
  public const MERGE_BEHAVIOR_APPEND = 'APPEND';
  /**
   * `messages` will replace the list of messages waiting to be sent to the
   * user.
   */
  public const MERGE_BEHAVIOR_REPLACE = 'REPLACE';
  protected $collection_key = 'messages';
  /**
   * Merge behavior for `messages`.
   *
   * @var string
   */
  public $mergeBehavior;
  protected $messagesType = GoogleCloudDialogflowCxV3beta1ResponseMessage::class;
  protected $messagesDataType = 'array';

  /**
   * Merge behavior for `messages`.
   *
   * Accepted values: MERGE_BEHAVIOR_UNSPECIFIED, APPEND, REPLACE
   *
   * @param self::MERGE_BEHAVIOR_* $mergeBehavior
   */
  public function setMergeBehavior($mergeBehavior)
  {
    $this->mergeBehavior = $mergeBehavior;
  }
  /**
   * @return self::MERGE_BEHAVIOR_*
   */
  public function getMergeBehavior()
  {
    return $this->mergeBehavior;
  }
  /**
   * The list of rich message responses to present to the user.
   *
   * @param GoogleCloudDialogflowCxV3beta1ResponseMessage[] $messages
   */
  public function setMessages($messages)
  {
    $this->messages = $messages;
  }
  /**
   * @return GoogleCloudDialogflowCxV3beta1ResponseMessage[]
   */
  public function getMessages()
  {
    return $this->messages;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3beta1WebhookResponseFulfillmentResponse::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3beta1WebhookResponseFulfillmentResponse');
