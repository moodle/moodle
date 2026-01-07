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

class ThreadReadState extends \Google\Model
{
  /**
   * The time when the user's thread read state was updated. Usually this
   * corresponds with the timestamp of the last read message in a thread.
   *
   * @var string
   */
  public $lastReadTime;
  /**
   * Resource name of the thread read state. Format:
   * `users/{user}/spaces/{space}/threads/{thread}/threadReadState`
   *
   * @var string
   */
  public $name;

  /**
   * The time when the user's thread read state was updated. Usually this
   * corresponds with the timestamp of the last read message in a thread.
   *
   * @param string $lastReadTime
   */
  public function setLastReadTime($lastReadTime)
  {
    $this->lastReadTime = $lastReadTime;
  }
  /**
   * @return string
   */
  public function getLastReadTime()
  {
    return $this->lastReadTime;
  }
  /**
   * Resource name of the thread read state. Format:
   * `users/{user}/spaces/{space}/threads/{thread}/threadReadState`
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ThreadReadState::class, 'Google_Service_HangoutsChat_ThreadReadState');
