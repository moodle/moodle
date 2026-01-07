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

class XPSBoundingBoxMetricsEntry extends \Google\Collection
{
  protected $collection_key = 'confidenceMetricsEntries';
  protected $confidenceMetricsEntriesType = XPSBoundingBoxMetricsEntryConfidenceMetricsEntry::class;
  protected $confidenceMetricsEntriesDataType = 'array';
  /**
   * The intersection-over-union threshold value used to compute this metrics
   * entry.
   *
   * @var float
   */
  public $iouThreshold;
  /**
   * The mean average precision.
   *
   * @var float
   */
  public $meanAveragePrecision;

  /**
   * Metrics for each label-match confidence_threshold from
   * 0.05,0.10,...,0.95,0.96,0.97,0.98,0.99.
   *
   * @param XPSBoundingBoxMetricsEntryConfidenceMetricsEntry[] $confidenceMetricsEntries
   */
  public function setConfidenceMetricsEntries($confidenceMetricsEntries)
  {
    $this->confidenceMetricsEntries = $confidenceMetricsEntries;
  }
  /**
   * @return XPSBoundingBoxMetricsEntryConfidenceMetricsEntry[]
   */
  public function getConfidenceMetricsEntries()
  {
    return $this->confidenceMetricsEntries;
  }
  /**
   * The intersection-over-union threshold value used to compute this metrics
   * entry.
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
   * The mean average precision.
   *
   * @param float $meanAveragePrecision
   */
  public function setMeanAveragePrecision($meanAveragePrecision)
  {
    $this->meanAveragePrecision = $meanAveragePrecision;
  }
  /**
   * @return float
   */
  public function getMeanAveragePrecision()
  {
    return $this->meanAveragePrecision;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSBoundingBoxMetricsEntry::class, 'Google_Service_CloudNaturalLanguage_XPSBoundingBoxMetricsEntry');
