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

class LiveBroadcastStatus extends \Google\Model
{
  /**
   * No value or the value is unknown.
   */
  public const LIFE_CYCLE_STATUS_lifeCycleStatusUnspecified = 'lifeCycleStatusUnspecified';
  /**
   * Incomplete settings, but otherwise valid
   */
  public const LIFE_CYCLE_STATUS_created = 'created';
  /**
   * Complete settings
   */
  public const LIFE_CYCLE_STATUS_ready = 'ready';
  /**
   * Visible only to partner, may need special UI treatment
   */
  public const LIFE_CYCLE_STATUS_testing = 'testing';
  /**
   * Viper is recording; this means the "clock" is running
   */
  public const LIFE_CYCLE_STATUS_live = 'live';
  /**
   * The broadcast is finished.
   */
  public const LIFE_CYCLE_STATUS_complete = 'complete';
  /**
   * This broadcast was removed by admin action
   */
  public const LIFE_CYCLE_STATUS_revoked = 'revoked';
  /**
   * Transition into TESTING has been requested
   */
  public const LIFE_CYCLE_STATUS_testStarting = 'testStarting';
  /**
   * Transition into LIVE has been requested
   */
  public const LIFE_CYCLE_STATUS_liveStarting = 'liveStarting';
  public const LIVE_BROADCAST_PRIORITY_liveBroadcastPriorityUnspecified = 'liveBroadcastPriorityUnspecified';
  /**
   * Low priority broadcast: for low view count HoAs or other low priority
   * broadcasts.
   */
  public const LIVE_BROADCAST_PRIORITY_low = 'low';
  /**
   * Normal priority broadcast: for regular HoAs and broadcasts.
   */
  public const LIVE_BROADCAST_PRIORITY_normal = 'normal';
  /**
   * High priority broadcast: for high profile HoAs, like PixelCorp ones.
   */
  public const LIVE_BROADCAST_PRIORITY_high = 'high';
  public const PRIVACY_STATUS_public = 'public';
  public const PRIVACY_STATUS_unlisted = 'unlisted';
  public const PRIVACY_STATUS_private = 'private';
  /**
   * No value or the value is unknown.
   */
  public const RECORDING_STATUS_liveBroadcastRecordingStatusUnspecified = 'liveBroadcastRecordingStatusUnspecified';
  /**
   * The recording has not yet been started.
   */
  public const RECORDING_STATUS_notRecording = 'notRecording';
  /**
   * The recording is currently on.
   */
  public const RECORDING_STATUS_recording = 'recording';
  /**
   * The recording is completed, and cannot be started again.
   */
  public const RECORDING_STATUS_recorded = 'recorded';
  /**
   * The broadcast's status. The status can be updated using the API's
   * liveBroadcasts.transition method.
   *
   * @var string
   */
  public $lifeCycleStatus;
  /**
   * Priority of the live broadcast event (internal state).
   *
   * @var string
   */
  public $liveBroadcastPriority;
  /**
   * Whether the broadcast is made for kids or not, decided by YouTube instead
   * of the creator. This field is read only.
   *
   * @var bool
   */
  public $madeForKids;
  /**
   * The broadcast's privacy status. Note that the broadcast represents exactly
   * one YouTube video, so the privacy settings are identical to those supported
   * for videos. In addition, you can set this field by modifying the broadcast
   * resource or by setting the privacyStatus field of the corresponding video
   * resource.
   *
   * @var string
   */
  public $privacyStatus;
  /**
   * The broadcast's recording status.
   *
   * @var string
   */
  public $recordingStatus;
  /**
   * This field will be set to True if the creator declares the broadcast to be
   * kids only: go/live-cw-work.
   *
   * @var bool
   */
  public $selfDeclaredMadeForKids;

  /**
   * The broadcast's status. The status can be updated using the API's
   * liveBroadcasts.transition method.
   *
   * Accepted values: lifeCycleStatusUnspecified, created, ready, testing, live,
   * complete, revoked, testStarting, liveStarting
   *
   * @param self::LIFE_CYCLE_STATUS_* $lifeCycleStatus
   */
  public function setLifeCycleStatus($lifeCycleStatus)
  {
    $this->lifeCycleStatus = $lifeCycleStatus;
  }
  /**
   * @return self::LIFE_CYCLE_STATUS_*
   */
  public function getLifeCycleStatus()
  {
    return $this->lifeCycleStatus;
  }
  /**
   * Priority of the live broadcast event (internal state).
   *
   * Accepted values: liveBroadcastPriorityUnspecified, low, normal, high
   *
   * @param self::LIVE_BROADCAST_PRIORITY_* $liveBroadcastPriority
   */
  public function setLiveBroadcastPriority($liveBroadcastPriority)
  {
    $this->liveBroadcastPriority = $liveBroadcastPriority;
  }
  /**
   * @return self::LIVE_BROADCAST_PRIORITY_*
   */
  public function getLiveBroadcastPriority()
  {
    return $this->liveBroadcastPriority;
  }
  /**
   * Whether the broadcast is made for kids or not, decided by YouTube instead
   * of the creator. This field is read only.
   *
   * @param bool $madeForKids
   */
  public function setMadeForKids($madeForKids)
  {
    $this->madeForKids = $madeForKids;
  }
  /**
   * @return bool
   */
  public function getMadeForKids()
  {
    return $this->madeForKids;
  }
  /**
   * The broadcast's privacy status. Note that the broadcast represents exactly
   * one YouTube video, so the privacy settings are identical to those supported
   * for videos. In addition, you can set this field by modifying the broadcast
   * resource or by setting the privacyStatus field of the corresponding video
   * resource.
   *
   * Accepted values: public, unlisted, private
   *
   * @param self::PRIVACY_STATUS_* $privacyStatus
   */
  public function setPrivacyStatus($privacyStatus)
  {
    $this->privacyStatus = $privacyStatus;
  }
  /**
   * @return self::PRIVACY_STATUS_*
   */
  public function getPrivacyStatus()
  {
    return $this->privacyStatus;
  }
  /**
   * The broadcast's recording status.
   *
   * Accepted values: liveBroadcastRecordingStatusUnspecified, notRecording,
   * recording, recorded
   *
   * @param self::RECORDING_STATUS_* $recordingStatus
   */
  public function setRecordingStatus($recordingStatus)
  {
    $this->recordingStatus = $recordingStatus;
  }
  /**
   * @return self::RECORDING_STATUS_*
   */
  public function getRecordingStatus()
  {
    return $this->recordingStatus;
  }
  /**
   * This field will be set to True if the creator declares the broadcast to be
   * kids only: go/live-cw-work.
   *
   * @param bool $selfDeclaredMadeForKids
   */
  public function setSelfDeclaredMadeForKids($selfDeclaredMadeForKids)
  {
    $this->selfDeclaredMadeForKids = $selfDeclaredMadeForKids;
  }
  /**
   * @return bool
   */
  public function getSelfDeclaredMadeForKids()
  {
    return $this->selfDeclaredMadeForKids;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LiveBroadcastStatus::class, 'Google_Service_YouTube_LiveBroadcastStatus');
