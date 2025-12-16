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

namespace Google\Service\Pubsub;

class PullResponse extends \Google\Collection
{
  protected $collection_key = 'receivedMessages';
  protected $receivedMessagesType = ReceivedMessage::class;
  protected $receivedMessagesDataType = 'array';

  /**
   * Optional. Received Pub/Sub messages. The list will be empty if there are no
   * more messages available in the backlog, or if no messages could be returned
   * before the request timeout. For JSON, the response can be entirely empty.
   * The Pub/Sub system may return fewer than the `maxMessages` requested even
   * if there are more messages available in the backlog.
   *
   * @param ReceivedMessage[] $receivedMessages
   */
  public function setReceivedMessages($receivedMessages)
  {
    $this->receivedMessages = $receivedMessages;
  }
  /**
   * @return ReceivedMessage[]
   */
  public function getReceivedMessages()
  {
    return $this->receivedMessages;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PullResponse::class, 'Google_Service_Pubsub_PullResponse');
