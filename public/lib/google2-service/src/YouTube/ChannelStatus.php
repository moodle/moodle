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

class ChannelStatus extends \Google\Model
{
  public const LONG_UPLOADS_STATUS_longUploadsUnspecified = 'longUploadsUnspecified';
  public const LONG_UPLOADS_STATUS_allowed = 'allowed';
  public const LONG_UPLOADS_STATUS_eligible = 'eligible';
  public const LONG_UPLOADS_STATUS_disallowed = 'disallowed';
  public const PRIVACY_STATUS_public = 'public';
  public const PRIVACY_STATUS_unlisted = 'unlisted';
  public const PRIVACY_STATUS_private = 'private';
  /**
   * Whether the channel is considered ypp monetization enabled. See go/yppornot
   * for more details.
   *
   * @var bool
   */
  public $isChannelMonetizationEnabled;
  /**
   * If true, then the user is linked to either a YouTube username or G+
   * account. Otherwise, the user doesn't have a public YouTube identity.
   *
   * @var bool
   */
  public $isLinked;
  /**
   * The long uploads status of this channel. See
   * https://support.google.com/youtube/answer/71673 for more information.
   *
   * @var string
   */
  public $longUploadsStatus;
  /**
   * @var bool
   */
  public $madeForKids;
  /**
   * Privacy status of the channel.
   *
   * @var string
   */
  public $privacyStatus;
  /**
   * @var bool
   */
  public $selfDeclaredMadeForKids;

  /**
   * Whether the channel is considered ypp monetization enabled. See go/yppornot
   * for more details.
   *
   * @param bool $isChannelMonetizationEnabled
   */
  public function setIsChannelMonetizationEnabled($isChannelMonetizationEnabled)
  {
    $this->isChannelMonetizationEnabled = $isChannelMonetizationEnabled;
  }
  /**
   * @return bool
   */
  public function getIsChannelMonetizationEnabled()
  {
    return $this->isChannelMonetizationEnabled;
  }
  /**
   * If true, then the user is linked to either a YouTube username or G+
   * account. Otherwise, the user doesn't have a public YouTube identity.
   *
   * @param bool $isLinked
   */
  public function setIsLinked($isLinked)
  {
    $this->isLinked = $isLinked;
  }
  /**
   * @return bool
   */
  public function getIsLinked()
  {
    return $this->isLinked;
  }
  /**
   * The long uploads status of this channel. See
   * https://support.google.com/youtube/answer/71673 for more information.
   *
   * Accepted values: longUploadsUnspecified, allowed, eligible, disallowed
   *
   * @param self::LONG_UPLOADS_STATUS_* $longUploadsStatus
   */
  public function setLongUploadsStatus($longUploadsStatus)
  {
    $this->longUploadsStatus = $longUploadsStatus;
  }
  /**
   * @return self::LONG_UPLOADS_STATUS_*
   */
  public function getLongUploadsStatus()
  {
    return $this->longUploadsStatus;
  }
  /**
   * @param bool $madeForKids
   */
  public function setMadeForKids($madeForKids)
  {
    $this->madeForKids = $madeForKids;
  }
  /**
   * @return bool
   */
  public function getMadeForKids()
  {
    return $this->madeForKids;
  }
  /**
   * Privacy status of the channel.
   *
   * Accepted values: public, unlisted, private
   *
   * @param self::PRIVACY_STATUS_* $privacyStatus
   */
  public function setPrivacyStatus($privacyStatus)
  {
    $this->privacyStatus = $privacyStatus;
  }
  /**
   * @return self::PRIVACY_STATUS_*
   */
  public function getPrivacyStatus()
  {
    return $this->privacyStatus;
  }
  /**
   * @param bool $selfDeclaredMadeForKids
   */
  public function setSelfDeclaredMadeForKids($selfDeclaredMadeForKids)
  {
    $this->selfDeclaredMadeForKids = $selfDeclaredMadeForKids;
  }
  /**
   * @return bool
   */
  public function getSelfDeclaredMadeForKids()
  {
    return $this->selfDeclaredMadeForKids;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ChannelStatus::class, 'Google_Service_YouTube_ChannelStatus');
