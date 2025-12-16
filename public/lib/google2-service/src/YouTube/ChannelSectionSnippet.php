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

class ChannelSectionSnippet extends \Google\Model
{
  public const STYLE_channelsectionStyleUnspecified = 'channelsectionStyleUnspecified';
  public const STYLE_horizontalRow = 'horizontalRow';
  public const STYLE_verticalList = 'verticalList';
  public const TYPE_channelsectionTypeUndefined = 'channelsectionTypeUndefined';
  public const TYPE_singlePlaylist = 'singlePlaylist';
  public const TYPE_multiplePlaylists = 'multiplePlaylists';
  public const TYPE_popularUploads = 'popularUploads';
  public const TYPE_recentUploads = 'recentUploads';
  /**
   * @deprecated
   */
  public const TYPE_likes = 'likes';
  public const TYPE_allPlaylists = 'allPlaylists';
  /**
   * @deprecated
   */
  public const TYPE_likedPlaylists = 'likedPlaylists';
  /**
   * @deprecated
   */
  public const TYPE_recentPosts = 'recentPosts';
  /**
   * @deprecated
   */
  public const TYPE_recentActivity = 'recentActivity';
  public const TYPE_liveEvents = 'liveEvents';
  public const TYPE_upcomingEvents = 'upcomingEvents';
  public const TYPE_completedEvents = 'completedEvents';
  public const TYPE_multipleChannels = 'multipleChannels';
  /**
   * @deprecated
   */
  public const TYPE_postedVideos = 'postedVideos';
  /**
   * @deprecated
   */
  public const TYPE_postedPlaylists = 'postedPlaylists';
  public const TYPE_subscriptions = 'subscriptions';
  /**
   * The ID that YouTube uses to uniquely identify the channel that published
   * the channel section.
   *
   * @var string
   */
  public $channelId;
  /**
   * The language of the channel section's default title and description.
   *
   * @deprecated
   * @var string
   */
  public $defaultLanguage;
  protected $localizedType = ChannelSectionLocalization::class;
  protected $localizedDataType = '';
  /**
   * The position of the channel section in the channel.
   *
   * @var string
   */
  public $position;
  /**
   * The style of the channel section.
   *
   * @deprecated
   * @var string
   */
  public $style;
  /**
   * The channel section's title for multiple_playlists and multiple_channels.
   *
   * @var string
   */
  public $title;
  /**
   * The type of the channel section.
   *
   * @var string
   */
  public $type;

  /**
   * The ID that YouTube uses to uniquely identify the channel that published
   * the channel section.
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
   * The language of the channel section's default title and description.
   *
   * @deprecated
   * @param string $defaultLanguage
   */
  public function setDefaultLanguage($defaultLanguage)
  {
    $this->defaultLanguage = $defaultLanguage;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getDefaultLanguage()
  {
    return $this->defaultLanguage;
  }
  /**
   * Localized title, read-only.
   *
   * @deprecated
   * @param ChannelSectionLocalization $localized
   */
  public function setLocalized(ChannelSectionLocalization $localized)
  {
    $this->localized = $localized;
  }
  /**
   * @deprecated
   * @return ChannelSectionLocalization
   */
  public function getLocalized()
  {
    return $this->localized;
  }
  /**
   * The position of the channel section in the channel.
   *
   * @param string $position
   */
  public function setPosition($position)
  {
    $this->position = $position;
  }
  /**
   * @return string
   */
  public function getPosition()
  {
    return $this->position;
  }
  /**
   * The style of the channel section.
   *
   * Accepted values: channelsectionStyleUnspecified, horizontalRow,
   * verticalList
   *
   * @deprecated
   * @param self::STYLE_* $style
   */
  public function setStyle($style)
  {
    $this->style = $style;
  }
  /**
   * @deprecated
   * @return self::STYLE_*
   */
  public function getStyle()
  {
    return $this->style;
  }
  /**
   * The channel section's title for multiple_playlists and multiple_channels.
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
  /**
   * The type of the channel section.
   *
   * Accepted values: channelsectionTypeUndefined, singlePlaylist,
   * multiplePlaylists, popularUploads, recentUploads, likes, allPlaylists,
   * likedPlaylists, recentPosts, recentActivity, liveEvents, upcomingEvents,
   * completedEvents, multipleChannels, postedVideos, postedPlaylists,
   * subscriptions
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ChannelSectionSnippet::class, 'Google_Service_YouTube_ChannelSectionSnippet');
