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

namespace Google\Service\Speech;

class SpeakerDiarizationConfig extends \Google\Model
{
  /**
   * If 'true', enables speaker detection for each recognized word in the top
   * alternative of the recognition result using a speaker_label provided in the
   * WordInfo.
   *
   * @var bool
   */
  public $enableSpeakerDiarization;
  /**
   * Maximum number of speakers in the conversation. This range gives you more
   * flexibility by allowing the system to automatically determine the correct
   * number of speakers. If not set, the default value is 6.
   *
   * @var int
   */
  public $maxSpeakerCount;
  /**
   * Minimum number of speakers in the conversation. This range gives you more
   * flexibility by allowing the system to automatically determine the correct
   * number of speakers. If not set, the default value is 2.
   *
   * @var int
   */
  public $minSpeakerCount;
  /**
   * Output only. Unused.
   *
   * @deprecated
   * @var int
   */
  public $speakerTag;

  /**
   * If 'true', enables speaker detection for each recognized word in the top
   * alternative of the recognition result using a speaker_label provided in the
   * WordInfo.
   *
   * @param bool $enableSpeakerDiarization
   */
  public function setEnableSpeakerDiarization($enableSpeakerDiarization)
  {
    $this->enableSpeakerDiarization = $enableSpeakerDiarization;
  }
  /**
   * @return bool
   */
  public function getEnableSpeakerDiarization()
  {
    return $this->enableSpeakerDiarization;
  }
  /**
   * Maximum number of speakers in the conversation. This range gives you more
   * flexibility by allowing the system to automatically determine the correct
   * number of speakers. If not set, the default value is 6.
   *
   * @param int $maxSpeakerCount
   */
  public function setMaxSpeakerCount($maxSpeakerCount)
  {
    $this->maxSpeakerCount = $maxSpeakerCount;
  }
  /**
   * @return int
   */
  public function getMaxSpeakerCount()
  {
    return $this->maxSpeakerCount;
  }
  /**
   * Minimum number of speakers in the conversation. This range gives you more
   * flexibility by allowing the system to automatically determine the correct
   * number of speakers. If not set, the default value is 2.
   *
   * @param int $minSpeakerCount
   */
  public function setMinSpeakerCount($minSpeakerCount)
  {
    $this->minSpeakerCount = $minSpeakerCount;
  }
  /**
   * @return int
   */
  public function getMinSpeakerCount()
  {
    return $this->minSpeakerCount;
  }
  /**
   * Output only. Unused.
   *
   * @deprecated
   * @param int $speakerTag
   */
  public function setSpeakerTag($speakerTag)
  {
    $this->speakerTag = $speakerTag;
  }
  /**
   * @deprecated
   * @return int
   */
  public function getSpeakerTag()
  {
    return $this->speakerTag;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SpeakerDiarizationConfig::class, 'Google_Service_Speech_SpeakerDiarizationConfig');
