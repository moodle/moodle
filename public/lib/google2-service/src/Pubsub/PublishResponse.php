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

class PublishResponse extends \Google\Collection
{
  protected $collection_key = 'messageIds';
  /**
   * Optional. The server-assigned ID of each published message, in the same
   * order as the messages in the request. IDs are guaranteed to be unique
   * within the topic.
   *
   * @var string[]
   */
  public $messageIds;

  /**
   * Optional. The server-assigned ID of each published message, in the same
   * order as the messages in the request. IDs are guaranteed to be unique
   * within the topic.
   *
   * @param string[] $messageIds
   */
  public function setMessageIds($messageIds)
  {
    $this->messageIds = $messageIds;
  }
  /**
   * @return string[]
   */
  public function getMessageIds()
  {
    return $this->messageIds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PublishResponse::class, 'Google_Service_Pubsub_PublishResponse');
