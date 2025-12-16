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

class LiveBroadcastSnippet extends \Google\Model
{
  /**
   * The date and time that the broadcast actually ended. This information is
   * only available once the broadcast's state is complete.
   *
   * @var string
   */
  public $actualEndTime;
  /**
   * The date and time that the broadcast actually started. This information is
   * only available once the broadcast's state is live.
   *
   * @var string
   */
  public $actualStartTime;
  /**
   * The ID that YouTube uses to uniquely identify the channel that is
   * publishing the broadcast.
   *
   * @var string
   */
  public $channelId;
  /**
   * The broadcast's description. As with the title, you can set this field by
   * modifying the broadcast resource or by setting the description field of the
   * corresponding video resource.
   *
   * @var string
   */
  public $description;
  /**
   * Indicates whether this broadcast is the default broadcast. Internal only.
   *
   * @var bool
   */
  public $isDefaultBroadcast;
  /**
   * The id of the live chat for this broadcast.
   *
   * @var string
   */
  public $liveChatId;
  /**
   * The date and time that the broadcast was added to YouTube's live broadcast
   * schedule.
   *
   * @var string
   */
  public $publishedAt;
  /**
   * The date and time that the broadcast is scheduled to end.
   *
   * @var string
   */
  public $scheduledEndTime;
  /**
   * The date and time that the broadcast is scheduled to start.
   *
   * @var string
   */
  public $scheduledStartTime;
  protected $thumbnailsType = ThumbnailDetails::class;
  protected $thumbnailsDataType = '';
  /**
   * The broadcast's title. Note that the broadcast represents exactly one
   * YouTube video. You can set this field by modifying the broadcast resource
   * or by setting the title field of the corresponding video resource.
   *
   * @var string
   */
  public $title;

  /**
   * The date and time that the broadcast actually ended. This information is
   * only available once the broadcast's state is complete.
   *
   * @param string $actualEndTime
   */
  public function setActualEndTime($actualEndTime)
  {
    $this->actualEndTime = $actualEndTime;
  }
  /**
   * @return string
   */
  public function getActualEndTime()
  {
    return $this->actualEndTime;
  }
  /**
   * The date and time that the broadcast actually started. This information is
   * only available once the broadcast's state is live.
   *
   * @param string $actualStartTime
   */
  public function setActualStartTime($actualStartTime)
  {
    $this->actualStartTime = $actualStartTime;
  }
  /**
   * @return string
   */
  public function getActualStartTime()
  {
    return $this->actualStartTime;
  }
  /**
   * The ID that YouTube uses to uniquely identify the channel that is
   * publishing the broadcast.
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
   * The broadcast's description. As with the title, you can set this field by
   * modifying the broadcast resource or by setting the description field of the
   * corresponding video resource.
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
   * Indicates whether this broadcast is the default broadcast. Internal only.
   *
   * @param bool $isDefaultBroadcast
   */
  public function setIsDefaultBroadcast($isDefaultBroadcast)
  {
    $this->isDefaultBroadcast = $isDefaultBroadcast;
  }
  /**
   * @return bool
   */
  public function getIsDefaultBroadcast()
  {
    return $this->isDefaultBroadcast;
  }
  /**
   * The id of the live chat for this broadcast.
   *
   * @param string $liveChatId
   */
  public function setLiveChatId($liveChatId)
  {
    $this->liveChatId = $liveChatId;
  }
  /**
   * @return string
   */
  public function getLiveChatId()
  {
    return $this->liveChatId;
  }
  /**
   * The date and time that the broadcast was added to YouTube's live broadcast
   * schedule.
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
   * The date and time that the broadcast is scheduled to end.
   *
   * @param string $scheduledEndTime
   */
  public function setScheduledEndTime($scheduledEndTime)
  {
    $this->scheduledEndTime = $scheduledEndTime;
  }
  /**
   * @return string
   */
  public function getScheduledEndTime()
  {
    return $this->scheduledEndTime;
  }
  /**
   * The date and time that the broadcast is scheduled to start.
   *
   * @param string $scheduledStartTime
   */
  public function setScheduledStartTime($scheduledStartTime)
  {
    $this->scheduledStartTime = $scheduledStartTime;
  }
  /**
   * @return string
   */
  public function getScheduledStartTime()
  {
    return $this->scheduledStartTime;
  }
  /**
   * A map of thumbnail images associated with the broadcast. For each nested
   * object in this object, the key is the name of the thumbnail image, and the
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
   * The broadcast's title. Note that the broadcast represents exactly one
   * YouTube video. You can set this field by modifying the broadcast resource
   * or by setting the title field of the corresponding video resource.
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
class_alias(LiveBroadcastSnippet::class, 'Google_Service_YouTube_LiveBroadcastSnippet');
