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

class SearchResultSnippet extends \Google\Model
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
  /**
   * The value that YouTube uses to uniquely identify the channel that published
   * the resource that the search result identifies.
   *
   * @var string
   */
  public $channelId;
  /**
   * The title of the channel that published the resource that the search result
   * identifies.
   *
   * @var string
   */
  public $channelTitle;
  /**
   * A description of the search result.
   *
   * @var string
   */
  public $description;
  /**
   * It indicates if the resource (video or channel) has upcoming/active live
   * broadcast content. Or it's "none" if there is not any upcoming/active live
   * broadcasts.
   *
   * @var string
   */
  public $liveBroadcastContent;
  /**
   * The creation date and time of the resource that the search result
   * identifies.
   *
   * @var string
   */
  public $publishedAt;
  protected $thumbnailsType = ThumbnailDetails::class;
  protected $thumbnailsDataType = '';
  /**
   * The title of the search result.
   *
   * @var string
   */
  public $title;

  /**
   * The value that YouTube uses to uniquely identify the channel that published
   * the resource that the search result identifies.
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
   * The title of the channel that published the resource that the search result
   * identifies.
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
   * A description of the search result.
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
   * It indicates if the resource (video or channel) has upcoming/active live
   * broadcast content. Or it's "none" if there is not any upcoming/active live
   * broadcasts.
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
   * The creation date and time of the resource that the search result
   * identifies.
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
   * A map of thumbnail images associated with the search result. For each
   * object in the map, the key is the name of the thumbnail image, and the
   * value is an object that contains other information about the thumbnail.
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
   * The title of the search result.
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
class_alias(SearchResultSnippet::class, 'Google_Service_YouTube_SearchResultSnippet');
