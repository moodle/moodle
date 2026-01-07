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

class GoogleCloudAiplatformV1SchemaPredictParamsVideoObjectTrackingPredictionParams extends \Google\Model
{
  /**
   * The Model only returns predictions with at least this confidence score.
   * Default value is 0.0
   *
   * @var float
   */
  public $confidenceThreshold;
  /**
   * The model only returns up to that many top, by confidence score,
   * predictions per frame of the video. If this number is very high, the Model
   * may return fewer predictions per frame. Default value is 50.
   *
   * @var int
   */
  public $maxPredictions;
  /**
   * Only bounding boxes with shortest edge at least that long as a relative
   * value of video frame size are returned. Default value is 0.0.
   *
   * @var float
   */
  public $minBoundingBoxSize;

  /**
   * The Model only returns predictions with at least this confidence score.
   * Default value is 0.0
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
  /**
   * The model only returns up to that many top, by confidence score,
   * predictions per frame of the video. If this number is very high, the Model
   * may return fewer predictions per frame. Default value is 50.
   *
   * @param int $maxPredictions
   */
  public function setMaxPredictions($maxPredictions)
  {
    $this->maxPredictions = $maxPredictions;
  }
  /**
   * @return int
   */
  public function getMaxPredictions()
  {
    return $this->maxPredictions;
  }
  /**
   * Only bounding boxes with shortest edge at least that long as a relative
   * value of video frame size are returned. Default value is 0.0.
   *
   * @param float $minBoundingBoxSize
   */
  public function setMinBoundingBoxSize($minBoundingBoxSize)
  {
    $this->minBoundingBoxSize = $minBoundingBoxSize;
  }
  /**
   * @return float
   */
  public function getMinBoundingBoxSize()
  {
    return $this->minBoundingBoxSize;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaPredictParamsVideoObjectTrackingPredictionParams::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaPredictParamsVideoObjectTrackingPredictionParams');
