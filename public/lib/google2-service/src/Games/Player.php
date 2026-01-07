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

namespace Google\Service\Games;

class Player extends \Google\Model
{
  /**
   * There is no relationship between the players.
   */
  public const FRIEND_STATUS_NO_RELATIONSHIP = 'NO_RELATIONSHIP';
  /**
   * The player and requester are friends.
   */
  public const FRIEND_STATUS_FRIEND = 'FRIEND';
  /**
   * The base URL for the image that represents the player.
   *
   * @var string
   */
  public $avatarImageUrl;
  /**
   * The url to the landscape mode player banner image.
   *
   * @var string
   */
  public $bannerUrlLandscape;
  /**
   * The url to the portrait mode player banner image.
   *
   * @var string
   */
  public $bannerUrlPortrait;
  /**
   * The name to display for the player.
   *
   * @var string
   */
  public $displayName;
  protected $experienceInfoType = PlayerExperienceInfo::class;
  protected $experienceInfoDataType = '';
  /**
   * The friend status of the given player, relative to the requester. This is
   * unset if the player is not sharing their friends list with the game.
   *
   * @var string
   */
  public $friendStatus;
  /**
   * Per-application unique player identifier.
   *
   * @var string
   */
  public $gamePlayerId;
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#player`
   *
   * @var string
   */
  public $kind;
  protected $nameType = PlayerName::class;
  protected $nameDataType = '';
  /**
   * The player ID that was used for this player the first time they signed into
   * the game in question. This is only populated for calls to player.get for
   * the requesting player, only if the player ID has subsequently changed, and
   * only to clients that support remapping player IDs.
   *
   * @var string
   */
  public $originalPlayerId;
  /**
   * The ID of the player.
   *
   * @var string
   */
  public $playerId;
  protected $profileSettingsType = ProfileSettings::class;
  protected $profileSettingsDataType = '';
  /**
   * The player's title rewarded for their game activities.
   *
   * @var string
   */
  public $title;

  /**
   * The base URL for the image that represents the player.
   *
   * @param string $avatarImageUrl
   */
  public function setAvatarImageUrl($avatarImageUrl)
  {
    $this->avatarImageUrl = $avatarImageUrl;
  }
  /**
   * @return string
   */
  public function getAvatarImageUrl()
  {
    return $this->avatarImageUrl;
  }
  /**
   * The url to the landscape mode player banner image.
   *
   * @param string $bannerUrlLandscape
   */
  public function setBannerUrlLandscape($bannerUrlLandscape)
  {
    $this->bannerUrlLandscape = $bannerUrlLandscape;
  }
  /**
   * @return string
   */
  public function getBannerUrlLandscape()
  {
    return $this->bannerUrlLandscape;
  }
  /**
   * The url to the portrait mode player banner image.
   *
   * @param string $bannerUrlPortrait
   */
  public function setBannerUrlPortrait($bannerUrlPortrait)
  {
    $this->bannerUrlPortrait = $bannerUrlPortrait;
  }
  /**
   * @return string
   */
  public function getBannerUrlPortrait()
  {
    return $this->bannerUrlPortrait;
  }
  /**
   * The name to display for the player.
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
   * An object to represent Play Game experience information for the player.
   *
   * @param PlayerExperienceInfo $experienceInfo
   */
  public function setExperienceInfo(PlayerExperienceInfo $experienceInfo)
  {
    $this->experienceInfo = $experienceInfo;
  }
  /**
   * @return PlayerExperienceInfo
   */
  public function getExperienceInfo()
  {
    return $this->experienceInfo;
  }
  /**
   * The friend status of the given player, relative to the requester. This is
   * unset if the player is not sharing their friends list with the game.
   *
   * Accepted values: NO_RELATIONSHIP, FRIEND
   *
   * @param self::FRIEND_STATUS_* $friendStatus
   */
  public function setFriendStatus($friendStatus)
  {
    $this->friendStatus = $friendStatus;
  }
  /**
   * @return self::FRIEND_STATUS_*
   */
  public function getFriendStatus()
  {
    return $this->friendStatus;
  }
  /**
   * Per-application unique player identifier.
   *
   * @param string $gamePlayerId
   */
  public function setGamePlayerId($gamePlayerId)
  {
    $this->gamePlayerId = $gamePlayerId;
  }
  /**
   * @return string
   */
  public function getGamePlayerId()
  {
    return $this->gamePlayerId;
  }
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#player`
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * A representation of the individual components of the name.
   *
   * @param PlayerName $name
   */
  public function setName(PlayerName $name)
  {
    $this->name = $name;
  }
  /**
   * @return PlayerName
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * The player ID that was used for this player the first time they signed into
   * the game in question. This is only populated for calls to player.get for
   * the requesting player, only if the player ID has subsequently changed, and
   * only to clients that support remapping player IDs.
   *
   * @param string $originalPlayerId
   */
  public function setOriginalPlayerId($originalPlayerId)
  {
    $this->originalPlayerId = $originalPlayerId;
  }
  /**
   * @return string
   */
  public function getOriginalPlayerId()
  {
    return $this->originalPlayerId;
  }
  /**
   * The ID of the player.
   *
   * @param string $playerId
   */
  public function setPlayerId($playerId)
  {
    $this->playerId = $playerId;
  }
  /**
   * @return string
   */
  public function getPlayerId()
  {
    return $this->playerId;
  }
  /**
   * The player's profile settings. Controls whether or not the player's profile
   * is visible to other players.
   *
   * @param ProfileSettings $profileSettings
   */
  public function setProfileSettings(ProfileSettings $profileSettings)
  {
    $this->profileSettings = $profileSettings;
  }
  /**
   * @return ProfileSettings
   */
  public function getProfileSettings()
  {
    return $this->profileSettings;
  }
  /**
   * The player's title rewarded for their game activities.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Player::class, 'Google_Service_Games_Player');
