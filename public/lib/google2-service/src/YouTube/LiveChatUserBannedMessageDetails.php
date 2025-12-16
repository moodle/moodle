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

class LiveChatUserBannedMessageDetails extends \Google\Model
{
  public const BAN_TYPE_permanent = 'permanent';
  public const BAN_TYPE_temporary = 'temporary';
  /**
   * The duration of the ban. This property is only present if the banType is
   * temporary.
   *
   * @var string
   */
  public $banDurationSeconds;
  /**
   * The type of ban.
   *
   * @var string
   */
  public $banType;
  protected $bannedUserDetailsType = ChannelProfileDetails::class;
  protected $bannedUserDetailsDataType = '';

  /**
   * The duration of the ban. This property is only present if the banType is
   * temporary.
   *
   * @param string $banDurationSeconds
   */
  public function setBanDurationSeconds($banDurationSeconds)
  {
    $this->banDurationSeconds = $banDurationSeconds;
  }
  /**
   * @return string
   */
  public function getBanDurationSeconds()
  {
    return $this->banDurationSeconds;
  }
  /**
   * The type of ban.
   *
   * Accepted values: permanent, temporary
   *
   * @param self::BAN_TYPE_* $banType
   */
  public function setBanType($banType)
  {
    $this->banType = $banType;
  }
  /**
   * @return self::BAN_TYPE_*
   */
  public function getBanType()
  {
    return $this->banType;
  }
  /**
   * The details of the user that was banned.
   *
   * @param ChannelProfileDetails $bannedUserDetails
   */
  public function setBannedUserDetails(ChannelProfileDetails $bannedUserDetails)
  {
    $this->bannedUserDetails = $bannedUserDetails;
  }
  /**
   * @return ChannelProfileDetails
   */
  public function getBannedUserDetails()
  {
    return $this->bannedUserDetails;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LiveChatUserBannedMessageDetails::class, 'Google_Service_YouTube_LiveChatUserBannedMessageDetails');
