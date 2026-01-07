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

class GetQueryResultsResponse extends \Google\Collection
{
  protected $collection_key = 'rows';
  /**
   * Whether the query result was fetched from the query cache.
   *
   * @var bool
   */
  public $cacheHit;
  protected $errorsType = ErrorProto::class;
  protected $errorsDataType = 'array';
  /**
   * A hash of this response.
   *
   * @var string
   */
  public $etag;
  /**
   * Whether the query has completed or not. If rows or totalRows are present,
   * this will always be true. If this is false, totalRows will not be
   * available.
   *
   * @var bool
   */
  public $jobComplete;
  protected $jobReferenceType = JobReference::class;
  protected $jobReferenceDataType = '';
  /**
   * The resource type of the response.
   *
   * @var string
   */
  public $kind;
  /**
   * Output only. The number of rows affected by a DML statement. Present only
   * for DML statements INSERT, UPDATE or DELETE.
   *
   * @var string
   */
  public $numDmlAffectedRows;
  /**
   * A token used for paging results. When this token is non-empty, it indicates
   * additional results are available.
   *
   * @var string
   */
  public $pageToken;
  protected $rowsType = TableRow::class;
  protected $rowsDataType = 'array';
  protected $schemaType = TableSchema::class;
  protected $schemaDataType = '';
  /**
   * The total number of bytes processed for this query.
   *
   * @var string
   */
  public $totalBytesProcessed;
  /**
   * The total number of rows in the complete query result set, which can be
   * more than the number of rows in this single page of results. Present only
   * when the query completes successfully.
   *
   * @var string
   */
  public $totalRows;

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
   * A hash of this response.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
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
   * Reference to the BigQuery Job that was created to run the query. This field
   * will be present even if the original request timed out, in which case
   * GetQueryResults can be used to read the results once the query has
   * completed. Since this API only returns the first page of results,
   * subsequent pages can be fetched via the same mechanism (GetQueryResults).
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
   * The resource type of the response.
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
   * A token used for paging results. When this token is non-empty, it indicates
   * additional results are available.
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
   * An object with as many results as can be contained within the maximum
   * permitted reply size. To get any additional rows, you can call
   * GetQueryResults and specify the jobReference returned above. Present only
   * when the query completes successfully. The REST-based representation of
   * this data leverages a series of JSON f,v objects for indicating fields and
   * values.
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
   * The total number of bytes processed for this query.
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
   * more than the number of rows in this single page of results. Present only
   * when the query completes successfully.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GetQueryResultsResponse::class, 'Google_Service_Bigquery_GetQueryResultsResponse');
