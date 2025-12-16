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

class GoogleCloudAiplatformV1QuestionAnsweringRelevanceResult extends \Google\Model
{
  /**
   * Output only. Confidence for question answering relevance score.
   *
   * @var float
   */
  public $confidence;
  /**
   * Output only. Explanation for question answering relevance score.
   *
   * @var string
   */
  public $explanation;
  /**
   * Output only. Question Answering Relevance score.
   *
   * @var float
   */
  public $score;

  /**
   * Output only. Confidence for question answering relevance score.
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
   * Output only. Explanation for question answering relevance score.
   *
   * @param string $explanation
   */
  public function setExplanation($explanation)
  {
    $this->explanation = $explanation;
  }
  /**
   * @return string
   */
  public function getExplanation()
  {
    return $this->explanation;
  }
  /**
   * Output only. Question Answering Relevance score.
   *
   * @param float $score
   */
  public function setScore($score)
  {
    $this->score = $score;
  }
  /**
   * @return float
   */
  public function getScore()
  {
    return $this->score;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1QuestionAnsweringRelevanceResult::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1QuestionAnsweringRelevanceResult');
