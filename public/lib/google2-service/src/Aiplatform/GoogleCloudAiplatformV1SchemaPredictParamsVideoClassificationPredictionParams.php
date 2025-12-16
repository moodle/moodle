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

class GoogleCloudAiplatformV1SchemaPredictParamsVideoClassificationPredictionParams extends \Google\Model
{
  /**
   * The Model only returns predictions with at least this confidence score.
   * Default value is 0.0
   *
   * @var float
   */
  public $confidenceThreshold;
  /**
   * The Model only returns up to that many top, by confidence score,
   * predictions per instance. If this number is very high, the Model may return
   * fewer predictions. Default value is 10,000.
   *
   * @var int
   */
  public $maxPredictions;
  /**
   * Set to true to request classification for a video at one-second intervals.
   * Vertex AI returns labels and their confidence scores for each second of the
   * entire time segment of the video that user specified in the input WARNING:
   * Model evaluation is not done for this classification type, the quality of
   * it depends on the training data, but there are no metrics provided to
   * describe that quality. Default value is false
   *
   * @var bool
   */
  public $oneSecIntervalClassification;
  /**
   * Set to true to request segment-level classification. Vertex AI returns
   * labels and their confidence scores for the entire time segment of the video
   * that user specified in the input instance. Default value is true
   *
   * @var bool
   */
  public $segmentClassification;
  /**
   * Set to true to request shot-level classification. Vertex AI determines the
   * boundaries for each camera shot in the entire time segment of the video
   * that user specified in the input instance. Vertex AI then returns labels
   * and their confidence scores for each detected shot, along with the start
   * and end time of the shot. WARNING: Model evaluation is not done for this
   * classification type, the quality of it depends on the training data, but
   * there are no metrics provided to describe that quality. Default value is
   * false
   *
   * @var bool
   */
  public $shotClassification;

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
   * The Model only returns up to that many top, by confidence score,
   * predictions per instance. If this number is very high, the Model may return
   * fewer predictions. Default value is 10,000.
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
   * Set to true to request classification for a video at one-second intervals.
   * Vertex AI returns labels and their confidence scores for each second of the
   * entire time segment of the video that user specified in the input WARNING:
   * Model evaluation is not done for this classification type, the quality of
   * it depends on the training data, but there are no metrics provided to
   * describe that quality. Default value is false
   *
   * @param bool $oneSecIntervalClassification
   */
  public function setOneSecIntervalClassification($oneSecIntervalClassification)
  {
    $this->oneSecIntervalClassification = $oneSecIntervalClassification;
  }
  /**
   * @return bool
   */
  public function getOneSecIntervalClassification()
  {
    return $this->oneSecIntervalClassification;
  }
  /**
   * Set to true to request segment-level classification. Vertex AI returns
   * labels and their confidence scores for the entire time segment of the video
   * that user specified in the input instance. Default value is true
   *
   * @param bool $segmentClassification
   */
  public function setSegmentClassification($segmentClassification)
  {
    $this->segmentClassification = $segmentClassification;
  }
  /**
   * @return bool
   */
  public function getSegmentClassification()
  {
    return $this->segmentClassification;
  }
  /**
   * Set to true to request shot-level classification. Vertex AI determines the
   * boundaries for each camera shot in the entire time segment of the video
   * that user specified in the input instance. Vertex AI then returns labels
   * and their confidence scores for each detected shot, along with the start
   * and end time of the shot. WARNING: Model evaluation is not done for this
   * classification type, the quality of it depends on the training data, but
   * there are no metrics provided to describe that quality. Default value is
   * false
   *
   * @param bool $shotClassification
   */
  public function setShotClassification($shotClassification)
  {
    $this->shotClassification = $shotClassification;
  }
  /**
   * @return bool
   */
  public function getShotClassification()
  {
    return $this->shotClassification;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaPredictParamsVideoClassificationPredictionParams::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaPredictParamsVideoClassificationPredictionParams');
