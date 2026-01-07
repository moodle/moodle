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

class LiveChatMessageAuthorDetails extends \Google\Model
{
  /**
   * The YouTube channel ID.
   *
   * @var string
   */
  public $channelId;
  /**
   * The channel's URL.
   *
   * @var string
   */
  public $channelUrl;
  /**
   * The channel's display name.
   *
   * @var string
   */
  public $displayName;
  /**
   * Whether the author is a moderator of the live chat.
   *
   * @var bool
   */
  public $isChatModerator;
  /**
   * Whether the author is the owner of the live chat.
   *
   * @var bool
   */
  public $isChatOwner;
  /**
   * Whether the author is a sponsor of the live chat.
   *
   * @var bool
   */
  public $isChatSponsor;
  /**
   * Whether the author's identity has been verified by YouTube.
   *
   * @var bool
   */
  public $isVerified;
  /**
   * The channels's avatar URL.
   *
   * @var string
   */
  public $profileImageUrl;

  /**
   * The YouTube channel ID.
   *
   * @param string $channelId
   */
  public function setChannelId($channelId)
  {
    $this->channelId = $channelId;
  }
  /**
   * @return string
   */
  public function getChannelId()
  {
    return $this->channelId;
  }
  /**
   * The channel's URL.
   *
   * @param string $channelUrl
   */
  public function setChannelUrl($channelUrl)
  {
    $this->channelUrl = $channelUrl;
  }
  /**
   * @return string
   */
  public function getChannelUrl()
  {
    return $this->channelUrl;
  }
  /**
   * The channel's display name.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Whether the author is a moderator of the live chat.
   *
   * @param bool $isChatModerator
   */
  public function setIsChatModerator($isChatModerator)
  {
    $this->isChatModerator = $isChatModerator;
  }
  /**
   * @return bool
   */
  public function getIsChatModerator()
  {
    return $this->isChatModerator;
  }
  /**
   * Whether the author is the owner of the live chat.
   *
   * @param bool $isChatOwner
   */
  public function setIsChatOwner($isChatOwner)
  {
    $this->isChatOwner = $isChatOwner;
  }
  /**
   * @return bool
   */
  public function getIsChatOwner()
  {
    return $this->isChatOwner;
  }
  /**
   * Whether the author is a sponsor of the live chat.
   *
   * @param bool $isChatSponsor
   */
  public function setIsChatSponsor($isChatSponsor)
  {
    $this->isChatSponsor = $isChatSponsor;
  }
  /**
   * @return bool
   */
  public function getIsChatSponsor()
  {
    return $this->isChatSponsor;
  }
  /**
   * Whether the author's identity has been verified by YouTube.
   *
   * @param bool $isVerified
   */
  public function setIsVerified($isVerified)
  {
    $this->isVerified = $isVerified;
  }
  /**
   * @return bool
   */
  public function getIsVerified()
  {
    return $this->isVerified;
  }
  /**
   * The channels's avatar URL.
   *
   * @param string $profileImageUrl
   */
  public function setProfileImageUrl($profileImageUrl)
  {
    $this->profileImageUrl = $profileImageUrl;
  }
  /**
   * @return string
   */
  public function getProfileImageUrl()
  {
    return $this->profileImageUrl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LiveChatMessageAuthorDetails::class, 'Google_Service_YouTube_LiveChatMessageAuthorDetails');
