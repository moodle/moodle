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

class LiveStreamSnippet extends \Google\Model
{
  /**
   * The ID that YouTube uses to uniquely identify the channel that is
   * transmitting the stream.
   *
   * @var string
   */
  public $channelId;
  /**
   * The stream's description. The value cannot be longer than 10000 characters.
   *
   * @var string
   */
  public $description;
  /**
   * @var bool
   */
  public $isDefaultStream;
  /**
   * The date and time that the stream was created.
   *
   * @var string
   */
  public $publishedAt;
  /**
   * The stream's title. The value must be between 1 and 128 characters long.
   *
   * @var string
   */
  public $title;

  /**
   * The ID that YouTube uses to uniquely identify the channel that is
   * transmitting the stream.
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
   * The stream's description. The value cannot be longer than 10000 characters.
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
   * @param bool $isDefaultStream
   */
  public function setIsDefaultStream($isDefaultStream)
  {
    $this->isDefaultStream = $isDefaultStream;
  }
  /**
   * @return bool
   */
  public function getIsDefaultStream()
  {
    return $this->isDefaultStream;
  }
  /**
   * The date and time that the stream was created.
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
   * The stream's title. The value must be between 1 and 128 characters long.
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
class_alias(LiveStreamSnippet::class, 'Google_Service_YouTube_LiveStreamSnippet');
