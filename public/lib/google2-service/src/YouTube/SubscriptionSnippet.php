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

class SubscriptionSnippet extends \Google\Model
{
  /**
   * The ID that YouTube uses to uniquely identify the subscriber's channel.
   *
   * @var string
   */
  public $channelId;
  /**
   * The subscription's details.
   *
   * @var string
   */
  public $description;
  /**
   * The date and time that the subscription was created.
   *
   * @var string
   */
  public $publishedAt;
  protected $resourceIdType = ResourceId::class;
  protected $resourceIdDataType = '';
  protected $thumbnailsType = ThumbnailDetails::class;
  protected $thumbnailsDataType = '';
  /**
   * The subscription's title.
   *
   * @var string
   */
  public $title;

  /**
   * The ID that YouTube uses to uniquely identify the subscriber's channel.
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
   * The subscription's details.
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
   * The date and time that the subscription was created.
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
   * The id object contains information about the channel that the user
   * subscribed to.
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
   * The subscription's title.
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
class_alias(SubscriptionSnippet::class, 'Google_Service_YouTube_SubscriptionSnippet');
