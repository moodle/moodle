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

namespace Google\Service\Spanner;

class ResultSetStats extends \Google\Model
{
  protected $queryPlanType = QueryPlan::class;
  protected $queryPlanDataType = '';
  /**
   * Aggregated statistics from the execution of the query. Only present when
   * the query is profiled. For example, a query could return the statistics as
   * follows: { "rows_returned": "3", "elapsed_time": "1.22 secs", "cpu_time":
   * "1.19 secs" }
   *
   * @var array[]
   */
  public $queryStats;
  /**
   * Standard DML returns an exact count of rows that were modified.
   *
   * @var string
   */
  public $rowCountExact;
  /**
   * Partitioned DML doesn't offer exactly-once semantics, so it returns a lower
   * bound of the rows modified.
   *
   * @var string
   */
  public $rowCountLowerBound;

  /**
   * QueryPlan for the query associated with this result.
   *
   * @param QueryPlan $queryPlan
   */
  public function setQueryPlan(QueryPlan $queryPlan)
  {
    $this->queryPlan = $queryPlan;
  }
  /**
   * @return QueryPlan
   */
  public function getQueryPlan()
  {
    return $this->queryPlan;
  }
  /**
   * Aggregated statistics from the execution of the query. Only present when
   * the query is profiled. For example, a query could return the statistics as
   * follows: { "rows_returned": "3", "elapsed_time": "1.22 secs", "cpu_time":
   * "1.19 secs" }
   *
   * @param array[] $queryStats
   */
  public function setQueryStats($queryStats)
  {
    $this->queryStats = $queryStats;
  }
  /**
   * @return array[]
   */
  public function getQueryStats()
  {
    return $this->queryStats;
  }
  /**
   * Standard DML returns an exact count of rows that were modified.
   *
   * @param string $rowCountExact
   */
  public function setRowCountExact($rowCountExact)
  {
    $this->rowCountExact = $rowCountExact;
  }
  /**
   * @return string
   */
  public function getRowCountExact()
  {
    return $this->rowCountExact;
  }
  /**
   * Partitioned DML doesn't offer exactly-once semantics, so it returns a lower
   * bound of the rows modified.
   *
   * @param string $rowCountLowerBound
   */
  public function setRowCountLowerBound($rowCountLowerBound)
  {
    $this->rowCountLowerBound = $rowCountLowerBound;
  }
  /**
   * @return string
   */
  public function getRowCountLowerBound()
  {
    return $this->rowCountLowerBound;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResultSetStats::class, 'Google_Service_Spanner_ResultSetStats');
