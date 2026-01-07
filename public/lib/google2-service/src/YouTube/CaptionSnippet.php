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

class CaptionSnippet extends \Google\Model
{
  public const AUDIO_TRACK_TYPE_unknown = 'unknown';
  public const AUDIO_TRACK_TYPE_primary = 'primary';
  public const AUDIO_TRACK_TYPE_commentary = 'commentary';
  public const AUDIO_TRACK_TYPE_descriptive = 'descriptive';
  public const FAILURE_REASON_unknownFormat = 'unknownFormat';
  public const FAILURE_REASON_unsupportedFormat = 'unsupportedFormat';
  public const FAILURE_REASON_processingFailed = 'processingFailed';
  public const STATUS_serving = 'serving';
  public const STATUS_syncing = 'syncing';
  public const STATUS_failed = 'failed';
  public const TRACK_KIND_standard = 'standard';
  public const TRACK_KIND_ASR = 'ASR';
  public const TRACK_KIND_forced = 'forced';
  /**
   * The type of audio track associated with the caption track.
   *
   * @var string
   */
  public $audioTrackType;
  /**
   * The reason that YouTube failed to process the caption track. This property
   * is only present if the state property's value is failed.
   *
   * @var string
   */
  public $failureReason;
  /**
   * Indicates whether YouTube synchronized the caption track to the audio track
   * in the video. The value will be true if a sync was explicitly requested
   * when the caption track was uploaded. For example, when calling the
   * captions.insert or captions.update methods, you can set the sync parameter
   * to true to instruct YouTube to sync the uploaded track to the video. If the
   * value is false, YouTube uses the time codes in the uploaded caption track
   * to determine when to display captions.
   *
   * @var bool
   */
  public $isAutoSynced;
  /**
   * Indicates whether the track contains closed captions for the deaf and hard
   * of hearing. The default value is false.
   *
   * @var bool
   */
  public $isCC;
  /**
   * Indicates whether the caption track is a draft. If the value is true, then
   * the track is not publicly visible. The default value is false. @mutable
   * youtube.captions.insert youtube.captions.update
   *
   * @var bool
   */
  public $isDraft;
  /**
   * Indicates whether caption track is formatted for "easy reader," meaning it
   * is at a third-grade level for language learners. The default value is
   * false.
   *
   * @var bool
   */
  public $isEasyReader;
  /**
   * Indicates whether the caption track uses large text for the vision-
   * impaired. The default value is false.
   *
   * @var bool
   */
  public $isLarge;
  /**
   * The language of the caption track. The property value is a BCP-47 language
   * tag.
   *
   * @var string
   */
  public $language;
  /**
   * The date and time when the caption track was last updated.
   *
   * @var string
   */
  public $lastUpdated;
  /**
   * The name of the caption track. The name is intended to be visible to the
   * user as an option during playback.
   *
   * @var string
   */
  public $name;
  /**
   * The caption track's status.
   *
   * @var string
   */
  public $status;
  /**
   * The caption track's type.
   *
   * @var string
   */
  public $trackKind;
  /**
   * The ID that YouTube uses to uniquely identify the video associated with the
   * caption track. @mutable youtube.captions.insert
   *
   * @var string
   */
  public $videoId;

