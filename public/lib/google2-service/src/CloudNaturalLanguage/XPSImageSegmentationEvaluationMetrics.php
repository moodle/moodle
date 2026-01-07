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

class XPSImageSegmentationEvaluationMetrics extends \Google\Collection
{
  protected $collection_key = 'confidenceMetricsEntries';
  protected $confidenceMetricsEntriesType = XPSImageSegmentationEvaluationMetricsConfidenceMetricsEntry::class;
  protected $confidenceMetricsEntriesDataType = 'array';

  /**
   * Metrics that have confidence thresholds. Precision-recall curve can be
   * derived from it.
   *
   * @param XPSImageSegmentationEvaluationMetricsConfidenceMetricsEntry[] $confidenceMetricsEntries
   */
  public function setConfidenceMetricsEntries($confidenceMetricsEntries)
  {
    $this->confidenceMetricsEntries = $confidenceMetricsEntries;
  }
  /**
   * @return XPSImageSegmentationEvaluationMetricsConfidenceMetricsEntry[]
   */
  public function getConfidenceMetricsEntries()
  {
    return $this->confidenceMetricsEntries;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSImageSegmentationEvaluationMetrics::class, 'Google_Service_CloudNaturalLanguage_XPSImageSegmentationEvaluationMetrics');
