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

class SpaceReadState extends \Google\Model
{
  /**
   * Optional. The time when the user's space read state was updated. Usually
   * this corresponds with either the timestamp of the last read message, or a
   * timestamp specified by the user to mark the last read position in a space.
   *
   * @var string
   */
  public $lastReadTime;
  /**
   * Resource name of the space read state. Format:
   * `users/{user}/spaces/{space}/spaceReadState`
   *
   * @var string
   */
  public $name;

  /**
   * Optional. The time when the user's space read state was updated. Usually
   * this corresponds with either the timestamp of the last read message, or a
   * timestamp specified by the user to mark the last read position in a space.
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
   * Resource name of the space read state. Format:
   * `users/{user}/spaces/{space}/spaceReadState`
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
class_alias(SpaceReadState::class, 'Google_Service_HangoutsChat_SpaceReadState');
