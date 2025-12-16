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

class XPSVideoActionRecognitionEvaluationMetrics extends \Google\Collection
{
  protected $collection_key = 'videoActionMetricsEntries';
  /**
   * Output only. The number of ground truth actions used to create this
   * evaluation.
   *
   * @var int
   */
  public $evaluatedActionCount;
  protected $videoActionMetricsEntriesType = XPSVideoActionMetricsEntry::class;
  protected $videoActionMetricsEntriesDataType = 'array';

  /**
   * Output only. The number of ground truth actions used to create this
   * evaluation.
   *
   * @param int $evaluatedActionCount
   */
  public function setEvaluatedActionCount($evaluatedActionCount)
  {
    $this->evaluatedActionCount = $evaluatedActionCount;
  }
  /**
   * @return int
   */
  public function getEvaluatedActionCount()
  {
    return $this->evaluatedActionCount;
  }
  /**
   * Output only. The metric entries for precision window lengths: 1s,2s,3s,4s,
   * 5s.
   *
   * @param XPSVideoActionMetricsEntry[] $videoActionMetricsEntries
   */
  public function setVideoActionMetricsEntries($videoActionMetricsEntries)
  {
    $this->videoActionMetricsEntries = $videoActionMetricsEntries;
  }
  /**
   * @return XPSVideoActionMetricsEntry[]
   */
  public function getVideoActionMetricsEntries()
  {
    return $this->videoActionMetricsEntries;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSVideoActionRecognitionEvaluationMetrics::class, 'Google_Service_CloudNaturalLanguage_XPSVideoActionRecognitionEvaluationMetrics');
