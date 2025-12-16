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

class SpeechRecognitionResult extends \Google\Collection
{
  protected $collection_key = 'alternatives';
  protected $alternativesType = SpeechRecognitionAlternative::class;
  protected $alternativesDataType = 'array';
  /**
   * For multi-channel audio, this is the channel number corresponding to the
   * recognized result for the audio from that channel. For audio_channel_count
   * = N, its output values can range from '1' to 'N'.
   *
   * @var int
   */
  public $channelTag;
  /**
   * Output only. The [BCP-47](https://www.rfc-editor.org/rfc/bcp/bcp47.txt)
   * language tag of the language in this result. This language code was
   * detected to have the most likelihood of being spoken in the audio.
   *
   * @var string
   */
  public $languageCode;
  /**
   * Time offset of the end of this result relative to the beginning of the
   * audio.
   *
   * @var string
   */
  public $resultEndTime;

  /**
   * May contain one or more recognition hypotheses (up to the maximum specified
   * in `max_alternatives`). These alternatives are ordered in terms of
   * accuracy, with the top (first) alternative being the most probable, as
   * ranked by the recognizer.
   *
   * @param SpeechRecognitionAlternative[] $alternatives
   */
  public function setAlternatives($alternatives)
  {
    $this->alternatives = $alternatives;
  }
  /**
   * @return SpeechRecognitionAlternative[]
   */
  public function getAlternatives()
  {
    return $this->alternatives;
  }
  /**
   * For multi-channel audio, this is the channel number corresponding to the
   * recognized result for the audio from that channel. For audio_channel_count
   * = N, its output values can range from '1' to 'N'.
   *
   * @param int $channelTag
   */
  public function setChannelTag($channelTag)
  {
    $this->channelTag = $channelTag;
  }
  /**
   * @return int
   */
  public function getChannelTag()
  {
    return $this->channelTag;
  }
  /**
   * Output only. The [BCP-47](https://www.rfc-editor.org/rfc/bcp/bcp47.txt)
   * language tag of the language in this result. This language code was
   * detected to have the most likelihood of being spoken in the audio.
   *
   * @param string $languageCode
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
  /**
   * Time offset of the end of this result relative to the beginning of the
   * audio.
   *
   * @param string $resultEndTime
   */
  public function setResultEndTime($resultEndTime)
  {
    $this->resultEndTime = $resultEndTime;
  }
  /**
   * @return string
   */
  public function getResultEndTime()
  {
    return $this->resultEndTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SpeechRecognitionResult::class, 'Google_Service_Speech_SpeechRecognitionResult');
