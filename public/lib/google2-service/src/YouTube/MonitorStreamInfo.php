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

class MonitorStreamInfo extends \Google\Model
{
  /**
   * If you have set the enableMonitorStream property to true, then this
   * property determines the length of the live broadcast delay.
   *
   * @var string
   */
  public $broadcastStreamDelayMs;
  /**
   * HTML code that embeds a player that plays the monitor stream.
   *
   * @var string
   */
  public $embedHtml;
  /**
   * This value determines whether the monitor stream is enabled for the
   * broadcast. If the monitor stream is enabled, then YouTube will broadcast
   * the event content on a special stream intended only for the broadcaster's
   * consumption. The broadcaster can use the stream to review the event content
   * and also to identify the optimal times to insert cuepoints. You need to set
   * this value to true if you intend to have a broadcast delay for your event.
   * *Note:* This property cannot be updated once the broadcast is in the
   * testing or live state.
   *
   * @var bool
   */
  public $enableMonitorStream;

  /**
   * If you have set the enableMonitorStream property to true, then this
   * property determines the length of the live broadcast delay.
   *
   * @param string $broadcastStreamDelayMs
   */
  public function setBroadcastStreamDelayMs($broadcastStreamDelayMs)
  {
    $this->broadcastStreamDelayMs = $broadcastStreamDelayMs;
  }
  /**
   * @return string
   */
  public function getBroadcastStreamDelayMs()
  {
    return $this->broadcastStreamDelayMs;
  }
  /**
   * HTML code that embeds a player that plays the monitor stream.
   *
   * @param string $embedHtml
   */
  public function setEmbedHtml($embedHtml)
  {
    $this->embedHtml = $embedHtml;
  }
  /**
   * @return string
   */
  public function getEmbedHtml()
  {
    return $this->embedHtml;
  }
  /**
   * This value determines whether the monitor stream is enabled for the
   * broadcast. If the monitor stream is enabled, then YouTube will broadcast
   * the event content on a special stream intended only for the broadcaster's
   * consumption. The broadcaster can use the stream to review the event content
   * and also to identify the optimal times to insert cuepoints. You need to set
   * this value to true if you intend to have a broadcast delay for your event.
   * *Note:* This property cannot be updated once the broadcast is in the
   * testing or live state.
   *
   * @param bool $enableMonitorStream
   */
  public function setEnableMonitorStream($enableMonitorStream)
  {
    $this->enableMonitorStream = $enableMonitorStream;
  }
  /**
   * @return bool
   */
  public function getEnableMonitorStream()
  {
    return $this->enableMonitorStream;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MonitorStreamInfo::class, 'Google_Service_YouTube_MonitorStreamInfo');
