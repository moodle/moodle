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

class MaterializedViewDefinition extends \Google\Model
{
  /**
   * Optional. This option declares the intention to construct a materialized
   * view that isn't refreshed incrementally. Non-incremental materialized views
   * support an expanded range of SQL queries. The
   * `allow_non_incremental_definition` option can't be changed after the
   * materialized view is created.
   *
   * @var bool
   */
  public $allowNonIncrementalDefinition;
  /**
   * Optional. Enable automatic refresh of the materialized view when the base
   * table is updated. The default value is "true".
   *
   * @var bool
   */
  public $enableRefresh;
  /**
   * Output only. The time when this materialized view was last refreshed, in
   * milliseconds since the epoch.
   *
   * @var string
   */
  public $lastRefreshTime;
  /**
   * [Optional] Max staleness of data that could be returned when materizlized
   * view is queried (formatted as Google SQL Interval type).
   *
   * @var string
   */
  public $maxStaleness;
  /**
   * Required. A query whose results are persisted.
   *
   * @var string
   */
  public $query;
  /**
   * Optional. The maximum frequency at which this materialized view will be
   * refreshed. The default value is "1800000" (30 minutes).
   *
   * @var string
   */
  public $refreshIntervalMs;

  /**
   * Optional. This option declares the intention to construct a materialized
   * view that isn't refreshed incrementally. Non-incremental materialized views
   * support an expanded range of SQL queries. The
   * `allow_non_incremental_definition` option can't be changed after the
   * materialized view is created.
   *
   * @param bool $allowNonIncrementalDefinition
   */
  public function setAllowNonIncrementalDefinition($allowNonIncrementalDefinition)
  {
    $this->allowNonIncrementalDefinition = $allowNonIncrementalDefinition;
  }
  /**
   * @return bool
   */
  public function getAllowNonIncrementalDefinition()
  {
    return $this->allowNonIncrementalDefinition;
  }
  /**
   * Optional. Enable automatic refresh of the materialized view when the base
   * table is updated. The default value is "true".
   *
   * @param bool $enableRefresh
   */
  public function setEnableRefresh($enableRefresh)
  {
    $this->enableRefresh = $enableRefresh;
  }
  /**
   * @return bool
   */
  public function getEnableRefresh()
  {
    return $this->enableRefresh;
  }
  /**
   * Output only. The time when this materialized view was last refreshed, in
   * milliseconds since the epoch.
   *
   * @param string $lastRefreshTime
   */
  public function setLastRefreshTime($lastRefreshTime)
  {
    $this->lastRefreshTime = $lastRefreshTime;
  }
  /**
   * @return string
   */
  public function getLastRefreshTime()
  {
    return $this->lastRefreshTime;
  }
  /**
   * [Optional] Max staleness of data that could be returned when materizlized
   * view is queried (formatted as Google SQL Interval type).
   *
   * @param string $maxStaleness
   */
  public function setMaxStaleness($maxStaleness)
  {
    $this->maxStaleness = $maxStaleness;
  }
  /**
   * @return string
   */
  public function getMaxStaleness()
  {
    return $this->maxStaleness;
  }
  /**
   * Required. A query whose results are persisted.
   *
   * @param string $query
   */
  public function setQuery($query)
  {
    $this->query = $query;
  }
  /**
   * @return string
   */
  public function getQuery()
  {
    return $this->query;
  }
  /**
   * Optional. The maximum frequency at which this materialized view will be
   * refreshed. The default value is "1800000" (30 minutes).
   *
   * @param string $refreshIntervalMs
   */
  public function setRefreshIntervalMs($refreshIntervalMs)
  {
    $this->refreshIntervalMs = $refreshIntervalMs;
  }
  /**
   * @return string
   */
  public function getRefreshIntervalMs()
  {
    return $this->refreshIntervalMs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MaterializedViewDefinition::class, 'Google_Service_Bigquery_MaterializedViewDefinition');
