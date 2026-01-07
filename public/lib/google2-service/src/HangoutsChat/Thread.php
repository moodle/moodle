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

class Thread extends \Google\Model
{
  /**
   * Identifier. Resource name of the thread. Example:
   * `spaces/{space}/threads/{thread}`
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Input for creating or updating a thread. Otherwise, output only.
   * ID for the thread. Supports up to 4000 characters. This ID is unique to the
   * Chat app that sets it. For example, if multiple Chat apps create a message
   * using the same thread key, the messages are posted in different threads. To
   * reply in a thread created by a person or another Chat app, specify the
   * thread `name` field instead.
   *
   * @var string
   */
  public $threadKey;

  /**
   * Identifier. Resource name of the thread. Example:
   * `spaces/{space}/threads/{thread}`
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
   * Optional. Input for creating or updating a thread. Otherwise, output only.
   * ID for the thread. Supports up to 4000 characters. This ID is unique to the
   * Chat app that sets it. For example, if multiple Chat apps create a message
   * using the same thread key, the messages are posted in different threads. To
   * reply in a thread created by a person or another Chat app, specify the
   * thread `name` field instead.
   *
   * @param string $threadKey
   */
  public function setThreadKey($threadKey)
  {
    $this->threadKey = $threadKey;
  }
  /**
   * @return string
   */
  public function getThreadKey()
  {
    return $this->threadKey;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Thread::class, 'Google_Service_HangoutsChat_Thread');
