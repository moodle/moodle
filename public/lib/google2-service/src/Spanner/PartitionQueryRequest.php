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

class PartitionQueryRequest extends \Google\Model
{
  protected $paramTypesType = Type::class;
  protected $paramTypesDataType = 'map';
  /**
   * Parameter names and values that bind to placeholders in the SQL string. A
   * parameter placeholder consists of the `@` character followed by the
   * parameter name (for example, `@firstName`). Parameter names can contain
   * letters, numbers, and underscores. Parameters can appear anywhere that a
   * literal value is expected. The same parameter name can be used more than
   * once, for example: `"WHERE id > @msg_id AND id < @msg_id + 100"` It's an
   * error to execute a SQL statement with unbound parameters.
   *
   * @var array[]
   */
  public $params;
  protected $partitionOptionsType = PartitionOptions::class;
  protected $partitionOptionsDataType = '';
  /**
   * Required. The query request to generate partitions for. The request fails
   * if the query isn't root partitionable. For a query to be root
   * partitionable, it needs to satisfy a few conditions. For example, if the
   * query execution plan contains a distributed union operator, then it must be
   * the first operator in the plan. For more information about other
   * conditions, see [Read data in parallel](https://cloud.google.com/spanner/do
   * cs/reads#read_data_in_parallel). The query request must not contain DML
   * commands, such as `INSERT`, `UPDATE`, or `DELETE`. Use
   * `ExecuteStreamingSql` with a `PartitionedDml` transaction for large,
   * partition-friendly DML operations.
   *
   * @var string
   */
  public $sql;
  protected $transactionType = TransactionSelector::class;
  protected $transactionDataType = '';

  /**
   * It isn't always possible for Cloud Spanner to infer the right SQL type from
   * a JSON value. For example, values of type `BYTES` and values of type
   * `STRING` both appear in params as JSON strings. In these cases,
   * `param_types` can be used to specify the exact SQL type for some or all of
   * the SQL query parameters. See the definition of Type for more information
   * about SQL types.
   *
   * @param Type[] $paramTypes
   */
  public function setParamTypes($paramTypes)
  {
    $this->paramTypes = $paramTypes;
  }
  /**
   * @return Type[]
   */
  public function getParamTypes()
  {
    return $this->paramTypes;
  }
  /**
   * Parameter names and values that bind to placeholders in the SQL string. A
   * parameter placeholder consists of the `@` character followed by the
   * parameter name (for example, `@firstName`). Parameter names can contain
   * letters, numbers, and underscores. Parameters can appear anywhere that a
   * literal value is expected. The same parameter name can be used more than
   * once, for example: `"WHERE id > @msg_id AND id < @msg_id + 100"` It's an
   * error to execute a SQL statement with unbound parameters.
   *
   * @param array[] $params
   */
  public function setParams($params)
  {
    $this->params = $params;
  }
  /**
   * @return array[]
   */
  public function getParams()
  {
    return $this->params;
  }
  /**
   * Additional options that affect how many partitions are created.
   *
   * @param PartitionOptions $partitionOptions
   */
  public function setPartitionOptions(PartitionOptions $partitionOptions)
  {
    $this->partitionOptions = $partitionOptions;
  }
  /**
   * @return PartitionOptions
   */
  public function getPartitionOptions()
  {
    return $this->partitionOptions;
  }
  /**
   * Required. The query request to generate partitions for. The request fails
   * if the query isn't root partitionable. For a query to be root
   * partitionable, it needs to satisfy a few conditions. For example, if the
   * query execution plan contains a distributed union operator, then it must be
   * the first operator in the plan. For more information about other
   * conditions, see [Read data in parallel](https://cloud.google.com/spanner/do
   * cs/reads#read_data_in_parallel). The query request must not contain DML
   * commands, such as `INSERT`, `UPDATE`, or `DELETE`. Use
   * `ExecuteStreamingSql` with a `PartitionedDml` transaction for large,
   * partition-friendly DML operations.
   *
   * @param string $sql
   */
  public function setSql($sql)
  {
    $this->sql = $sql;
  }
  /**
   * @return string
   */
  public function getSql()
  {
    return $this->sql;
  }
  /**
   * Read-only snapshot transactions are supported, read and write and single-
   * use transactions are not.
   *
   * @param TransactionSelector $transaction
   */
  public function setTransaction(TransactionSelector $transaction)
  {
    $this->transaction = $transaction;
  }
  /**
   * @return TransactionSelector
   */
  public function getTransaction()
  {
    return $this->transaction;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PartitionQueryRequest::class, 'Google_Service_Spanner_PartitionQueryRequest');
