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

class TableDataInsertAllRequest extends \Google\Collection
{
  protected $collection_key = 'rows';
  /**
   * Optional. Accept rows that contain values that do not match the schema. The
   * unknown values are ignored. Default is false, which treats unknown values
   * as errors.
   *
   * @var bool
   */
  public $ignoreUnknownValues;
  /**
   * Optional. The resource type of the response. The value is not checked at
   * the backend. Historically, it has been set to
   * "bigquery#tableDataInsertAllRequest" but you are not required to set it.
   *
   * @var string
   */
  public $kind;
  protected $rowsType = TableDataInsertAllRequestRows::class;
  protected $rowsDataType = 'array';
  /**
   * Optional. Insert all valid rows of a request, even if invalid rows exist.
   * The default value is false, which causes the entire request to fail if any
   * invalid rows exist.
   *
   * @var bool
   */
  public $skipInvalidRows;
  /**
   * Optional. If specified, treats the destination table as a base template,
   * and inserts the rows into an instance table named
   * "{destination}{templateSuffix}". BigQuery will manage creation of the
   * instance table, using the schema of the base template table. See
   * https://cloud.google.com/bigquery/streaming-data-into-bigquery#template-
   * tables for considerations when working with templates tables.
   *
   * @var string
   */
  public $templateSuffix;
  /**
   * Optional. Unique request trace id. Used for debugging purposes only. It is
   * case-sensitive, limited to up to 36 ASCII characters. A UUID is
   * recommended.
   *
   * @var string
   */
  public $traceId;

  /**
   * Optional. Accept rows that contain values that do not match the schema. The
   * unknown values are ignored. Default is false, which treats unknown values
   * as errors.
   *
   * @param bool $ignoreUnknownValues
   */
  public function setIgnoreUnknownValues($ignoreUnknownValues)
  {
    $this->ignoreUnknownValues = $ignoreUnknownValues;
  }
  /**
   * @return bool
   */
  public function getIgnoreUnknownValues()
  {
    return $this->ignoreUnknownValues;
  }
  /**
   * Optional. The resource type of the response. The value is not checked at
   * the backend. Historically, it has been set to
   * "bigquery#tableDataInsertAllRequest" but you are not required to set it.
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
   * @param TableDataInsertAllRequestRows[] $rows
   */
  public function setRows($rows)
  {
    $this->rows = $rows;
  }
  /**
   * @return TableDataInsertAllRequestRows[]
   */
  public function getRows()
  {
    return $this->rows;
  }
  /**
   * Optional. Insert all valid rows of a request, even if invalid rows exist.
   * The default value is false, which causes the entire request to fail if any
   * invalid rows exist.
   *
   * @param bool $skipInvalidRows
   */
  public function setSkipInvalidRows($skipInvalidRows)
  {
    $this->skipInvalidRows = $skipInvalidRows;
  }
  /**
   * @return bool
   */
  public function getSkipInvalidRows()
  {
    return $this->skipInvalidRows;
  }
  /**
   * Optional. If specified, treats the destination table as a base template,
   * and inserts the rows into an instance table named
   * "{destination}{templateSuffix}". BigQuery will manage creation of the
   * instance table, using the schema of the base template table. See
   * https://cloud.google.com/bigquery/streaming-data-into-bigquery#template-
   * tables for considerations when working with templates tables.
   *
   * @param string $templateSuffix
   */
  public function setTemplateSuffix($templateSuffix)
  {
    $this->templateSuffix = $templateSuffix;
  }
  /**
   * @return string
   */
  public function getTemplateSuffix()
  {
    return $this->templateSuffix;
  }
  /**
   * Optional. Unique request trace id. Used for debugging purposes only. It is
   * case-sensitive, limited to up to 36 ASCII characters. A UUID is
   * recommended.
   *
   * @param string $traceId
   */
  public function setTraceId($traceId)
  {
    $this->traceId = $traceId;
  }
  /**
   * @return string
   */
  public function getTraceId()
  {
    return $this->traceId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TableDataInsertAllRequest::class, 'Google_Service_Bigquery_TableDataInsertAllRequest');
