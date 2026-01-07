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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowV2beta1SpeechWordInfo extends \Google\Model
{
  /**
   * The Speech confidence between 0.0 and 1.0 for this word. A higher number
   * indicates an estimated greater likelihood that the recognized word is
   * correct. The default of 0.0 is a sentinel value indicating that confidence
   * was not set. This field is not guaranteed to be fully stable over time for
   * the same audio input. Users should also not rely on it to always be
   * provided.
   *
   * @var float
   */
  public $confidence;
  /**
   * Time offset relative to the beginning of the audio that corresponds to the
   * end of the spoken word. This is an experimental feature and the accuracy of
   * the time offset can vary.
   *
   * @var string
   */
  public $endOffset;
  /**
   * Time offset relative to the beginning of the audio that corresponds to the
   * start of the spoken word. This is an experimental feature and the accuracy
   * of the time offset can vary.
   *
   * @var string
   */
  public $startOffset;
  /**
   * The word this info is for.
   *
   * @var string
   */
  public $word;

  /**
   * The Speech confidence between 0.0 and 1.0 for this word. A higher number
   * indicates an estimated greater likelihood that the recognized word is
   * correct. The default of 0.0 is a sentinel value indicating that confidence
   * was not set. This field is not guaranteed to be fully stable over time for
   * the same audio input. Users should also not rely on it to always be
   * provided.
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
   * Time offset relative to the beginning of the audio that corresponds to the
   * end of the spoken word. This is an experimental feature and the accuracy of
   * the time offset can vary.
   *
   * @param string $endOffset
   */
  public function setEndOffset($endOffset)
  {
    $this->endOffset = $endOffset;
  }
  /**
   * @return string
   */
  public function getEndOffset()
  {
    return $this->endOffset;
  }
  /**
   * Time offset relative to the beginning of the audio that corresponds to the
   * start of the spoken word. This is an experimental feature and the accuracy
   * of the time offset can vary.
   *
   * @param string $startOffset
   */
  public function setStartOffset($startOffset)
  {
    $this->startOffset = $startOffset;
  }
  /**
   * @return string
   */
  public function getStartOffset()
  {
    return $this->startOffset;
  }
  /**
   * The word this info is for.
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
class_alias(GoogleCloudDialogflowV2beta1SpeechWordInfo::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2beta1SpeechWordInfo');
