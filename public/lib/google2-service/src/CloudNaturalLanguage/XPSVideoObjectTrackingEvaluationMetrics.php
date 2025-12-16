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

class XPSVideoObjectTrackingEvaluationMetrics extends \Google\Collection
{
  protected $collection_key = 'trackMetricsEntries';
  /**
   * Output only. The single metric for bounding boxes evaluation: the
   * mean_average_precision averaged over all bounding_box_metrics_entries.
   *
   * @var float
   */
  public $boundingBoxMeanAveragePrecision;
  protected $boundingBoxMetricsEntriesType = XPSBoundingBoxMetricsEntry::class;
  protected $boundingBoxMetricsEntriesDataType = 'array';
  /**
   * The number of bounding boxes used for model evaluation.
   *
   * @var int
   */
  public $evaluatedBoundingboxCount;
  /**
   * The number of video frames used for model evaluation.
   *
   * @var int
   */
  public $evaluatedFrameCount;
  /**
   * The number of tracks used for model evaluation.
   *
   * @var int
   */
  public $evaluatedTrackCount;
  /**
   * Output only. The single metric for tracks accuracy evaluation: the
   * mean_average_precision averaged over all track_metrics_entries.
   *
   * @var float
   */
  public $trackMeanAveragePrecision;
  /**
   * Output only. The single metric for tracks bounding box iou evaluation: the
   * mean_bounding_box_iou averaged over all track_metrics_entries.
   *
   * @var float
   */
  public $trackMeanBoundingBoxIou;
  /**
   * Output only. The single metric for tracking consistency evaluation: the
   * mean_mismatch_rate averaged over all track_metrics_entries.
   *
   * @var float
   */
  public $trackMeanMismatchRate;
  protected $trackMetricsEntriesType = XPSTrackMetricsEntry::class;
  protected $trackMetricsEntriesDataType = 'array';

  /**
   * Output only. The single metric for bounding boxes evaluation: the
   * mean_average_precision averaged over all bounding_box_metrics_entries.
   *
   * @param float $boundingBoxMeanAveragePrecision
   */
  public function setBoundingBoxMeanAveragePrecision($boundingBoxMeanAveragePrecision)
  {
    $this->boundingBoxMeanAveragePrecision = $boundingBoxMeanAveragePrecision;
  }
  /**
   * @return float
   */
  public function getBoundingBoxMeanAveragePrecision()
  {
    return $this->boundingBoxMeanAveragePrecision;
  }
  /**
   * Output only. The bounding boxes match metrics for each Intersection-over-
   * union threshold 0.05,0.10,...,0.95,0.96,0.97,0.98,0.99.
   *
   * @param XPSBoundingBoxMetricsEntry[] $boundingBoxMetricsEntries
   */
  public function setBoundingBoxMetricsEntries($boundingBoxMetricsEntries)
  {
    $this->boundingBoxMetricsEntries = $boundingBoxMetricsEntries;
  }
  /**
   * @return XPSBoundingBoxMetricsEntry[]
   */
  public function getBoundingBoxMetricsEntries()
  {
    return $this->boundingBoxMetricsEntries;
  }
  /**
   * The number of bounding boxes used for model evaluation.
   *
   * @param int $evaluatedBoundingboxCount
   */
  public function setEvaluatedBoundingboxCount($evaluatedBoundingboxCount)
  {
    $this->evaluatedBoundingboxCount = $evaluatedBoundingboxCount;
  }
  /**
   * @return int
   */
  public function getEvaluatedBoundingboxCount()
  {
    return $this->evaluatedBoundingboxCount;
  }
  /**
   * The number of video frames used for model evaluation.
   *
   * @param int $evaluatedFrameCount
   */
  public function setEvaluatedFrameCount($evaluatedFrameCount)
  {
    $this->evaluatedFrameCount = $evaluatedFrameCount;
  }
  /**
   * @return int
   */
  public function getEvaluatedFrameCount()
  {
    return $this->evaluatedFrameCount;
  }
  /**
   * The number of tracks used for model evaluation.
   *
   * @param int $evaluatedTrackCount
   */
  public function setEvaluatedTrackCount($evaluatedTrackCount)
  {
    $this->evaluatedTrackCount = $evaluatedTrackCount;
  }
  /**
   * @return int
   */
  public function getEvaluatedTrackCount()
  {
    return $this->evaluatedTrackCount;
  }
  /**
   * Output only. The single metric for tracks accuracy evaluation: the
   * mean_average_precision averaged over all track_metrics_entries.
   *
   * @param float $trackMeanAveragePrecision
   */
  public function setTrackMeanAveragePrecision($trackMeanAveragePrecision)
  {
    $this->trackMeanAveragePrecision = $trackMeanAveragePrecision;
  }
  /**
   * @return float
   */
  public function getTrackMeanAveragePrecision()
  {
    return $this->trackMeanAveragePrecision;
  }
  /**
   * Output only. The single metric for tracks bounding box iou evaluation: the
   * mean_bounding_box_iou averaged over all track_metrics_entries.
   *
   * @param float $trackMeanBoundingBoxIou
   */
  public function setTrackMeanBoundingBoxIou($trackMeanBoundingBoxIou)
  {
    $this->trackMeanBoundingBoxIou = $trackMeanBoundingBoxIou;
  }
  /**
   * @return float
   */
  public function getTrackMeanBoundingBoxIou()
  {
    return $this->trackMeanBoundingBoxIou;
  }
  /**
   * Output only. The single metric for tracking consistency evaluation: the
   * mean_mismatch_rate averaged over all track_metrics_entries.
   *
   * @param float $trackMeanMismatchRate
   */
  public function setTrackMeanMismatchRate($trackMeanMismatchRate)
  {
    $this->trackMeanMismatchRate = $trackMeanMismatchRate;
  }
  /**
   * @return float
   */
  public function getTrackMeanMismatchRate()
  {
    return $this->trackMeanMismatchRate;
  }
  /**
   * Output only. The tracks match metrics for each Intersection-over-union
   * threshold 0.05,0.10,...,0.95,0.96,0.97,0.98,0.99.
   *
   * @param XPSTrackMetricsEntry[] $trackMetricsEntries
   */
  public function setTrackMetricsEntries($trackMetricsEntries)
  {
    $this->trackMetricsEntries = $trackMetricsEntries;
  }
  /**
   * @return XPSTrackMetricsEntry[]
   */
  public function getTrackMetricsEntries()
  {
    return $this->trackMetricsEntries;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSVideoObjectTrackingEvaluationMetrics::class, 'Google_Service_CloudNaturalLanguage_XPSVideoObjectTrackingEvaluationMetrics');
