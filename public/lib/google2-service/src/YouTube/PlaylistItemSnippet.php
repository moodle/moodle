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

class PlaylistItemSnippet extends \Google\Model
{
  /**
   * The ID that YouTube uses to uniquely identify the user that added the item
   * to the playlist.
   *
   * @var string
   */
  public $channelId;
  /**
   * Channel title for the channel that the playlist item belongs to.
   *
   * @var string
   */
  public $channelTitle;
  /**
   * The item's description.
   *
   * @var string
   */
  public $description;
  /**
   * The ID that YouTube uses to uniquely identify thGe playlist that the
   * playlist item is in.
   *
   * @var string
   */
  public $playlistId;
  /**
   * The order in which the item appears in the playlist. The value uses a zero-
   * based index, so the first item has a position of 0, the second item has a
   * position of 1, and so forth.
   *
   * @var string
   */
  public $position;
  /**
   * The date and time that the item was added to the playlist.
   *
   * @var string
   */
  public $publishedAt;
  protected $resourceIdType = ResourceId::class;
  protected $resourceIdDataType = '';
  protected $thumbnailsType = ThumbnailDetails::class;
  protected $thumbnailsDataType = '';
  /**
   * The item's title.
   *
   * @var string
   */
  public $title;
  /**
   * Channel id for the channel this video belongs to.
   *
   * @var string
   */
  public $videoOwnerChannelId;
  /**
   * Channel title for the channel this video belongs to.
   *
   * @var string
   */
  public $videoOwnerChannelTitle;

  /**
   * The ID that YouTube uses to uniquely identify the user that added the item
   * to the playlist.
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
   * Channel title for the channel that the playlist item belongs to.
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
   * The item's description.
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
   * The ID that YouTube uses to uniquely identify thGe playlist that the
   * playlist item is in.
   *
   * @param string $playlistId
   */
  public function setPlaylistId($playlistId)
  {
    $this->playlistId = $playlistId;
  }
  /**
   * @return string
   */
  public function getPlaylistId()
  {
    return $this->playlistId;
  }
  /**
   * The order in which the item appears in the playlist. The value uses a zero-
   * based index, so the first item has a position of 0, the second item has a
   * position of 1, and so forth.
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
   * The date and time that the item was added to the playlist.
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
   * The id object contains information that can be used to uniquely identify
   * the resource that is included in the playlist as the playlist item.
   *
   * @param ResourceId $resourceId
   */
  public function setResourceId(ResourceId $resourceId)
  {
    $this->resourceId = $resourceId;
  }
  /**
   * @return ResourceId
   */
  public function getResourceId()
  {
    return $this->resourceId;
  }
  /**
   * A map of thumbnail images associated with the playlist item. For each
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
   * The item's title.
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
   * Channel id for the channel this video belongs to.
   *
   * @param string $videoOwnerChannelId
   */
  public function setVideoOwnerChannelId($videoOwnerChannelId)
  {
    $this->videoOwnerChannelId = $videoOwnerChannelId;
  }
  /**
   * @return string
   */
  public function getVideoOwnerChannelId()
  {
    return $this->videoOwnerChannelId;
  }
  /**
   * Channel title for the channel this video belongs to.
   *
   * @param string $videoOwnerChannelTitle
   */
  public function setVideoOwnerChannelTitle($videoOwnerChannelTitle)
  {
    $this->videoOwnerChannelTitle = $videoOwnerChannelTitle;
  }
  /**
   * @return string
   */
  public function getVideoOwnerChannelTitle()
  {
    return $this->videoOwnerChannelTitle;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PlaylistItemSnippet::class, 'Google_Service_YouTube_PlaylistItemSnippet');
