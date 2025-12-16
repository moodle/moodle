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

namespace Google\Service\Pubsub;

class SeekRequest extends \Google\Model
{
  /**
   * Optional. The snapshot to seek to. The snapshot's topic must be the same as
   * that of the provided subscription. Format is
   * `projects/{project}/snapshots/{snap}`.
   *
   * @var string
   */
  public $snapshot;
  /**
   * Optional. The time to seek to. Messages retained in the subscription that
   * were published before this time are marked as acknowledged, and messages
   * retained in the subscription that were published after this time are marked
   * as unacknowledged. Note that this operation affects only those messages
   * retained in the subscription (configured by the combination of
   * `message_retention_duration` and `retain_acked_messages`). For example, if
   * `time` corresponds to a point before the message retention window (or to a
   * point before the system's notion of the subscription creation time), only
   * retained messages will be marked as unacknowledged, and already-expunged
   * messages will not be restored.
   *
   * @var string
   */
  public $time;

  /**
   * Optional. The snapshot to seek to. The snapshot's topic must be the same as
   * that of the provided subscription. Format is
   * `projects/{project}/snapshots/{snap}`.
   *
   * @param string $snapshot
   */
  public function setSnapshot($snapshot)
  {
    $this->snapshot = $snapshot;
  }
  /**
   * @return string
   */
  public function getSnapshot()
  {
    return $this->snapshot;
  }
  /**
   * Optional. The time to seek to. Messages retained in the subscription that
   * were published before this time are marked as acknowledged, and messages
   * retained in the subscription that were published after this time are marked
   * as unacknowledged. Note that this operation affects only those messages
   * retained in the subscription (configured by the combination of
   * `message_retention_duration` and `retain_acked_messages`). For example, if
   * `time` corresponds to a point before the message retention window (or to a
   * point before the system's notion of the subscription creation time), only
   * retained messages will be marked as unacknowledged, and already-expunged
   * messages will not be restored.
   *
   * @param string $time
   */
  public function setTime($time)
  {
    $this->time = $time;
  }
  /**
   * @return string
   */
  public function getTime()
  {
    return $this->time;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SeekRequest::class, 'Google_Service_Pubsub_SeekRequest');
