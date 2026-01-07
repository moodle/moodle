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

class GoogleCloudAiplatformV1SchemaPredictPredictionClassificationPredictionResult extends \Google\Collection
{
  protected $collection_key = 'ids';
  /**
   * The Model's confidences in correctness of the predicted IDs, higher value
   * means higher confidence. Order matches the Ids.
   *
   * @var float[]
   */
  public $confidences;
  /**
   * The display names of the AnnotationSpecs that had been identified, order
   * matches the IDs.
   *
   * @var string[]
   */
  public $displayNames;
  /**
   * The resource IDs of the AnnotationSpecs that had been identified.
   *
   * @var string[]
   */
  public $ids;

  /**
   * The Model's confidences in correctness of the predicted IDs, higher value
   * means higher confidence. Order matches the Ids.
   *
   * @param float[] $confidences
   */
  public function setConfidences($confidences)
  {
    $this->confidences = $confidences;
  }
  /**
   * @return float[]
   */
  public function getConfidences()
  {
    return $this->confidences;
  }
  /**
   * The display names of the AnnotationSpecs that had been identified, order
   * matches the IDs.
   *
   * @param string[] $displayNames
   */
  public function setDisplayNames($displayNames)
  {
    $this->displayNames = $displayNames;
  }
  /**
   * @return string[]
   */
  public function getDisplayNames()
  {
    return $this->displayNames;
  }
  /**
   * The resource IDs of the AnnotationSpecs that had been identified.
   *
   * @param string[] $ids
   */
  public function setIds($ids)
  {
    $this->ids = $ids;
  }
  /**
   * @return string[]
   */
  public function getIds()
  {
    return $this->ids;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaPredictPredictionClassificationPredictionResult::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaPredictPredictionClassificationPredictionResult');
