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

class MembershipCount extends \Google\Model
{
  /**
   * Output only. Count of human users that have directly joined the space, not
   * counting users joined by having membership in a joined group.
   *
   * @var int
   */
  public $joinedDirectHumanUserCount;
  /**
   * Output only. Count of all groups that have directly joined the space.
   *
   * @var int
   */
  public $joinedGroupCount;

  /**
   * Output only. Count of human users that have directly joined the space, not
   * counting users joined by having membership in a joined group.
   *
   * @param int $joinedDirectHumanUserCount
   */
  public function setJoinedDirectHumanUserCount($joinedDirectHumanUserCount)
  {
    $this->joinedDirectHumanUserCount = $joinedDirectHumanUserCount;
  }
  /**
   * @return int
   */
  public function getJoinedDirectHumanUserCount()
  {
    return $this->joinedDirectHumanUserCount;
  }
  /**
   * Output only. Count of all groups that have directly joined the space.
   *
   * @param int $joinedGroupCount
   */
  public function setJoinedGroupCount($joinedGroupCount)
  {
    $this->joinedGroupCount = $joinedGroupCount;
  }
  /**
   * @return int
   */
  public function getJoinedGroupCount()
  {
    return $this->joinedGroupCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MembershipCount::class, 'Google_Service_HangoutsChat_MembershipCount');
