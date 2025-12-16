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

class GoogleCloudDialogflowCxV3LanguageInfo extends \Google\Model
{
  /**
   * The confidence score of the detected language between 0 and 1.
   *
   * @var float
   */
  public $confidenceScore;
  /**
   * The language code specified in the original request.
   *
   * @var string
   */
  public $inputLanguageCode;
  /**
   * The language code detected for this request based on the user conversation.
   *
   * @var string
   */
  public $resolvedLanguageCode;

  /**
   * The confidence score of the detected language between 0 and 1.
   *
   * @param float $confidenceScore
   */
  public function setConfidenceScore($confidenceScore)
  {
    $this->confidenceScore = $confidenceScore;
  }
  /**
   * @return float
   */
  public function getConfidenceScore()
  {
    return $this->confidenceScore;
  }
  /**
   * The language code specified in the original request.
   *
   * @param string $inputLanguageCode
   */
  public function setInputLanguageCode($inputLanguageCode)
  {
    $this->inputLanguageCode = $inputLanguageCode;
  }
  /**
   * @return string
   */
  public function getInputLanguageCode()
  {
    return $this->inputLanguageCode;
  }
  /**
   * The language code detected for this request based on the user conversation.
   *
   * @param string $resolvedLanguageCode
   */
  public function setResolvedLanguageCode($resolvedLanguageCode)
  {
    $this->resolvedLanguageCode = $resolvedLanguageCode;
  }
  /**
   * @return string
   */
  public function getResolvedLanguageCode()
  {
    return $this->resolvedLanguageCode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3LanguageInfo::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3LanguageInfo');
