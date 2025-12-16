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

namespace Google\Service\Datastore;

class ExplainMetrics extends \Google\Model
{
  protected $executionStatsType = ExecutionStats::class;
  protected $executionStatsDataType = '';
  protected $planSummaryType = PlanSummary::class;
  protected $planSummaryDataType = '';

  /**
   * Aggregated stats from the execution of the query. Only present when
   * ExplainOptions.analyze is set to true.
   *
   * @param ExecutionStats $executionStats
   */
  public function setExecutionStats(ExecutionStats $executionStats)
  {
    $this->executionStats = $executionStats;
  }
  /**
   * @return ExecutionStats
   */
  public function getExecutionStats()
  {
    return $this->executionStats;
  }
  /**
   * Planning phase information for the query.
   *
   * @param PlanSummary $planSummary
   */
  public function setPlanSummary(PlanSummary $planSummary)
  {
    $this->planSummary = $planSummary;
  }
  /**
   * @return PlanSummary
   */
  public function getPlanSummary()
  {
    return $this->planSummary;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExplainMetrics::class, 'Google_Service_Datastore_ExplainMetrics');
