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

class QueryResponse extends \Google\Collection
{
  protected $collection_key = 'rows';
  /**
   * Whether the query result was fetched from the query cache.
   *
   * @var bool
   */
  public $cacheHit;
  /**
   * Output only. Creation time of this query, in milliseconds since the epoch.
   * This field will be present on all queries.
   *
   * @var string
   */
  public $creationTime;
  protected $dmlStatsType = DmlStatistics::class;
  protected $dmlStatsDataType = '';
  /**
   * Output only. End time of this query, in milliseconds since the epoch. This
   * field will be present whenever a query job is in the DONE state.
   *
   * @var string
   */
  public $endTime;
  protected $errorsType = ErrorProto::class;
  protected $errorsDataType = 'array';
  /**
   * Whether the query has completed or not. If rows or totalRows are present,
   * this will always be true. If this is false, totalRows will not be
   * available.
   *
   * @var bool
   */
  public $jobComplete;
  protected $jobCreationReasonType = JobCreationReason::class;
  protected $jobCreationReasonDataType = '';
  protected $jobReferenceType = JobReference::class;
  protected $jobReferenceDataType = '';
  /**
   * The resource type.
   *
   * @var string
   */
  public $kind;
  /**
   * Output only. The geographic location of the query. For more information
   * about BigQuery locations, see:
   * https://cloud.google.com/bigquery/docs/locations
   *
   * @var string
   */
  public $location;
  /**
   * Output only. The number of rows affected by a DML statement. Present only
   * for DML statements INSERT, UPDATE or DELETE.
   *
   * @var string
   */
  public $numDmlAffectedRows;
  /**
   * A token used for paging results. A non-empty token indicates that
   * additional results are available. To see additional results, query the [`jo
   * bs.getQueryResults`](https://cloud.google.com/bigquery/docs/reference/rest/
   * v2/jobs/getQueryResults) method. For more information, see [Paging through
   * table data](https://cloud.google.com/bigquery/docs/paging-results).
   *
   * @var string
   */
  public $pageToken;
  /**
   * Auto-generated ID for the query.
   *
   * @var string
   */
  public $queryId;
  protected $rowsType = TableRow::class;
  protected $rowsDataType = 'array';
  protected $schemaType = TableSchema::class;
  protected $schemaDataType = '';
  protected $sessionInfoType = SessionInfo::class;
  protected $sessionInfoDataType = '';
  /**
   * Output only. Start time of this query, in milliseconds since the epoch.
   * This field will be present when the query job transitions from the PENDING
   * state to either RUNNING or DONE.
   *
   * @var string
   */
  public $startTime;
  /**
   * Output only. If the project is configured to use on-demand pricing, then
   * this field contains the total bytes billed for the job. If the project is
   * configured to use flat-rate pricing, then you are not billed for bytes and
   * this field is informational only.
   *
   * @var string
   */
  public $totalBytesBilled;
  /**
   * The total number of bytes processed for this query. If this query was a dry
   * run, this is the number of bytes that would be processed if the query were
   * run.
   *
   * @var string
   */
  public $totalBytesProcessed;
  /**
   * The total number of rows in the complete query result set, which can be
   * more than the number of rows in this single page of results.
   *
   * @var string
   */
  public $totalRows;
  /**
   * Output only. Number of slot ms the user is actually billed for.
   *
   * @var string
   */
  public $totalSlotMs;

