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

namespace Google\Service\CloudAsset;

class QueryAssetsRequest extends \Google\Model
{
  /**
   * Optional. Reference to the query job, which is from the
   * `QueryAssetsResponse` of previous `QueryAssets` call.
   *
   * @var string
   */
  public $jobReference;
  protected $outputConfigType = QueryAssetsOutputConfig::class;
  protected $outputConfigDataType = '';
  /**
   * Optional. The maximum number of rows to return in the results. Responses
   * are limited to 10 MB and 1000 rows. By default, the maximum row count is
   * 1000. When the byte or row count limit is reached, the rest of the query
   * results will be paginated. The field will be ignored when [output_config]
   * is specified.
   *
   * @var int
   */
  public $pageSize;
  /**
   * Optional. A page token received from previous `QueryAssets`. The field will
   * be ignored when [output_config] is specified.
   *
   * @var string
   */
  public $pageToken;
  /**
   * Optional. Queries cloud assets as they appeared at the specified point in
   * time.
   *
   * @var string
   */
  public $readTime;
  protected $readTimeWindowType = TimeWindow::class;
  protected $readTimeWindowDataType = '';
  /**
   * Optional. A SQL statement that's compatible with [BigQuery
   * SQL](https://cloud.google.com/bigquery/docs/introduction-sql).
   *
   * @var string
   */
  public $statement;
  /**
   * Optional. Specifies the maximum amount of time that the client is willing
   * to wait for the query to complete. By default, this limit is 5 min for the
   * first query, and 1 minute for the following queries. If the query is
   * complete, the `done` field in the `QueryAssetsResponse` is true, otherwise
   * false. Like BigQuery [jobs.query API](https://cloud.google.com/bigquery/doc
   * s/reference/rest/v2/jobs/query#queryrequest) The call is not guaranteed to
   * wait for the specified timeout; it typically returns after around 200
   * seconds (200,000 milliseconds), even if the query is not complete. The
   * field will be ignored when [output_config] is specified.
   *
   * @var string
   */
  public $timeout;

  /**
   * Optional. Reference to the query job, which is from the
   * `QueryAssetsResponse` of previous `QueryAssets` call.
   *
   * @param string $jobReference
   */
  public function setJobReference($jobReference)
  {
    $this->jobReference = $jobReference;
  }
  /**
   * @return string
   */
  public function getJobReference()
  {
    return $this->jobReference;
  }
  /**
   * Optional. Destination where the query results will be saved. When this
   * field is specified, the query results won't be saved in the
   * [QueryAssetsResponse.query_result]. Instead
   * [QueryAssetsResponse.output_config] will be set. Meanwhile,
   * [QueryAssetsResponse.job_reference] will be set and can be used to check
   * the status of the query job when passed to a following [QueryAssets] API
   * call.
   *
   * @param QueryAssetsOutputConfig $outputConfig
   */
  public function setOutputConfig(QueryAssetsOutputConfig $outputConfig)
  {
    $this->outputConfig = $outputConfig;
  }
  /**
   * @return QueryAssetsOutputConfig
   */
  public function getOutputConfig()
  {
    return $this->outputConfig;
  }
  /**
   * Optional. The maximum number of rows to return in the results. Responses
   * are limited to 10 MB and 1000 rows. By default, the maximum row count is
   * 1000. When the byte or row count limit is reached, the rest of the query
   * results will be paginated. The field will be ignored when [output_config]
   * is specified.
   *
   * @param int $pageSize
   */
  public function setPageSize($pageSize)
  {
    $this->pageSize = $pageSize;
  }
  /**
   * @return int
   */
  public function getPageSize()
  {
    return $this->pageSize;
  }
  /**
   * Optional. A page token received from previous `QueryAssets`. The field will
   * be ignored when [output_config] is specified.
   *
   * @param string $pageToken
   */
  public function setPageToken($pageToken)
  {
    $this->pageToken = $pageToken;
  }
  /**
   * @return string
   */
  public function getPageToken()
  {
    return $this->pageToken;
  }
  /**
   * Optional. Queries cloud assets as they appeared at the specified point in
   * time.
   *
   * @param string $readTime
   */
  public function setReadTime($readTime)
  {
    $this->readTime = $readTime;
  }
  /**
   * @return string
   */
  public function getReadTime()
  {
    return $this->readTime;
  }
  /**
   * Optional. [start_time] is required. [start_time] must be less than
   * [end_time] Defaults [end_time] to now if [start_time] is set and [end_time]
   * isn't. Maximum permitted time range is 7 days.
   *
   * @param TimeWindow $readTimeWindow
   */
  public function setReadTimeWindow(TimeWindow $readTimeWindow)
  {
    $this->readTimeWindow = $readTimeWindow;
  }
  /**
   * @return TimeWindow
   */
  public function getReadTimeWindow()
  {
    return $this->readTimeWindow;
  }
  /**
   * Optional. A SQL statement that's compatible with [BigQuery
   * SQL](https://cloud.google.com/bigquery/docs/introduction-sql).
   *
   * @param string $statement
   */
  public function setStatement($statement)
  {
    $this->statement = $statement;
  }
  /**
   * @return string
   */
  public function getStatement()
  {
    return $this->statement;
  }
  /**
   * Optional. Specifies the maximum amount of time that the client is willing
   * to wait for the query to complete. By default, this limit is 5 min for the
   * first query, and 1 minute for the following queries. If the query is
   * complete, the `done` field in the `QueryAssetsResponse` is true, otherwise
   * false. Like BigQuery [jobs.query API](https://cloud.google.com/bigquery/doc
   * s/reference/rest/v2/jobs/query#queryrequest) The call is not guaranteed to
   * wait for the specified timeout; it typically returns after around 200
   * seconds (200,000 milliseconds), even if the query is not complete. The
   * field will be ignored when [output_config] is specified.
   *
   * @param string $timeout
   */
  public function setTimeout($timeout)
  {
    $this->timeout = $timeout;
  }
  /**
   * @return string
   */
  public function getTimeout()
  {
    return $this->timeout;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(QueryAssetsRequest::class, 'Google_Service_CloudAsset_QueryAssetsRequest');
