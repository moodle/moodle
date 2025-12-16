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

namespace Google\Service\Document;

class GoogleCloudDocumentaiV1EvaluationConfidenceLevelMetrics extends \Google\Model
{
  /**
   * The confidence level.
   *
   * @var float
   */
  public $confidenceLevel;
  protected $metricsType = GoogleCloudDocumentaiV1EvaluationMetrics::class;
  protected $metricsDataType = '';

  /**
   * The confidence level.
   *
   * @param float $confidenceLevel
   */
  public function setConfidenceLevel($confidenceLevel)
  {
    $this->confidenceLevel = $confidenceLevel;
  }
  /**
   * @return float
   */
  public function getConfidenceLevel()
  {
    return $this->confidenceLevel;
  }
  /**
   * The metrics at the specific confidence level.
   *
   * @param GoogleCloudDocumentaiV1EvaluationMetrics $metrics
   */
  public function setMetrics(GoogleCloudDocumentaiV1EvaluationMetrics $metrics)
  {
    $this->metrics = $metrics;
  }
  /**
   * @return GoogleCloudDocumentaiV1EvaluationMetrics
   */
  public function getMetrics()
  {
    return $this->metrics;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1EvaluationConfidenceLevelMetrics::class, 'Google_Service_Document_GoogleCloudDocumentaiV1EvaluationConfidenceLevelMetrics');
