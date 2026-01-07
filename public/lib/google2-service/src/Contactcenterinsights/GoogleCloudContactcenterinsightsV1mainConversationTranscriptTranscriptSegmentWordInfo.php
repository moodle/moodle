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

namespace Google\Service\Contactcenterinsights;

class GoogleCloudContactcenterinsightsV1mainConversationTranscriptTranscriptSegmentWordInfo extends \Google\Model
{
  /**
   * A confidence estimate between 0.0 and 1.0 of the fidelity of this word. A
   * default value of 0.0 indicates that the value is unset.
   *
   * @var float
   */
  public $confidence;
  /**
   * Time offset of the end of this word relative to the beginning of the total
   * conversation.
   *
   * @var string
   */
  public $endOffset;
  /**
   * Time offset of the start of this word relative to the beginning of the
   * total conversation.
   *
   * @var string
   */
  public $startOffset;
  /**
   * The word itself. Includes punctuation marks that surround the word.
   *
   * @var string
   */
  public $word;

  /**
   * A confidence estimate between 0.0 and 1.0 of the fidelity of this word. A
   * default value of 0.0 indicates that the value is unset.
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
   * Time offset of the end of this word relative to the beginning of the total
   * conversation.
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
   * Time offset of the start of this word relative to the beginning of the
   * total conversation.
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
   * The word itself. Includes punctuation marks that surround the word.
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
class_alias(GoogleCloudContactcenterinsightsV1mainConversationTranscriptTranscriptSegmentWordInfo::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1mainConversationTranscriptTranscriptSegmentWordInfo');
