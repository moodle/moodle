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

namespace Google\Service\Monitoring;

class SqlCondition extends \Google\Model
{
  protected $booleanTestType = BooleanTest::class;
  protected $booleanTestDataType = '';
  protected $dailyType = Daily::class;
  protected $dailyDataType = '';
  protected $hourlyType = Hourly::class;
  protected $hourlyDataType = '';
  protected $minutesType = Minutes::class;
  protected $minutesDataType = '';
  /**
   * Required. The Log Analytics SQL query to run, as a string. The query must
   * conform to the required shape. Specifically, the query must not try to
   * filter the input by time. A filter will automatically be applied to filter
   * the input so that the query receives all rows received since the last time
   * the query was run.For example, the following query extracts all log entries
   * containing an HTTP request: SELECT timestamp, log_name, severity,
   * http_request, resource, labels FROM my-project.global._Default._AllLogs
   * WHERE http_request IS NOT NULL
   *
   * @var string
   */
  public $query;
  protected $rowCountTestType = RowCountTest::class;
  protected $rowCountTestDataType = '';

  /**
   * Test the boolean value in the indicated column.
   *
   * @param BooleanTest $booleanTest
   */
  public function setBooleanTest(BooleanTest $booleanTest)
  {
    $this->booleanTest = $booleanTest;
  }
  /**
   * @return BooleanTest
   */
  public function getBooleanTest()
  {
    return $this->booleanTest;
  }
  /**
   * Schedule the query to execute every so many days.
   *
   * @param Daily $daily
   */
  public function setDaily(Daily $daily)
  {
    $this->daily = $daily;
  }
  /**
   * @return Daily
   */
  public function getDaily()
  {
    return $this->daily;
  }
  /**
   * Schedule the query to execute every so many hours.
   *
   * @param Hourly $hourly
   */
  public function setHourly(Hourly $hourly)
  {
    $this->hourly = $hourly;
  }
  /**
   * @return Hourly
   */
  public function getHourly()
  {
    return $this->hourly;
  }
  /**
   * Schedule the query to execute every so many minutes.
   *
   * @param Minutes $minutes
   */
  public function setMinutes(Minutes $minutes)
  {
    $this->minutes = $minutes;
  }
  /**
   * @return Minutes
   */
  public function getMinutes()
  {
    return $this->minutes;
  }
  /**
   * Required. The Log Analytics SQL query to run, as a string. The query must
   * conform to the required shape. Specifically, the query must not try to
   * filter the input by time. A filter will automatically be applied to filter
   * the input so that the query receives all rows received since the last time
   * the query was run.For example, the following query extracts all log entries
   * containing an HTTP request: SELECT timestamp, log_name, severity,
   * http_request, resource, labels FROM my-project.global._Default._AllLogs
   * WHERE http_request IS NOT NULL
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
   * Test the row count against a threshold.
   *
   * @param RowCountTest $rowCountTest
   */
  public function setRowCountTest(RowCountTest $rowCountTest)
  {
    $this->rowCountTest = $rowCountTest;
  }
  /**
   * @return RowCountTest
   */
  public function getRowCountTest()
  {
    return $this->rowCountTest;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SqlCondition::class, 'Google_Service_Monitoring_SqlCondition');
