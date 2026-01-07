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

class QueryAssetsResponse extends \Google\Model
{
  /**
   * The query response, which can be either an `error` or a valid `response`.
   * If `done` == `false` and the query result is being saved in an output, the
   * output_config field will be set. If `done` == `true`, exactly one of
   * `error`, `query_result` or `output_config` will be set. [done] is unset
   * unless the [QueryAssetsResponse] contains a
   * [QueryAssetsResponse.job_reference].
   *
   * @var bool
   */
  public $done;
  protected $errorType = Status::class;
  protected $errorDataType = '';
  /**
   * Reference to a query job.
   *
   * @var string
   */
  public $jobReference;
  protected $outputConfigType = QueryAssetsOutputConfig::class;
  protected $outputConfigDataType = '';
  protected $queryResultType = QueryResult::class;
  protected $queryResultDataType = '';

  /**
   * The query response, which can be either an `error` or a valid `response`.
   * If `done` == `false` and the query result is being saved in an output, the
   * output_config field will be set. If `done` == `true`, exactly one of
   * `error`, `query_result` or `output_config` will be set. [done] is unset
   * unless the [QueryAssetsResponse] contains a
   * [QueryAssetsResponse.job_reference].
   *
   * @param bool $done
   */
  public function setDone($done)
  {
    $this->done = $done;
  }
  /**
   * @return bool
   */
  public function getDone()
  {
    return $this->done;
  }
  /**
   * Error status.
   *
   * @param Status $error
   */
  public function setError(Status $error)
  {
    $this->error = $error;
  }
  /**
   * @return Status
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * Reference to a query job.
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
   * Output configuration, which indicates that instead of being returned in an
   * API response on the fly, the query result will be saved in a specific
   * output.
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
   * Result of the query.
   *
   * @param QueryResult $queryResult
   */
  public function setQueryResult(QueryResult $queryResult)
  {
    $this->queryResult = $queryResult;
  }
  /**
   * @return QueryResult
   */
  public function getQueryResult()
  {
    return $this->queryResult;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(QueryAssetsResponse::class, 'Google_Service_CloudAsset_QueryAssetsResponse');
