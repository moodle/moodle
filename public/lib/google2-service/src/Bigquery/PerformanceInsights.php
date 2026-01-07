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

namespace Google\Service\Bigquery;

class PerformanceInsights extends \Google\Collection
{
  protected $collection_key = 'stagePerformanceStandaloneInsights';
  /**
   * Output only. Average execution ms of previous runs. Indicates the job ran
   * slow compared to previous executions. To find previous executions, use
   * INFORMATION_SCHEMA tables and filter jobs with same query hash.
   *
   * @var string
   */
  public $avgPreviousExecutionMs;
  protected $stagePerformanceChangeInsightsType = StagePerformanceChangeInsight::class;
  protected $stagePerformanceChangeInsightsDataType = 'array';
  protected $stagePerformanceStandaloneInsightsType = StagePerformanceStandaloneInsight::class;
  protected $stagePerformanceStandaloneInsightsDataType = 'array';

  /**
   * Output only. Average execution ms of previous runs. Indicates the job ran
   * slow compared to previous executions. To find previous executions, use
   * INFORMATION_SCHEMA tables and filter jobs with same query hash.
   *
   * @param string $avgPreviousExecutionMs
   */
  public function setAvgPreviousExecutionMs($avgPreviousExecutionMs)
  {
    $this->avgPreviousExecutionMs = $avgPreviousExecutionMs;
  }
  /**
   * @return string
   */
  public function getAvgPreviousExecutionMs()
  {
    return $this->avgPreviousExecutionMs;
  }
  /**
   * Output only. Query stage performance insights compared to previous runs,
   * for diagnosing performance regression.
   *
   * @param StagePerformanceChangeInsight[] $stagePerformanceChangeInsights
   */
  public function setStagePerformanceChangeInsights($stagePerformanceChangeInsights)
  {
    $this->stagePerformanceChangeInsights = $stagePerformanceChangeInsights;
  }
  /**
   * @return StagePerformanceChangeInsight[]
   */
  public function getStagePerformanceChangeInsights()
  {
    return $this->stagePerformanceChangeInsights;
  }
  /**
   * Output only. Standalone query stage performance insights, for exploring
   * potential improvements.
   *
   * @param StagePerformanceStandaloneInsight[] $stagePerformanceStandaloneInsights
   */
  public function setStagePerformanceStandaloneInsights($stagePerformanceStandaloneInsights)
  {
    $this->stagePerformanceStandaloneInsights = $stagePerformanceStandaloneInsights;
  }
  /**
   * @return StagePerformanceStandaloneInsight[]
   */
  public function getStagePerformanceStandaloneInsights()
  {
    return $this->stagePerformanceStandaloneInsights;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PerformanceInsights::class, 'Google_Service_Bigquery_PerformanceInsights');
