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

namespace Google\Service\YouTubeAnalytics;

class QueryResponse extends \Google\Collection
{
  protected $collection_key = 'rows';
  protected $columnHeadersType = ResultTableColumnHeader::class;
  protected $columnHeadersDataType = 'array';
  protected $errorsType = Errors::class;
  protected $errorsDataType = '';
  /**
   * This value specifies the type of data included in the API response. For the
   * query method, the kind property value will be
   * `youtubeAnalytics#resultTable`.
   *
   * @var string
   */
  public $kind;
  /**
   * The list contains all rows of the result table. Each item in the list is an
   * array that contains comma-delimited data corresponding to a single row of
   * data. The order of the comma-delimited data fields will match the order of
   * the columns listed in the `columnHeaders` field. If no data is available
   * for the given query, the `rows` element will be omitted from the response.
   * The response for a query with the `day` dimension will not contain rows for
   * the most recent days.
   *
   * @var array[]
   */
  public $rows;

  /**
   * This value specifies information about the data returned in the `rows`
   * fields. Each item in the `columnHeaders` list identifies a field returned
   * in the `rows` value, which contains a list of comma-delimited data. The
   * `columnHeaders` list will begin with the dimensions specified in the API
   * request, which will be followed by the metrics specified in the API
   * request. The order of both dimensions and metrics will match the ordering
   * in the API request. For example, if the API request contains the parameters
   * `dimensions=ageGroup,gender&metrics=viewerPercentage`, the API response
   * will return columns in this order: `ageGroup`, `gender`,
   * `viewerPercentage`.
   *
   * @param ResultTableColumnHeader[] $columnHeaders
   */
  public function setColumnHeaders($columnHeaders)
  {
    $this->columnHeaders = $columnHeaders;
  }
  /**
   * @return ResultTableColumnHeader[]
   */
  public function getColumnHeaders()
  {
    return $this->columnHeaders;
  }
  /**
   * When set, indicates that the operation failed.
   *
   * @param Errors $errors
   */
  public function setErrors(Errors $errors)
  {
    $this->errors = $errors;
  }
  /**
   * @return Errors
   */
  public function getErrors()
  {
    return $this->errors;
  }
  /**
   * This value specifies the type of data included in the API response. For the
   * query method, the kind property value will be
   * `youtubeAnalytics#resultTable`.
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
   * The list contains all rows of the result table. Each item in the list is an
   * array that contains comma-delimited data corresponding to a single row of
   * data. The order of the comma-delimited data fields will match the order of
   * the columns listed in the `columnHeaders` field. If no data is available
   * for the given query, the `rows` element will be omitted from the response.
   * The response for a query with the `day` dimension will not contain rows for
   * the most recent days.
   *
   * @param array[] $rows
   */
  public function setRows($rows)
  {
    $this->rows = $rows;
  }
  /**
   * @return array[]
   */
  public function getRows()
  {
    return $this->rows;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(QueryResponse::class, 'Google_Service_YouTubeAnalytics_QueryResponse');
