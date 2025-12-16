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

class PlaylistItemContentDetails extends \Google\Model
{
  /**
   * The time, measured in seconds from the start of the video, when the video
   * should stop playing. (The playlist owner can specify the times when the
   * video should start and stop playing when the video is played in the context
   * of the playlist.) By default, assume that the video.endTime is the end of
   * the video.
   *
   * @deprecated
   * @var string
   */
  public $endAt;
  /**
   * A user-generated note for this item.
   *
   * @var string
   */
  public $note;
  /**
   * The time, measured in seconds from the start of the video, when the video
   * should start playing. (The playlist owner can specify the times when the
   * video should start and stop playing when the video is played in the context
   * of the playlist.) The default value is 0.
   *
   * @deprecated
   * @var string
   */
  public $startAt;
  /**
   * The ID that YouTube uses to uniquely identify a video. To retrieve the
   * video resource, set the id query parameter to this value in your API
   * request.
   *
   * @var string
   */
  public $videoId;
  /**
   * The date and time that the video was published to YouTube.
   *
   * @var string
   */
  public $videoPublishedAt;

  /**
   * The time, measured in seconds from the start of the video, when the video
   * should stop playing. (The playlist owner can specify the times when the
   * video should start and stop playing when the video is played in the context
   * of the playlist.) By default, assume that the video.endTime is the end of
   * the video.
   *
   * @deprecated
   * @param string $endAt
   */
  public function setEndAt($endAt)
  {
    $this->endAt = $endAt;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getEndAt()
  {
    return $this->endAt;
  }
  /**
   * A user-generated note for this item.
   *
   * @param string $note
   */
  public function setNote($note)
  {
    $this->note = $note;
  }
  /**
   * @return string
   */
  public function getNote()
  {
    return $this->note;
  }
  /**
   * The time, measured in seconds from the start of the video, when the video
   * should start playing. (The playlist owner can specify the times when the
   * video should start and stop playing when the video is played in the context
   * of the playlist.) The default value is 0.
   *
   * @deprecated
   * @param string $startAt
   */
  public function setStartAt($startAt)
  {
    $this->startAt = $startAt;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getStartAt()
  {
    return $this->startAt;
  }
  /**
   * The ID that YouTube uses to uniquely identify a video. To retrieve the
   * video resource, set the id query parameter to this value in your API
   * request.
   *
   * @param string $videoId
   */
  public function setVideoId($videoId)
  {
    $this->videoId = $videoId;
  }
  /**
   * @return string
   */
  public function getVideoId()
  {
    return $this->videoId;
  }
  /**
   * The date and time that the video was published to YouTube.
   *
   * @param string $videoPublishedAt
   */
  public function setVideoPublishedAt($videoPublishedAt)
  {
    $this->videoPublishedAt = $videoPublishedAt;
  }
  /**
   * @return string
   */
  public function getVideoPublishedAt()
  {
    return $this->videoPublishedAt;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PlaylistItemContentDetails::class, 'Google_Service_YouTube_PlaylistItemContentDetails');
