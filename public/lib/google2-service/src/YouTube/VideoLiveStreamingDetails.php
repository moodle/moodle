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

class VideoLiveStreamingDetails extends \Google\Model
{
  /**
   * The ID of the currently active live chat attached to this video. This field
   * is filled only if the video is a currently live broadcast that has live
   * chat. Once the broadcast transitions to complete this field will be removed
   * and the live chat closed down. For persistent broadcasts that live chat id
   * will no longer be tied to this video but rather to the new video being
   * displayed at the persistent page.
   *
   * @var string
   */
  public $activeLiveChatId;
  /**
   * The time that the broadcast actually ended. This value will not be
   * available until the broadcast is over.
   *
   * @var string
   */
  public $actualEndTime;
  /**
   * The time that the broadcast actually started. This value will not be
   * available until the broadcast begins.
   *
   * @var string
   */
  public $actualStartTime;
  /**
   * The number of viewers currently watching the broadcast. The property and
   * its value will be present if the broadcast has current viewers and the
   * broadcast owner has not hidden the viewcount for the video. Note that
   * YouTube stops tracking the number of concurrent viewers for a broadcast
   * when the broadcast ends. So, this property would not identify the number of
   * viewers watching an archived video of a live broadcast that already ended.
   *
   * @var string
   */
  public $concurrentViewers;
  /**
   * The time that the broadcast is scheduled to end. If the value is empty or
   * the property is not present, then the broadcast is scheduled to continue
   * indefinitely.
   *
   * @var string
   */
  public $scheduledEndTime;
  /**
   * The time that the broadcast is scheduled to begin.
   *
   * @var string
   */
  public $scheduledStartTime;

  /**
   * The ID of the currently active live chat attached to this video. This field
   * is filled only if the video is a currently live broadcast that has live
   * chat. Once the broadcast transitions to complete this field will be removed
   * and the live chat closed down. For persistent broadcasts that live chat id
   * will no longer be tied to this video but rather to the new video being
   * displayed at the persistent page.
   *
   * @param string $activeLiveChatId
   */
  public function setActiveLiveChatId($activeLiveChatId)
  {
    $this->activeLiveChatId = $activeLiveChatId;
  }
  /**
   * @return string
   */
  public function getActiveLiveChatId()
  {
    return $this->activeLiveChatId;
  }
  /**
   * The time that the broadcast actually ended. This value will not be
   * available until the broadcast is over.
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
   * The time that the broadcast actually started. This value will not be
   * available until the broadcast begins.
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
   * The number of viewers currently watching the broadcast. The property and
   * its value will be present if the broadcast has current viewers and the
   * broadcast owner has not hidden the viewcount for the video. Note that
   * YouTube stops tracking the number of concurrent viewers for a broadcast
   * when the broadcast ends. So, this property would not identify the number of
   * viewers watching an archived video of a live broadcast that already ended.
   *
   * @param string $concurrentViewers
   */
  public function setConcurrentViewers($concurrentViewers)
  {
    $this->concurrentViewers = $concurrentViewers;
  }
  /**
   * @return string
   */
  public function getConcurrentViewers()
  {
    return $this->concurrentViewers;
  }
  /**
   * The time that the broadcast is scheduled to end. If the value is empty or
   * the property is not present, then the broadcast is scheduled to continue
   * indefinitely.
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
   * The time that the broadcast is scheduled to begin.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VideoLiveStreamingDetails::class, 'Google_Service_YouTube_VideoLiveStreamingDetails');
