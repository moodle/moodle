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

class XPSImageObjectDetectionEvaluationMetrics extends \Google\Collection
{
  protected $collection_key = 'boundingBoxMetricsEntries';
  /**
   * The single metric for bounding boxes evaluation: the mean_average_precision
   * averaged over all bounding_box_metrics_entries.
   *
   * @var float
   */
  public $boundingBoxMeanAveragePrecision;
  protected $boundingBoxMetricsEntriesType = XPSBoundingBoxMetricsEntry::class;
  protected $boundingBoxMetricsEntriesDataType = 'array';
  /**
   * The total number of bounding boxes (i.e. summed over all images) the ground
   * truth used to create this evaluation had.
   *
   * @var int
   */
  public $evaluatedBoundingBoxCount;

  /**
   * The single metric for bounding boxes evaluation: the mean_average_precision
   * averaged over all bounding_box_metrics_entries.
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
   * The bounding boxes match metrics for each Intersection-over-union threshold
   * 0.05,0.10,...,0.95,0.96,0.97,0.98,0.99 and each label confidence threshold
   * 0.05,0.10,...,0.95,0.96,0.97,0.98,0.99 pair.
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
   * The total number of bounding boxes (i.e. summed over all images) the ground
   * truth used to create this evaluation had.
   *
   * @param int $evaluatedBoundingBoxCount
   */
  public function setEvaluatedBoundingBoxCount($evaluatedBoundingBoxCount)
  {
    $this->evaluatedBoundingBoxCount = $evaluatedBoundingBoxCount;
  }
  /**
   * @return int
   */
  public function getEvaluatedBoundingBoxCount()
  {
    return $this->evaluatedBoundingBoxCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSImageObjectDetectionEvaluationMetrics::class, 'Google_Service_CloudNaturalLanguage_XPSImageObjectDetectionEvaluationMetrics');
