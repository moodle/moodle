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

namespace Google\Service\CloudNaturalLanguage;

class XPSTrackMetricsEntryConfidenceMetricsEntry extends \Google\Model
{
  /**
   * Output only. Bounding box intersection-over-union precision. Measures how
   * well the bounding boxes overlap between each other (e.g. complete overlap
   * or just barely above iou_threshold).
   *
   * @var float
   */
  public $boundingBoxIou;
  /**
   * Output only. The confidence threshold value used to compute the metrics.
   *
   * @var float
   */
  public $confidenceThreshold;
  /**
   * Output only. Mismatch rate, which measures the tracking consistency, i.e.
   * correctness of instance ID continuity.
   *
   * @var float
   */
  public $mismatchRate;
  /**
   * Output only. Tracking precision.
   *
   * @var float
   */
  public $trackingPrecision;
  /**
   * Output only. Tracking recall.
   *
   * @var float
   */
  public $trackingRecall;

  /**
   * Output only. Bounding box intersection-over-union precision. Measures how
   * well the bounding boxes overlap between each other (e.g. complete overlap
   * or just barely above iou_threshold).
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
   * Output only. The confidence threshold value used to compute the metrics.
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
   * Output only. Mismatch rate, which measures the tracking consistency, i.e.
   * correctness of instance ID continuity.
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
   * Output only. Tracking precision.
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
   * Output only. Tracking recall.
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
class_alias(XPSTrackMetricsEntryConfidenceMetricsEntry::class, 'Google_Service_CloudNaturalLanguage_XPSTrackMetricsEntryConfidenceMetricsEntry');
