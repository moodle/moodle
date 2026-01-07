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

class VideoSnippet extends \Google\Collection
{
  public const LIVE_BROADCAST_CONTENT_none = 'none';
  /**
   * The live broadcast is upcoming.
   */
  public const LIVE_BROADCAST_CONTENT_upcoming = 'upcoming';
  /**
   * The live broadcast is active.
   */
  public const LIVE_BROADCAST_CONTENT_live = 'live';
  /**
   * The live broadcast has been completed.
   */
  public const LIVE_BROADCAST_CONTENT_completed = 'completed';
  protected $collection_key = 'tags';
  /**
   * The YouTube video category associated with the video.
   *
   * @var string
   */
  public $categoryId;
  /**
   * The ID that YouTube uses to uniquely identify the channel that the video
   * was uploaded to.
   *
   * @var string
   */
  public $channelId;
  /**
   * Channel title for the channel that the video belongs to.
   *
   * @var string
   */
  public $channelTitle;
  /**
   * The default_audio_language property specifies the language spoken in the
   * video's default audio track.
   *
   * @var string
   */
  public $defaultAudioLanguage;
  /**
   * The language of the videos's default snippet.
   *
   * @var string
   */
  public $defaultLanguage;
  /**
   * The video's description. @mutable youtube.videos.insert
   * youtube.videos.update
   *
   * @var string
   */
  public $description;
  /**
   * Indicates if the video is an upcoming/active live broadcast. Or it's "none"
   * if the video is not an upcoming/active live broadcast.
   *
   * @var string
   */
  public $liveBroadcastContent;
  protected $localizedType = VideoLocalization::class;
  protected $localizedDataType = '';
  /**
   * The date and time when the video was uploaded.
   *
   * @var string
   */
  public $publishedAt;
  /**
   * A list of keyword tags associated with the video. Tags may contain spaces.
   *
   * @var string[]
   */
  public $tags;
  protected $thumbnailsType = ThumbnailDetails::class;
  protected $thumbnailsDataType = '';
  /**
   * The video's title. @mutable youtube.videos.insert youtube.videos.update
   *
   * @var string
   */
  public $title;

  /**
   * The YouTube video category associated with the video.
   *
   * @param string $categoryId
   */
  public function setCategoryId($categoryId)
  {
    $this->categoryId = $categoryId;
  }
  /**
   * @return string
   */
  public function getCategoryId()
  {
    return $this->categoryId;
  }
  /**
   * The ID that YouTube uses to uniquely identify the channel that the video
   * was uploaded to.
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
   * Channel title for the channel that the video belongs to.
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
   * The default_audio_language property specifies the language spoken in the
   * video's default audio track.
   *
   * @param string $defaultAudioLanguage
   */
  public function setDefaultAudioLanguage($defaultAudioLanguage)
  {
    $this->defaultAudioLanguage = $defaultAudioLanguage;
  }
  /**
   * @return string
   */
  public function getDefaultAudioLanguage()
  {
    return $this->defaultAudioLanguage;
  }
  /**
   * The language of the videos's default snippet.
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
   * The video's description. @mutable youtube.videos.insert
   * youtube.videos.update
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
   * Indicates if the video is an upcoming/active live broadcast. Or it's "none"
   * if the video is not an upcoming/active live broadcast.
   *
   * Accepted values: none, upcoming, live, completed
   *
   * @param self::LIVE_BROADCAST_CONTENT_* $liveBroadcastContent
   */
  public function setLiveBroadcastContent($liveBroadcastContent)
  {
    $this->liveBroadcastContent = $liveBroadcastContent;
  }
  /**
   * @return self::LIVE_BROADCAST_CONTENT_*
   */
  public function getLiveBroadcastContent()
  {
    return $this->liveBroadcastContent;
  }
  /**
   * Localized snippet selected with the hl parameter. If no such localization
   * exists, this field is populated with the default snippet. (Read-only)
   *
   * @param VideoLocalization $localized
   */
  public function setLocalized(VideoLocalization $localized)
  {
    $this->localized = $localized;
  }
  /**
   * @return VideoLocalization
   */
  public function getLocalized()
  {
    return $this->localized;
  }
  /**
   * The date and time when the video was uploaded.
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
   * A list of keyword tags associated with the video. Tags may contain spaces.
   *
   * @param string[] $tags
   */
  public function setTags($tags)
  {
    $this->tags = $tags;
  }
  /**
   * @return string[]
   */
  public function getTags()
  {
    return $this->tags;
  }
  /**
   * A map of thumbnail images associated with the video. For each object in the
   * map, the key is the name of the thumbnail image, and the value is an object
   * that contains other information about the thumbnail.
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
   * The video's title. @mutable youtube.videos.insert youtube.videos.update
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
class_alias(VideoSnippet::class, 'Google_Service_YouTube_VideoSnippet');
