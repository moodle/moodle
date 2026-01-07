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

class LiveStreamConfigurationIssue extends \Google\Model
{
  public const SEVERITY_info = 'info';
  public const SEVERITY_warning = 'warning';
  public const SEVERITY_error = 'error';
  public const TYPE_gopSizeOver = 'gopSizeOver';
  public const TYPE_gopSizeLong = 'gopSizeLong';
  public const TYPE_gopSizeShort = 'gopSizeShort';
  public const TYPE_openGop = 'openGop';
  public const TYPE_badContainer = 'badContainer';
  public const TYPE_audioBitrateHigh = 'audioBitrateHigh';
  public const TYPE_audioBitrateLow = 'audioBitrateLow';
  public const TYPE_audioSampleRate = 'audioSampleRate';
  public const TYPE_bitrateHigh = 'bitrateHigh';
  public const TYPE_bitrateLow = 'bitrateLow';
  public const TYPE_audioCodec = 'audioCodec';
  public const TYPE_videoCodec = 'videoCodec';
  public const TYPE_noAudioStream = 'noAudioStream';
  public const TYPE_noVideoStream = 'noVideoStream';
  public const TYPE_multipleVideoStreams = 'multipleVideoStreams';
  public const TYPE_multipleAudioStreams = 'multipleAudioStreams';
  public const TYPE_audioTooManyChannels = 'audioTooManyChannels';
  public const TYPE_interlacedVideo = 'interlacedVideo';
  public const TYPE_frameRateHigh = 'frameRateHigh';
  public const TYPE_resolutionMismatch = 'resolutionMismatch';
  public const TYPE_videoCodecMismatch = 'videoCodecMismatch';
  public const TYPE_videoInterlaceMismatch = 'videoInterlaceMismatch';
  public const TYPE_videoProfileMismatch = 'videoProfileMismatch';
  public const TYPE_videoBitrateMismatch = 'videoBitrateMismatch';
  public const TYPE_framerateMismatch = 'framerateMismatch';
  public const TYPE_gopMismatch = 'gopMismatch';
  public const TYPE_audioSampleRateMismatch = 'audioSampleRateMismatch';
  public const TYPE_audioStereoMismatch = 'audioStereoMismatch';
  public const TYPE_audioCodecMismatch = 'audioCodecMismatch';
  public const TYPE_audioBitrateMismatch = 'audioBitrateMismatch';
  public const TYPE_videoResolutionSuboptimal = 'videoResolutionSuboptimal';
  public const TYPE_videoResolutionUnsupported = 'videoResolutionUnsupported';
  public const TYPE_videoIngestionStarved = 'videoIngestionStarved';
  public const TYPE_videoIngestionFasterThanRealtime = 'videoIngestionFasterThanRealtime';
  /**
   * The long-form description of the issue and how to resolve it.
   *
   * @var string
   */
  public $description;
  /**
   * The short-form reason for this issue.
   *
   * @var string
   */
  public $reason;
  /**
   * How severe this issue is to the stream.
   *
   * @var string
   */
  public $severity;
  /**
   * The kind of error happening.
   *
   * @var string
   */
  public $type;

  /**
   * The long-form description of the issue and how to resolve it.
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
   * The short-form reason for this issue.
   *
   * @param string $reason
   */
  public function setReason($reason)
  {
    $this->reason = $reason;
  }
  /**
   * @return string
   */
  public function getReason()
  {
    return $this->reason;
  }
  /**
   * How severe this issue is to the stream.
   *
   * Accepted values: info, warning, error
   *
   * @param self::SEVERITY_* $severity
   */
  public function setSeverity($severity)
  {
    $this->severity = $severity;
  }
  /**
   * @return self::SEVERITY_*
   */
  public function getSeverity()
  {
    return $this->severity;
  }
  /**
   * The kind of error happening.
   *
   * Accepted values: gopSizeOver, gopSizeLong, gopSizeShort, openGop,
   * badContainer, audioBitrateHigh, audioBitrateLow, audioSampleRate,
   * bitrateHigh, bitrateLow, audioCodec, videoCodec, noAudioStream,
   * noVideoStream, multipleVideoStreams, multipleAudioStreams,
   * audioTooManyChannels, interlacedVideo, frameRateHigh, resolutionMismatch,
   * videoCodecMismatch, videoInterlaceMismatch, videoProfileMismatch,
   * videoBitrateMismatch, framerateMismatch, gopMismatch,
   * audioSampleRateMismatch, audioStereoMismatch, audioCodecMismatch,
   * audioBitrateMismatch, videoResolutionSuboptimal,
   * videoResolutionUnsupported, videoIngestionStarved,
   * videoIngestionFasterThanRealtime
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
class_alias(LiveStreamConfigurationIssue::class, 'Google_Service_YouTube_LiveStreamConfigurationIssue');
