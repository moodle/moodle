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

class GoogleCloudAiplatformV1SchemaPredictPredictionTabularClassificationPredictionResult extends \Google\Collection
{
  protected $collection_key = 'scores';
  /**
   * The name of the classes being classified, contains all possible values of
   * the target column.
   *
   * @var string[]
   */
  public $classes;
  /**
   * The model's confidence in each class being correct, higher value means
   * higher confidence. The N-th score corresponds to the N-th class in classes.
   *
   * @var float[]
   */
  public $scores;

  /**
   * The name of the classes being classified, contains all possible values of
   * the target column.
   *
   * @param string[] $classes
   */
  public function setClasses($classes)
  {
    $this->classes = $classes;
  }
  /**
   * @return string[]
   */
  public function getClasses()
  {
    return $this->classes;
  }
  /**
   * The model's confidence in each class being correct, higher value means
   * higher confidence. The N-th score corresponds to the N-th class in classes.
   *
   * @param float[] $scores
   */
  public function setScores($scores)
  {
    $this->scores = $scores;
  }
  /**
   * @return float[]
   */
  public function getScores()
  {
    return $this->scores;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaPredictPredictionTabularClassificationPredictionResult::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaPredictPredictionTabularClassificationPredictionResult');
