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

namespace Google\Service\PubsubLite;

class ComputeMessageStatsResponse extends \Google\Model
{
  /**
   * The number of quota bytes accounted to these messages.
   *
   * @var string
   */
  public $messageBytes;
  /**
   * The count of messages.
   *
   * @var string
   */
  public $messageCount;
  /**
   * The minimum event timestamp across these messages. For the purposes of this
   * computation, if a message does not have an event time, we use the publish
   * time. The timestamp will be unset if there are no messages.
   *
   * @var string
   */
  public $minimumEventTime;
  /**
   * The minimum publish timestamp across these messages. Note that publish
   * timestamps within a partition are not guaranteed to be non-decreasing. The
   * timestamp will be unset if there are no messages.
   *
   * @var string
   */
  public $minimumPublishTime;

  /**
   * The number of quota bytes accounted to these messages.
   *
   * @param string $messageBytes
   */
  public function setMessageBytes($messageBytes)
  {
    $this->messageBytes = $messageBytes;
  }
  /**
   * @return string
   */
  public function getMessageBytes()
  {
    return $this->messageBytes;
  }
  /**
   * The count of messages.
   *
   * @param string $messageCount
   */
  public function setMessageCount($messageCount)
  {
    $this->messageCount = $messageCount;
  }
  /**
   * @return string
   */
  public function getMessageCount()
  {
    return $this->messageCount;
  }
  /**
   * The minimum event timestamp across these messages. For the purposes of this
   * computation, if a message does not have an event time, we use the publish
   * time. The timestamp will be unset if there are no messages.
   *
   * @param string $minimumEventTime
   */
  public function setMinimumEventTime($minimumEventTime)
  {
    $this->minimumEventTime = $minimumEventTime;
  }
  /**
   * @return string
   */
  public function getMinimumEventTime()
  {
    return $this->minimumEventTime;
  }
  /**
   * The minimum publish timestamp across these messages. Note that publish
   * timestamps within a partition are not guaranteed to be non-decreasing. The
   * timestamp will be unset if there are no messages.
   *
   * @param string $minimumPublishTime
   */
  public function setMinimumPublishTime($minimumPublishTime)
  {
    $this->minimumPublishTime = $minimumPublishTime;
  }
  /**
   * @return string
   */
  public function getMinimumPublishTime()
  {
    return $this->minimumPublishTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ComputeMessageStatsResponse::class, 'Google_Service_PubsubLite_ComputeMessageStatsResponse');
