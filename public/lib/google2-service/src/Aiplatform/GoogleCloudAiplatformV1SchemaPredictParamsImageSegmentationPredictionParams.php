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

class GoogleCloudAiplatformV1SchemaPredictParamsImageSegmentationPredictionParams extends \Google\Model
{
  /**
   * When the model predicts category of pixels of the image, it will only
   * provide predictions for pixels that it is at least this much confident
   * about. All other pixels will be classified as background. Default value is
   * 0.5.
   *
   * @var float
   */
  public $confidenceThreshold;

  /**
   * When the model predicts category of pixels of the image, it will only
   * provide predictions for pixels that it is at least this much confident
   * about. All other pixels will be classified as background. Default value is
   * 0.5.
   *
   * @param float $confidenceThreshold
   */
  public function setConfidenceThreshold($confidenceThreshold)
  {
    $this->confidenceThreshold = $confidenceThreshold;
  }
  /**
   * @return float
   */
  public function getConfidenceThreshold()
  {
    return $this->confidenceThreshold;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaPredictParamsImageSegmentationPredictionParams::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaPredictParamsImageSegmentationPredictionParams');
