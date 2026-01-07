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

namespace Google\Service\HangoutsChat;

class ChatSpaceLinkData extends \Google\Model
{
  /**
   * The message of the linked Chat space resource. Format:
   * `spaces/{space}/messages/{message}`
   *
   * @var string
   */
  public $message;
  /**
   * The space of the linked Chat space resource. Format: `spaces/{space}`
   *
   * @var string
   */
  public $space;
  /**
   * The thread of the linked Chat space resource. Format:
   * `spaces/{space}/threads/{thread}`
   *
   * @var string
   */
  public $thread;

  /**
   * The message of the linked Chat space resource. Format:
   * `spaces/{space}/messages/{message}`
   *
   * @param string $message
   */
  public function setMessage($message)
  {
    $this->message = $message;
  }
  /**
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }
  /**
   * The space of the linked Chat space resource. Format: `spaces/{space}`
   *
   * @param string $space
   */
  public function setSpace($space)
  {
    $this->space = $space;
  }
  /**
   * @return string
   */
  public function getSpace()
  {
    return $this->space;
  }
  /**
   * The thread of the linked Chat space resource. Format:
   * `spaces/{space}/threads/{thread}`
   *
   * @param string $thread
   */
  public function setThread($thread)
  {
    $this->thread = $thread;
  }
  /**
   * @return string
   */
  public function getThread()
  {
    return $this->thread;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ChatSpaceLinkData::class, 'Google_Service_HangoutsChat_ChatSpaceLinkData');
