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

namespace Google\Service\Spanner;

class ResultSetMetadata extends \Google\Model
{
  protected $rowTypeType = StructType::class;
  protected $rowTypeDataType = '';
  protected $transactionType = Transaction::class;
  protected $transactionDataType = '';
  protected $undeclaredParametersType = StructType::class;
  protected $undeclaredParametersDataType = '';

  /**
   * Indicates the field names and types for the rows in the result set. For
   * example, a SQL query like `"SELECT UserId, UserName FROM Users"` could
   * return a `row_type` value like: "fields": [ { "name": "UserId", "type": {
   * "code": "INT64" } }, { "name": "UserName", "type": { "code": "STRING" } },
   * ]
   *
   * @param StructType $rowType
   */
  public function setRowType(StructType $rowType)
  {
    $this->rowType = $rowType;
  }
  /**
   * @return StructType
   */
  public function getRowType()
  {
    return $this->rowType;
  }
  /**
   * If the read or SQL query began a transaction as a side-effect, the
   * information about the new transaction is yielded here.
   *
   * @param Transaction $transaction
   */
  public function setTransaction(Transaction $transaction)
  {
    $this->transaction = $transaction;
  }
  /**
   * @return Transaction
   */
  public function getTransaction()
  {
    return $this->transaction;
  }
  /**
   * A SQL query can be parameterized. In PLAN mode, these parameters can be
   * undeclared. This indicates the field names and types for those undeclared
   * parameters in the SQL query. For example, a SQL query like `"SELECT * FROM
   * Users where UserId = @userId and UserName = @userName "` could return a
   * `undeclared_parameters` value like: "fields": [ { "name": "UserId", "type":
   * { "code": "INT64" } }, { "name": "UserName", "type": { "code": "STRING" }
   * }, ]
   *
   * @param StructType $undeclaredParameters
   */
  public function setUndeclaredParameters(StructType $undeclaredParameters)
  {
    $this->undeclaredParameters = $undeclaredParameters;
  }
  /**
   * @return StructType
   */
  public function getUndeclaredParameters()
  {
    return $this->undeclaredParameters;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResultSetMetadata::class, 'Google_Service_Spanner_ResultSetMetadata');
