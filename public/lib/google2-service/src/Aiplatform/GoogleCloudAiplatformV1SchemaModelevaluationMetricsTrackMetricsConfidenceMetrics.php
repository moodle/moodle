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

class GoogleCloudAiplatformV1SchemaModelevaluationMetricsTrackMetricsConfidenceMetrics extends \Google\Model
{
  /**
   * Bounding box intersection-over-union precision. Measures how well the
   * bounding boxes overlap between each other (e.g. complete overlap or just
   * barely above iou_threshold).
   *
   * @var float
   */
  public $boundingBoxIou;
  /**
   * The confidence threshold value used to compute the metrics.
   *
   * @var float
   */
  public $confidenceThreshold;
  /**
   * Mismatch rate, which measures the tracking consistency, i.e. correctness of
   * instance ID continuity.
   *
   * @var float
   */
  public $mismatchRate;
  /**
   * Tracking precision.
   *
   * @var float
   */
  public $trackingPrecision;
  /**
   * Tracking recall.
   *
   * @var float
   */
  public $trackingRecall;

  /**
   * Bounding box intersection-over-union precision. Measures how well the
   * bounding boxes overlap between each other (e.g. complete overlap or just
   * barely above iou_threshold).
   *
   * @param float $boundingBoxIou
   */
  public function setBoundingBoxIou($boundingBoxIou)
  {
    $this->boundingBoxIou = $boundingBoxIou;
  }
  /**
   * @return float
   */
  public function getBoundingBoxIou()
  {
    return $this->boundingBoxIou;
  }
  /**
   * The confidence threshold value used to compute the metrics.
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
   * Mismatch rate, which measures the tracking consistency, i.e. correctness of
   * instance ID continuity.
   *
   * @param float $mismatchRate
   */
  public function setMismatchRate($mismatchRate)
  {
    $this->mismatchRate = $mismatchRate;
  }
  /**
   * @return float
   */
  public function getMismatchRate()
  {
    return $this->mismatchRate;
  }
  /**
   * Tracking precision.
   *
   * @param float $trackingPrecision
   */
  public function setTrackingPrecision($trackingPrecision)
  {
    $this->trackingPrecision = $trackingPrecision;
  }
  /**
   * @return float
   */
  public function getTrackingPrecision()
  {
    return $this->trackingPrecision;
  }
  /**
   * Tracking recall.
   *
   * @param float $trackingRecall
   */
  public function setTrackingRecall($trackingRecall)
  {
    $this->trackingRecall = $trackingRecall;
  }
  /**
   * @return float
   */
  public function getTrackingRecall()
  {
    return $this->trackingRecall;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaModelevaluationMetricsTrackMetricsConfidenceMetrics::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaModelevaluationMetricsTrackMetricsConfidenceMetrics');