  /**
   * Whether the query result was fetched from the query cache.
   *
   * @param bool $cacheHit
   */
  public function setCacheHit($cacheHit)
  {
    $this->cacheHit = $cacheHit;
  }
  /**
   * @return bool
   */
  public function getCacheHit()
  {
    return $this->cacheHit;
  }
  /**
   * Output only. Creation time of this query, in milliseconds since the epoch.
   * This field will be present on all queries.
   *
   * @param string $creationTime
   */
  public function setCreationTime($creationTime)
  {
    $this->creationTime = $creationTime;
  }
  /**
   * @return string
   */
  public function getCreationTime()
  {
    return $this->creationTime;
  }
  /**
   * Output only. Detailed statistics for DML statements INSERT, UPDATE, DELETE,
   * MERGE or TRUNCATE.
   *
   * @param DmlStatistics $dmlStats
   */
  public function setDmlStats(DmlStatistics $dmlStats)
  {
    $this->dmlStats = $dmlStats;
  }
  /**
   * @return DmlStatistics
   */
  public function getDmlStats()
  {
    return $this->dmlStats;
  }
  /**
   * Output only. End time of this query, in milliseconds since the epoch. This
   * field will be present whenever a query job is in the DONE state.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Output only. The first errors or warnings encountered during the running of
   * the job. The final message includes the number of errors that caused the
   * process to stop. Errors here do not necessarily mean that the job has
   * completed or was unsuccessful. For more information about error messages,
   * see [Error messages](https://cloud.google.com/bigquery/docs/error-
   * messages).
   *
   * @param ErrorProto[] $errors
   */
  public function setErrors($errors)
  {
    $this->errors = $errors;
  }
  /**
   * @return ErrorProto[]
   */
  public function getErrors()
  {
    return $this->errors;
  }
  /**
   * Whether the query has completed or not. If rows or totalRows are present,
   * this will always be true. If this is false, totalRows will not be
   * available.
   *
   * @param bool $jobComplete
   */
  public function setJobComplete($jobComplete)
  {
    $this->jobComplete = $jobComplete;
  }
  /**
   * @return bool
   */
  public function getJobComplete()
  {
    return $this->jobComplete;
  }
  /**
   * Optional. The reason why a Job was created. Only relevant when a
   * job_reference is present in the response. If job_reference is not present
   * it will always be unset.
   *
   * @param JobCreationReason $jobCreationReason
   */
  public function setJobCreationReason(JobCreationReason $jobCreationReason)
  {
    $this->jobCreationReason = $jobCreationReason;
  }
  /**
   * @return JobCreationReason
   */
  public function getJobCreationReason()
  {
    return $this->jobCreationReason;
  }
  /**
   * Reference to the Job that was created to run the query. This field will be
   * present even if the original request timed out, in which case
   * GetQueryResults can be used to read the results once the query has
   * completed. Since this API only returns the first page of results,
   * subsequent pages can be fetched via the same mechanism (GetQueryResults).
   * If job_creation_mode was set to `JOB_CREATION_OPTIONAL` and the query
   * completes without creating a job, this field will be empty.
   *
   * @param JobReference $jobReference
   */
  public function setJobReference(JobReference $jobReference)
  {
    $this->jobReference = $jobReference;
  }
  /**
   * @return JobReference
   */
  public function getJobReference()
  {
    return $this->jobReference;
  }
  /**
   * The resource type.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Output only. The geographic location of the query. For more information
   * about BigQuery locations, see:
   * https://cloud.google.com/bigquery/docs/locations
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * Output only. The number of rows affected by a DML statement. Present only
   * for DML statements INSERT, UPDATE or DELETE.
   *
   * @param string $numDmlAffectedRows
   */
  public function setNumDmlAffectedRows($numDmlAffectedRows)
  {
    $this->numDmlAffectedRows = $numDmlAffectedRows;
  }
  /**
   * @return string
   */
  public function getNumDmlAffectedRows()
  {
    return $this->numDmlAffectedRows;
  }
  /**
   * A token used for paging results. A non-empty token indicates that
   * additional results are available. To see additional results, query the [`jo
   * bs.getQueryResults`](https://cloud.google.com/bigquery/docs/reference/rest/
   * v2/jobs/getQueryResults) method. For more information, see [Paging through
   * table data](https://cloud.google.com/bigquery/docs/paging-results).
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
   * Auto-generated ID for the query.
   *
   * @param string $queryId
   */
  public function setQueryId($queryId)
  {
    $this->queryId = $queryId;
  }
  /**
   * @return string
   */
  public function getQueryId()
  {
    return $this->queryId;
  }
  /**
   * An object with as many results as can be contained within the maximum
   * permitted reply size. To get any additional rows, you can call
   * GetQueryResults and specify the jobReference returned above.
   *
   * @param TableRow[] $rows
   */
  public function setRows($rows)
  {
    $this->rows = $rows;
  }
  /**
   * @return TableRow[]
   */
  public function getRows()
  {
    return $this->rows;
  }
  /**
   * The schema of the results. Present only when the query completes
   * successfully.
   *
   * @param TableSchema $schema
   */
  public function setSchema(TableSchema $schema)
  {
    $this->schema = $schema;
  }
  /**
   * @return TableSchema
   */
  public function getSchema()
  {
    return $this->schema;
  }
  /**
   * Output only. Information of the session if this job is part of one.
   *
   * @param SessionInfo $sessionInfo
   */
  public function setSessionInfo(SessionInfo $sessionInfo)
  {
    $this->sessionInfo = $sessionInfo;
  }
  /**
   * @return SessionInfo
   */
  public function getSessionInfo()
  {
    return $this->sessionInfo;
  }
  /**
   * Output only. Start time of this query, in milliseconds since the epoch.
   * This field will be present when the query job transitions from the PENDING
   * state to either RUNNING or DONE.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * Output only. If the project is configured to use on-demand pricing, then
   * this field contains the total bytes billed for the job. If the project is
   * configured to use flat-rate pricing, then you are not billed for bytes and
   * this field is informational only.
   *
   * @param string $totalBytesBilled
   */
  public function setTotalBytesBilled($totalBytesBilled)
  {
    $this->totalBytesBilled = $totalBytesBilled;
  }
  /**
   * @return string
   */
  public function getTotalBytesBilled()
  {
    return $this->totalBytesBilled;
  }
  /**
   * The total number of bytes processed for this query. If this query was a dry
   * run, this is the number of bytes that would be processed if the query were
   * run.
   *
   * @param string $totalBytesProcessed
   */
  public function setTotalBytesProcessed($totalBytesProcessed)
  {
    $this->totalBytesProcessed = $totalBytesProcessed;
  }
  /**
   * @return string
   */
  public function getTotalBytesProcessed()
  {
    return $this->totalBytesProcessed;
  }
  /**
   * The total number of rows in the complete query result set, which can be
   * more than the number of rows in this single page of results.
   *
   * @param string $totalRows
   */
  public function setTotalRows($totalRows)
  {
    $this->totalRows = $totalRows;
  }
  /**
   * @return string
   */
  public function getTotalRows()
  {
    return $this->totalRows;
  }
  /**
   * Output only. Number of slot ms the user is actually billed for.
   *
   * @param string $totalSlotMs
   */
  public function setTotalSlotMs($totalSlotMs)
  {
    $this->totalSlotMs = $totalSlotMs;
  }
  /**
   * @return string
   */
  public function getTotalSlotMs()
  {
    return $this->totalSlotMs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(QueryResponse::class, 'Google_Service_Bigquery_QueryResponse');
