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

namespace Google\Service\Vision;

class TextDetectionParams extends \Google\Collection
{
  protected $collection_key = 'advancedOcrOptions';
  /**
   * A list of advanced OCR options to further fine-tune OCR behavior. Current
   * valid values are: - `legacy_layout`: a heuristics layout detection
   * algorithm, which serves as an alternative to the current ML-based layout
   * detection algorithm. Customers can choose the best suitable layout
   * algorithm based on their situation.
   *
   * @var string[]
   */
  public $advancedOcrOptions;
  /**
   * By default, Cloud Vision API only includes confidence score for
   * DOCUMENT_TEXT_DETECTION result. Set the flag to true to include confidence
   * score for TEXT_DETECTION as well.
   *
   * @var bool
   */
  public $enableTextDetectionConfidenceScore;

  /**
   * A list of advanced OCR options to further fine-tune OCR behavior. Current
   * valid values are: - `legacy_layout`: a heuristics layout detection
   * algorithm, which serves as an alternative to the current ML-based layout
   * detection algorithm. Customers can choose the best suitable layout
   * algorithm based on their situation.
   *
   * @param string[] $advancedOcrOptions
   */
  public function setAdvancedOcrOptions($advancedOcrOptions)
  {
    $this->advancedOcrOptions = $advancedOcrOptions;
  }
  /**
   * @return string[]
   */
  public function getAdvancedOcrOptions()
  {
    return $this->advancedOcrOptions;
  }
  /**
   * By default, Cloud Vision API only includes confidence score for
   * DOCUMENT_TEXT_DETECTION result. Set the flag to true to include confidence
   * score for TEXT_DETECTION as well.
   *
   * @param bool $enableTextDetectionConfidenceScore
   */
  public function setEnableTextDetectionConfidenceScore($enableTextDetectionConfidenceScore)
  {
    $this->enableTextDetectionConfidenceScore = $enableTextDetectionConfidenceScore;
  }
  /**
   * @return bool
   */
  public function getEnableTextDetectionConfidenceScore()
  {
    return $this->enableTextDetectionConfidenceScore;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TextDetectionParams::class, 'Google_Service_Vision_TextDetectionParams');
