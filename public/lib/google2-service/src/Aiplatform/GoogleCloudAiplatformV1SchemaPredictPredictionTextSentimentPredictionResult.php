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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1SchemaPredictPredictionTextSentimentPredictionResult extends \Google\Model
{
  /**
   * The integer sentiment labels between 0 (inclusive) and sentimentMax label
   * (inclusive), while 0 maps to the least positive sentiment and sentimentMax
   * maps to the most positive one. The higher the score is, the more positive
   * the sentiment in the text snippet is. Note: sentimentMax is an integer
   * value between 1 (inclusive) and 10 (inclusive).
   *
   * @var int
   */
  public $sentiment;

  /**
   * The integer sentiment labels between 0 (inclusive) and sentimentMax label
   * (inclusive), while 0 maps to the least positive sentiment and sentimentMax
   * maps to the most positive one. The higher the score is, the more positive
   * the sentiment in the text snippet is. Note: sentimentMax is an integer
   * value between 1 (inclusive) and 10 (inclusive).
   *
   * @param int $sentiment
   */
  public function setSentiment($sentiment)
  {
    $this->sentiment = $sentiment;
  }
  /**
   * @return int
   */
  public function getSentiment()
  {
    return $this->sentiment;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaPredictPredictionTextSentimentPredictionResult::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaPredictPredictionTextSentimentPredictionResult');
