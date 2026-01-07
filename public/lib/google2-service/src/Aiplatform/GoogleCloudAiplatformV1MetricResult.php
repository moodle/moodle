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

class GoogleCloudAiplatformV1MetricResult extends \Google\Collection
{
  protected $collection_key = 'rubricVerdicts';
  protected $errorType = GoogleRpcStatus::class;
  protected $errorDataType = '';
  /**
   * Output only. The explanation for the metric result.
   *
   * @var string
   */
  public $explanation;
  protected $rubricVerdictsType = GoogleCloudAiplatformV1RubricVerdict::class;
  protected $rubricVerdictsDataType = 'array';
  /**
   * Output only. The score for the metric. Please refer to each metric's
   * documentation for the meaning of the score.
   *
   * @var float
   */
  public $score;

  /**
   * Output only. The error status for the metric result.
   *
   * @param GoogleRpcStatus $error
   */
  public function setError(GoogleRpcStatus $error)
  {
    $this->error = $error;
  }
  /**
   * @return GoogleRpcStatus
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * Output only. The explanation for the metric result.
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
   * Output only. For rubric-based metrics, the verdicts for each rubric.
   *
   * @param GoogleCloudAiplatformV1RubricVerdict[] $rubricVerdicts
   */
  public function setRubricVerdicts($rubricVerdicts)
  {
    $this->rubricVerdicts = $rubricVerdicts;
  }
  /**
   * @return GoogleCloudAiplatformV1RubricVerdict[]
   */
  public function getRubricVerdicts()
  {
    return $this->rubricVerdicts;
  }
  /**
   * Output only. The score for the metric. Please refer to each metric's
   * documentation for the meaning of the score.
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
class_alias(GoogleCloudAiplatformV1MetricResult::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1MetricResult');
