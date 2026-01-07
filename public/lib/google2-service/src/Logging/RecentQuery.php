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

namespace Google\Service\Logging;

class RecentQuery extends \Google\Model
{
  /**
   * Output only. The timestamp when this query was last run.
   *
   * @var string
   */
  public $lastRunTime;
  protected $loggingQueryType = LoggingQuery::class;
  protected $loggingQueryDataType = '';
  /**
   * Output only. Resource name of the recent query.In the format:
   * "projects/[PROJECT_ID]/locations/[LOCATION_ID]/recentQueries/[QUERY_ID]"
   * For a list of supported locations, see Supported Regions
   * (https://cloud.google.com/logging/docs/region-support)The QUERY_ID is a
   * system generated alphanumeric ID.
   *
   * @var string
   */
  public $name;
  protected $opsAnalyticsQueryType = OpsAnalyticsQuery::class;
  protected $opsAnalyticsQueryDataType = '';

  /**
   * Output only. The timestamp when this query was last run.
   *
   * @param string $lastRunTime
   */
  public function setLastRunTime($lastRunTime)
  {
    $this->lastRunTime = $lastRunTime;
  }
  /**
   * @return string
   */
  public function getLastRunTime()
  {
    return $this->lastRunTime;
  }
  /**
   * Logging query that can be executed in Logs Explorer or via Logging API.
   *
   * @param LoggingQuery $loggingQuery
   */
  public function setLoggingQuery(LoggingQuery $loggingQuery)
  {
    $this->loggingQuery = $loggingQuery;
  }
  /**
   * @return LoggingQuery
   */
  public function getLoggingQuery()
  {
    return $this->loggingQuery;
  }
  /**
   * Output only. Resource name of the recent query.In the format:
   * "projects/[PROJECT_ID]/locations/[LOCATION_ID]/recentQueries/[QUERY_ID]"
   * For a list of supported locations, see Supported Regions
   * (https://cloud.google.com/logging/docs/region-support)The QUERY_ID is a
   * system generated alphanumeric ID.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Analytics query that can be executed in Log Analytics.
   *
   * @param OpsAnalyticsQuery $opsAnalyticsQuery
   */
  public function setOpsAnalyticsQuery(OpsAnalyticsQuery $opsAnalyticsQuery)
  {
    $this->opsAnalyticsQuery = $opsAnalyticsQuery;
  }
  /**
   * @return OpsAnalyticsQuery
   */
  public function getOpsAnalyticsQuery()
  {
    return $this->opsAnalyticsQuery;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RecentQuery::class, 'Google_Service_Logging_RecentQuery');
