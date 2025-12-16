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

class ProfileSettings extends \Google\Model
{
  /**
   * The friends list is currently visible to the game.
   */
  public const FRIENDS_LIST_VISIBILITY_VISIBLE = 'VISIBLE';
  /**
   * The developer does not have access to the friends list, but can call the
   * Android API to show a consent dialog.
   */
  public const FRIENDS_LIST_VISIBILITY_REQUEST_REQUIRED = 'REQUEST_REQUIRED';
  /**
   * The friends list is currently unavailable for this user, and it is not
   * possible to request access at this time, either because the user has
   * permanently declined or the friends feature is not available to them. In
   * this state, any attempts to request access to the friends list will be
   * unsuccessful.
   */
  public const FRIENDS_LIST_VISIBILITY_UNAVAILABLE = 'UNAVAILABLE';
  /**
   * @var string
   */
  public $friendsListVisibility;
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#profileSettings`.
   *
   * @var string
   */
  public $kind;
  /**
   * Whether the player's profile is visible to the currently signed in player.
   *
   * @var bool
   */
  public $profileVisible;

  /**
   * @param self::FRIENDS_LIST_VISIBILITY_* $friendsListVisibility
   */
  public function setFriendsListVisibility($friendsListVisibility)
  {
    $this->friendsListVisibility = $friendsListVisibility;
  }
  /**
   * @return self::FRIENDS_LIST_VISIBILITY_*
   */
  public function getFriendsListVisibility()
  {
    return $this->friendsListVisibility;
  }
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#profileSettings`.
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
   * Whether the player's profile is visible to the currently signed in player.
   *
   * @param bool $profileVisible
   */
  public function setProfileVisible($profileVisible)
  {
    $this->profileVisible = $profileVisible;
  }
  /**
   * @return bool
   */
  public function getProfileVisible()
  {
    return $this->profileVisible;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProfileSettings::class, 'Google_Service_Games_ProfileSettings');
