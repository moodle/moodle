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

namespace Google\Service\CloudVideoIntelligence;

class GoogleCloudVideointelligenceV1beta2WordInfo extends \Google\Model
{
  /**
   * Output only. The confidence estimate between 0.0 and 1.0. A higher number
   * indicates an estimated greater likelihood that the recognized words are
   * correct. This field is set only for the top alternative. This field is not
   * guaranteed to be accurate and users should not rely on it to be always
   * provided. The default of 0.0 is a sentinel value indicating `confidence`
   * was not set.
   *
   * @var float
   */
  public $confidence;
  /**
   * Time offset relative to the beginning of the audio, and corresponding to
   * the end of the spoken word. This field is only set if
   * `enable_word_time_offsets=true` and only in the top hypothesis. This is an
   * experimental feature and the accuracy of the time offset can vary.
   *
   * @var string
   */
  public $endTime;
  /**
   * Output only. A distinct integer value is assigned for every speaker within
   * the audio. This field specifies which one of those speakers was detected to
   * have spoken this word. Value ranges from 1 up to diarization_speaker_count,
   * and is only set if speaker diarization is enabled.
   *
   * @var int
   */
  public $speakerTag;
  /**
   * Time offset relative to the beginning of the audio, and corresponding to
   * the start of the spoken word. This field is only set if
   * `enable_word_time_offsets=true` and only in the top hypothesis. This is an
   * experimental feature and the accuracy of the time offset can vary.
   *
   * @var string
   */
  public $startTime;
  /**
   * The word corresponding to this set of information.
   *
   * @var string
   */
  public $word;

  /**
   * Output only. The confidence estimate between 0.0 and 1.0. A higher number
   * indicates an estimated greater likelihood that the recognized words are
   * correct. This field is set only for the top alternative. This field is not
   * guaranteed to be accurate and users should not rely on it to be always
   * provided. The default of 0.0 is a sentinel value indicating `confidence`
   * was not set.
   *
   * @param float $confidence
   */
  public function setConfidence($confidence)
  {
    $this->confidence = $confidence;
  }
  /**
   * @return float
   */
  public function getConfidence()
  {
    return $this->confidence;
  }
  /**
   * Time offset relative to the beginning of the audio, and corresponding to
   * the end of the spoken word. This field is only set if
   * `enable_word_time_offsets=true` and only in the top hypothesis. This is an
   * experimental feature and the accuracy of the time offset can vary.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Output only. A distinct integer value is assigned for every speaker within
   * the audio. This field specifies which one of those speakers was detected to
   * have spoken this word. Value ranges from 1 up to diarization_speaker_count,
   * and is only set if speaker diarization is enabled.
   *
   * @param int $speakerTag
   */
  public function setSpeakerTag($speakerTag)
  {
    $this->speakerTag = $speakerTag;
  }
  /**
   * @return int
   */
  public function getSpeakerTag()
  {
    return $this->speakerTag;
  }
  /**
   * Time offset relative to the beginning of the audio, and corresponding to
   * the start of the spoken word. This field is only set if
   * `enable_word_time_offsets=true` and only in the top hypothesis. This is an
   * experimental feature and the accuracy of the time offset can vary.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * The word corresponding to this set of information.
   *
   * @param string $word
   */
  public function setWord($word)
  {
    $this->word = $word;
  }
  /**
   * @return string
   */
  public function getWord()
  {
    return $this->word;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudVideointelligenceV1beta2WordInfo::class, 'Google_Service_CloudVideoIntelligence_GoogleCloudVideointelligenceV1beta2WordInfo');
