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

namespace Google\Service\YouTube;

class LiveChatMemberMilestoneChatDetails extends \Google\Model
{
  /**
   * The name of the Level at which the viever is a member. The Level names are
   * defined by the YouTube channel offering the Membership. In some situations
   * this field isn't filled.
   *
   * @var string
   */
  public $memberLevelName;
  /**
   * The total amount of months (rounded up) the viewer has been a member that
   * granted them this Member Milestone Chat. This is the same number of months
   * as is being displayed to YouTube users.
   *
   * @var string
   */
  public $memberMonth;
  /**
   * The comment added by the member to this Member Milestone Chat. This field
   * is empty for messages without a comment from the member.
   *
   * @var string
   */
  public $userComment;

  /**
   * The name of the Level at which the viever is a member. The Level names are
   * defined by the YouTube channel offering the Membership. In some situations
   * this field isn't filled.
   *
   * @param string $memberLevelName
   */
  public function setMemberLevelName($memberLevelName)
  {
    $this->memberLevelName = $memberLevelName;
  }
  /**
   * @return string
   */
  public function getMemberLevelName()
  {
    return $this->memberLevelName;
  }
  /**
   * The total amount of months (rounded up) the viewer has been a member that
   * granted them this Member Milestone Chat. This is the same number of months
   * as is being displayed to YouTube users.
   *
   * @param string $memberMonth
   */
  public function setMemberMonth($memberMonth)
  {
    $this->memberMonth = $memberMonth;
  }
  /**
   * @return string
   */
  public function getMemberMonth()
  {
    return $this->memberMonth;
  }
  /**
   * The comment added by the member to this Member Milestone Chat. This field
   * is empty for messages without a comment from the member.
   *
   * @param string $userComment
   */
  public function setUserComment($userComment)
  {
    $this->userComment = $userComment;
  }
  /**
   * @return string
   */
  public function getUserComment()
  {
    return $this->userComment;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LiveChatMemberMilestoneChatDetails::class, 'Google_Service_YouTube_LiveChatMemberMilestoneChatDetails');
