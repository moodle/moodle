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

namespace Google\Service\SQLAdmin;

class QueryResult extends \Google\Collection
{
  protected $collection_key = 'rows';
  protected $columnsType = Column::class;
  protected $columnsDataType = 'array';
  /**
   * Message related to the SQL execution result.
   *
   * @var string
   */
  public $message;
  /**
   * Set to true if the SQL execution's result is truncated due to size limits
   * or an error retrieving results.
   *
   * @var bool
   */
  public $partialResult;
  protected $rowsType = Row::class;
  protected $rowsDataType = 'array';
  protected $statusType = Status::class;
  protected $statusDataType = '';

  /**
   * List of columns included in the result. This also includes the data type of
   * the column.
   *
   * @param Column[] $columns
   */
  public function setColumns($columns)
  {
    $this->columns = $columns;
  }
  /**
   * @return Column[]
   */
  public function getColumns()
  {
    return $this->columns;
  }
  /**
   * Message related to the SQL execution result.
   *
   * @param string $message
   */
  public function setMessage($message)
  {
    $this->message = $message;
  }
  /**
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }
  /**
   * Set to true if the SQL execution's result is truncated due to size limits
   * or an error retrieving results.
   *
   * @param bool $partialResult
   */
  public function setPartialResult($partialResult)
  {
    $this->partialResult = $partialResult;
  }
  /**
   * @return bool
   */
  public function getPartialResult()
  {
    return $this->partialResult;
  }
  /**
   * Rows returned by the SQL statement.
   *
   * @param Row[] $rows
   */
  public function setRows($rows)
  {
    $this->rows = $rows;
  }
  /**
   * @return Row[]
   */
  public function getRows()
  {
    return $this->rows;
  }
  /**
   * If results were truncated due to an error, details of that error.
   *
   * @param Status $status
   */
  public function setStatus(Status $status)
  {
    $this->status = $status;
  }
  /**
   * @return Status
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(QueryResult::class, 'Google_Service_SQLAdmin_QueryResult');
