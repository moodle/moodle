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

class XPSTrackMetricsEntry extends \Google\Collection
{
  protected $collection_key = 'confidenceMetricsEntries';
  protected $confidenceMetricsEntriesType = XPSTrackMetricsEntryConfidenceMetricsEntry::class;
  protected $confidenceMetricsEntriesDataType = 'array';
  /**
   * Output only. The intersection-over-union threshold value between bounding
   * boxes across frames used to compute this metric entry.
   *
   * @var float
   */
  public $iouThreshold;
  /**
   * Output only. The mean bounding box iou over all confidence thresholds.
   *
   * @var float
   */
  public $meanBoundingBoxIou;
  /**
   * Output only. The mean mismatch rate over all confidence thresholds.
   *
   * @var float
   */
  public $meanMismatchRate;
  /**
   * Output only. The mean average precision over all confidence thresholds.
   *
   * @var float
   */
  public $meanTrackingAveragePrecision;

  /**
   * Output only. Metrics for each label-match confidence_threshold from
   * 0.05,0.10,...,0.95,0.96,0.97,0.98,0.99. Precision-recall curve is derived
   * from them.
   *
   * @param XPSTrackMetricsEntryConfidenceMetricsEntry[] $confidenceMetricsEntries
   */
  public function setConfidenceMetricsEntries($confidenceMetricsEntries)
  {
    $this->confidenceMetricsEntries = $confidenceMetricsEntries;
  }
  /**
   * @return XPSTrackMetricsEntryConfidenceMetricsEntry[]
   */
  public function getConfidenceMetricsEntries()
  {
    return $this->confidenceMetricsEntries;
  }
  /**
   * Output only. The intersection-over-union threshold value between bounding
   * boxes across frames used to compute this metric entry.
   *
   * @param float $iouThreshold
   */
  public function setIouThreshold($iouThreshold)
  {
    $this->iouThreshold = $iouThreshold;
  }
  /**
   * @return float
   */
  public function getIouThreshold()
  {
    return $this->iouThreshold;
  }
  /**
   * Output only. The mean bounding box iou over all confidence thresholds.
   *
   * @param float $meanBoundingBoxIou
   */
  public function setMeanBoundingBoxIou($meanBoundingBoxIou)
  {
    $this->meanBoundingBoxIou = $meanBoundingBoxIou;
  }
  /**
   * @return float
   */
  public function getMeanBoundingBoxIou()
  {
    return $this->meanBoundingBoxIou;
  }
  /**
   * Output only. The mean mismatch rate over all confidence thresholds.
   *
   * @param float $meanMismatchRate
   */
  public function setMeanMismatchRate($meanMismatchRate)
  {
    $this->meanMismatchRate = $meanMismatchRate;
  }
  /**
   * @return float
   */
  public function getMeanMismatchRate()
  {
    return $this->meanMismatchRate;
  }
  /**
   * Output only. The mean average precision over all confidence thresholds.
   *
   * @param float $meanTrackingAveragePrecision
   */
  public function setMeanTrackingAveragePrecision($meanTrackingAveragePrecision)
  {
    $this->meanTrackingAveragePrecision = $meanTrackingAveragePrecision;
  }
  /**
   * @return float
   */
  public function getMeanTrackingAveragePrecision()
  {
    return $this->meanTrackingAveragePrecision;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSTrackMetricsEntry::class, 'Google_Service_CloudNaturalLanguage_XPSTrackMetricsEntry');
