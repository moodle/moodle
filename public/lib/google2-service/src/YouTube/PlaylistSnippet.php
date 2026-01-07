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

class PlaylistSnippet extends \Google\Collection
{
  protected $collection_key = 'tags';
  /**
   * The ID that YouTube uses to uniquely identify the channel that published
   * the playlist.
   *
   * @var string
   */
  public $channelId;
  /**
   * The channel title of the channel that the video belongs to.
   *
   * @var string
   */
  public $channelTitle;
  /**
   * The language of the playlist's default title and description.
   *
   * @var string
   */
  public $defaultLanguage;
  /**
   * The playlist's description.
   *
   * @var string
   */
  public $description;
  protected $localizedType = PlaylistLocalization::class;
  protected $localizedDataType = '';
  /**
   * The date and time that the playlist was created.
   *
   * @var string
   */
  public $publishedAt;
  /**
   * Keyword tags associated with the playlist.
   *
   * @deprecated
   * @var string[]
   */
  public $tags;
  /**
   * Note: if the playlist has a custom thumbnail, this field will not be
   * populated. The video id selected by the user that will be used as the
   * thumbnail of this playlist. This field defaults to the first publicly
   * viewable video in the playlist, if: 1. The user has never selected a video
   * to be the thumbnail of the playlist. 2. The user selects a video to be the
   * thumbnail, and then removes that video from the playlist. 3. The user
   * selects a non-owned video to be the thumbnail, but that video becomes
   * private, or gets deleted.
   *
   * @var string
   */
  public $thumbnailVideoId;
  protected $thumbnailsType = ThumbnailDetails::class;
  protected $thumbnailsDataType = '';
  /**
   * The playlist's title.
   *
   * @var string
   */
  public $title;

  /**
   * The ID that YouTube uses to uniquely identify the channel that published
   * the playlist.
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
   * The channel title of the channel that the video belongs to.
   *
   * @param string $channelTitle
   */
  public function setChannelTitle($channelTitle)
  {
    $this->channelTitle = $channelTitle;
  }
  /**
   * @return string
   */
  public function getChannelTitle()
  {
    return $this->channelTitle;
  }
  /**
   * The language of the playlist's default title and description.
   *
   * @param string $defaultLanguage
   */
  public function setDefaultLanguage($defaultLanguage)
  {
    $this->defaultLanguage = $defaultLanguage;
  }
  /**
   * @return string
   */
  public function getDefaultLanguage()
  {
    return $this->defaultLanguage;
  }
  /**
   * The playlist's description.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Localized title and description, read-only.
   *
   * @param PlaylistLocalization $localized
   */
  public function setLocalized(PlaylistLocalization $localized)
  {
    $this->localized = $localized;
  }
  /**
   * @return PlaylistLocalization
   */
  public function getLocalized()
  {
    return $this->localized;
  }
  /**
   * The date and time that the playlist was created.
   *
   * @param string $publishedAt
   */
  public function setPublishedAt($publishedAt)
  {
    $this->publishedAt = $publishedAt;
  }
  /**
   * @return string
   */
  public function getPublishedAt()
  {
    return $this->publishedAt;
  }
  /**
   * Keyword tags associated with the playlist.
   *
   * @deprecated
   * @param string[] $tags
   */
  public function setTags($tags)
  {
    $this->tags = $tags;
  }
  /**
   * @deprecated
   * @return string[]
   */
  public function getTags()
  {
    return $this->tags;
  }
  /**
   * Note: if the playlist has a custom thumbnail, this field will not be
   * populated. The video id selected by the user that will be used as the
   * thumbnail of this playlist. This field defaults to the first publicly
   * viewable video in the playlist, if: 1. The user has never selected a video
   * to be the thumbnail of the playlist. 2. The user selects a video to be the
   * thumbnail, and then removes that video from the playlist. 3. The user
   * selects a non-owned video to be the thumbnail, but that video becomes
   * private, or gets deleted.
   *
   * @param string $thumbnailVideoId
   */
  public function setThumbnailVideoId($thumbnailVideoId)
  {
    $this->thumbnailVideoId = $thumbnailVideoId;
  }
  /**
   * @return string
   */
  public function getThumbnailVideoId()
  {
    return $this->thumbnailVideoId;
  }
  /**
   * A map of thumbnail images associated with the playlist. For each object in
   * the map, the key is the name of the thumbnail image, and the value is an
   * object that contains other information about the thumbnail.
   *
   * @param ThumbnailDetails $thumbnails
   */
  public function setThumbnails(ThumbnailDetails $thumbnails)
  {
    $this->thumbnails = $thumbnails;
  }
  /**
   * @return ThumbnailDetails
   */
  public function getThumbnails()
  {
    return $this->thumbnails;
  }
  /**
   * The playlist's title.
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
class_alias(PlaylistSnippet::class, 'Google_Service_YouTube_PlaylistSnippet');
