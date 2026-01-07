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

class ActivitySnippet extends \Google\Model
{
  public const TYPE_typeUnspecified = 'typeUnspecified';
  public const TYPE_upload = 'upload';
  public const TYPE_like = 'like';
  public const TYPE_favorite = 'favorite';
  public const TYPE_comment = 'comment';
  public const TYPE_subscription = 'subscription';
  public const TYPE_playlistItem = 'playlistItem';
  public const TYPE_recommendation = 'recommendation';
  public const TYPE_bulletin = 'bulletin';
  public const TYPE_social = 'social';
  public const TYPE_channelItem = 'channelItem';
  public const TYPE_promotedItem = 'promotedItem';
  /**
   * The ID that YouTube uses to uniquely identify the channel associated with
   * the activity.
   *
   * @var string
   */
  public $channelId;
  /**
   * Channel title for the channel responsible for this activity
   *
   * @var string
   */
  public $channelTitle;
  /**
   * The description of the resource primarily associated with the activity.
   * @mutable youtube.activities.insert
   *
   * @var string
   */
  public $description;
  /**
   * The group ID associated with the activity. A group ID identifies user
   * events that are associated with the same user and resource. For example, if
   * a user rates a video and marks the same video as a favorite, the entries
   * for those events would have the same group ID in the user's activity feed.
   * In your user interface, you can avoid repetition by grouping events with
   * the same groupId value.
   *
   * @var string
   */
  public $groupId;
  /**
   * The date and time that the video was uploaded.
   *
   * @var string
   */
  public $publishedAt;
  protected $thumbnailsType = ThumbnailDetails::class;
  protected $thumbnailsDataType = '';
  /**
   * The title of the resource primarily associated with the activity.
   *
   * @var string
   */
  public $title;
  /**
   * The type of activity that the resource describes.
   *
   * @var string
   */
  public $type;

  /**
   * The ID that YouTube uses to uniquely identify the channel associated with
   * the activity.
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
   * Channel title for the channel responsible for this activity
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
   * The description of the resource primarily associated with the activity.
   * @mutable youtube.activities.insert
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
   * The group ID associated with the activity. A group ID identifies user
   * events that are associated with the same user and resource. For example, if
   * a user rates a video and marks the same video as a favorite, the entries
   * for those events would have the same group ID in the user's activity feed.
   * In your user interface, you can avoid repetition by grouping events with
   * the same groupId value.
   *
   * @param string $groupId
   */
  public function setGroupId($groupId)
  {
    $this->groupId = $groupId;
  }
  /**
   * @return string
   */
  public function getGroupId()
  {
    return $this->groupId;
  }
  /**
   * The date and time that the video was uploaded.
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
   * A map of thumbnail images associated with the resource that is primarily
   * associated with the activity. For each object in the map, the key is the
   * name of the thumbnail image, and the value is an object that contains other
   * information about the thumbnail.
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
   * The title of the resource primarily associated with the activity.
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
   * The type of activity that the resource describes.
   *
   * Accepted values: typeUnspecified, upload, like, favorite, comment,
   * subscription, playlistItem, recommendation, bulletin, social, channelItem,
   * promotedItem
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
class_alias(ActivitySnippet::class, 'Google_Service_YouTube_ActivitySnippet');