  /**
   * The type of audio track associated with the caption track.
   *
   * Accepted values: unknown, primary, commentary, descriptive
   *
   * @param self::AUDIO_TRACK_TYPE_* $audioTrackType
   */
  public function setAudioTrackType($audioTrackType)
  {
    $this->audioTrackType = $audioTrackType;
  }
  /**
   * @return self::AUDIO_TRACK_TYPE_*
   */
  public function getAudioTrackType()
  {
    return $this->audioTrackType;
  }
  /**
   * The reason that YouTube failed to process the caption track. This property
   * is only present if the state property's value is failed.
   *
   * Accepted values: unknownFormat, unsupportedFormat, processingFailed
   *
   * @param self::FAILURE_REASON_* $failureReason
   */
  public function setFailureReason($failureReason)
  {
    $this->failureReason = $failureReason;
  }
  /**
   * @return self::FAILURE_REASON_*
   */
  public function getFailureReason()
  {
    return $this->failureReason;
  }
  /**
   * Indicates whether YouTube synchronized the caption track to the audio track
   * in the video. The value will be true if a sync was explicitly requested
   * when the caption track was uploaded. For example, when calling the
   * captions.insert or captions.update methods, you can set the sync parameter
   * to true to instruct YouTube to sync the uploaded track to the video. If the
   * value is false, YouTube uses the time codes in the uploaded caption track
   * to determine when to display captions.
   *
   * @param bool $isAutoSynced
   */
  public function setIsAutoSynced($isAutoSynced)
  {
    $this->isAutoSynced = $isAutoSynced;
  }
  /**
   * @return bool
   */
  public function getIsAutoSynced()
  {
    return $this->isAutoSynced;
  }
  /**
   * Indicates whether the track contains closed captions for the deaf and hard
   * of hearing. The default value is false.
   *
   * @param bool $isCC
   */
  public function setIsCC($isCC)
  {
    $this->isCC = $isCC;
  }
  /**
   * @return bool
   */
  public function getIsCC()
  {
    return $this->isCC;
  }
  /**
   * Indicates whether the caption track is a draft. If the value is true, then
   * the track is not publicly visible. The default value is false. @mutable
   * youtube.captions.insert youtube.captions.update
   *
   * @param bool $isDraft
   */
  public function setIsDraft($isDraft)
  {
    $this->isDraft = $isDraft;
  }
  /**
   * @return bool
   */
  public function getIsDraft()
  {
    return $this->isDraft;
  }
  /**
   * Indicates whether caption track is formatted for "easy reader," meaning it
   * is at a third-grade level for language learners. The default value is
   * false.
   *
   * @param bool $isEasyReader
   */
  public function setIsEasyReader($isEasyReader)
  {
    $this->isEasyReader = $isEasyReader;
  }
  /**
   * @return bool
   */
  public function getIsEasyReader()
  {
    return $this->isEasyReader;
  }
  /**
   * Indicates whether the caption track uses large text for the vision-
   * impaired. The default value is false.
   *
   * @param bool $isLarge
   */
  public function setIsLarge($isLarge)
  {
    $this->isLarge = $isLarge;
  }
  /**
   * @return bool
   */
  public function getIsLarge()
  {
    return $this->isLarge;
  }
  /**
   * The language of the caption track. The property value is a BCP-47 language
   * tag.
   *
   * @param string $language
   */
  public function setLanguage($language)
  {
    $this->language = $language;
  }
  /**
   * @return string
   */
  public function getLanguage()
  {
    return $this->language;
  }
  /**
   * The date and time when the caption track was last updated.
   *
   * @param string $lastUpdated
   */
  public function setLastUpdated($lastUpdated)
  {
    $this->lastUpdated = $lastUpdated;
  }
  /**
   * @return string
   */
  public function getLastUpdated()
  {
    return $this->lastUpdated;
  }
  /**
   * The name of the caption track. The name is intended to be visible to the
   * user as an option during playback.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * The caption track's status.
   *
   * Accepted values: serving, syncing, failed
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * The caption track's type.
   *
   * Accepted values: standard, ASR, forced
   *
   * @param self::TRACK_KIND_* $trackKind
   */
  public function setTrackKind($trackKind)
  {
    $this->trackKind = $trackKind;
  }
  /**
   * @return self::TRACK_KIND_*
   */
  public function getTrackKind()
  {
    return $this->trackKind;
  }
  /**
   * The ID that YouTube uses to uniquely identify the video associated with the
   * caption track. @mutable youtube.captions.insert
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CaptionSnippet::class, 'Google_Service_YouTube_CaptionSnippet');
