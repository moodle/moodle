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

class ExecuteSqlRequest extends \Google\Model
{
  /**
   * The default mode. Only the statement results are returned.
   */
  public const QUERY_MODE_NORMAL = 'NORMAL';
  /**
   * This mode returns only the query plan, without any results or execution
   * statistics information.
   */
  public const QUERY_MODE_PLAN = 'PLAN';
  /**
   * This mode returns the query plan, overall execution statistics, operator
   * level execution statistics along with the results. This has a performance
   * overhead compared to the other modes. It isn't recommended to use this mode
   * for production traffic.
   */
  public const QUERY_MODE_PROFILE = 'PROFILE';
  /**
   * This mode returns the overall (but not operator-level) execution statistics
   * along with the results.
   */
  public const QUERY_MODE_WITH_STATS = 'WITH_STATS';
  /**
   * This mode returns the query plan, overall (but not operator-level)
   * execution statistics along with the results.
   */
  public const QUERY_MODE_WITH_PLAN_AND_STATS = 'WITH_PLAN_AND_STATS';
  /**
   * If this is for a partitioned query and this field is set to `true`, the
   * request is executed with Spanner Data Boost independent compute resources.
   * If the field is set to `true` but the request doesn't set
   * `partition_token`, the API returns an `INVALID_ARGUMENT` error.
   *
   * @var bool
   */
  public $dataBoostEnabled;
  protected $directedReadOptionsType = DirectedReadOptions::class;
  protected $directedReadOptionsDataType = '';
  /**
   * Optional. If set to `true`, this statement marks the end of the
   * transaction. After this statement executes, you must commit or abort the
   * transaction. Attempts to execute any other requests against this
   * transaction (including reads and queries) are rejected. For DML statements,
   * setting this option might cause some error reporting to be deferred until
   * commit time (for example, validation of unique constraints). Given this,
   * successful execution of a DML statement shouldn't be assumed until a
   * subsequent `Commit` call completes successfully.
   *
   * @var bool
   */
  public $lastStatement;
  protected $paramTypesType = Type::class;
  protected $paramTypesDataType = 'map';
  /**
   * Parameter names and values that bind to placeholders in the SQL string. A
   * parameter placeholder consists of the `@` character followed by the
   * parameter name (for example, `@firstName`). Parameter names must conform to
   * the naming requirements of identifiers as specified at
   * https://cloud.google.com/spanner/docs/lexical#identifiers. Parameters can
   * appear anywhere that a literal value is expected. The same parameter name
   * can be used more than once, for example: `"WHERE id > @msg_id AND id <
   * @msg_id + 100"` It's an error to execute a SQL statement with unbound
   * parameters.
   *
   * @var array[]
   */
  public $params;
  /**
   * If present, results are restricted to the specified partition previously
   * created using `PartitionQuery`. There must be an exact match for the values
   * of fields common to this message and the `PartitionQueryRequest` message
   * used to create this `partition_token`.
   *
   * @var string
   */
  public $partitionToken;
  /**
   * Used to control the amount of debugging information returned in
   * ResultSetStats. If partition_token is set, query_mode can only be set to
   * QueryMode.NORMAL.
   *
   * @var string
   */
  public $queryMode;
  protected $queryOptionsType = QueryOptions::class;
  protected $queryOptionsDataType = '';
  protected $requestOptionsType = RequestOptions::class;
  protected $requestOptionsDataType = '';
  /**
   * If this request is resuming a previously interrupted SQL statement
   * execution, `resume_token` should be copied from the last PartialResultSet
   * yielded before the interruption. Doing this enables the new SQL statement
   * execution to resume where the last one left off. The rest of the request
   * parameters must exactly match the request that yielded this token.
   *
   * @var string
   */
  public $resumeToken;
  /**
   * A per-transaction sequence number used to identify this request. This field
   * makes each request idempotent such that if the request is received multiple
   * times, at most one succeeds. The sequence number must be monotonically
   * increasing within the transaction. If a request arrives for the first time
   * with an out-of-order sequence number, the transaction can be aborted.
   * Replays of previously handled requests yield the same response as the first
   * execution. Required for DML statements. Ignored for queries.
   *
   * @var string
   */
  public $seqno;
  /**
   * Required. The SQL string.
   *
   * @var string
   */
  public $sql;
  protected $transactionType = TransactionSelector::class;
  protected $transactionDataType = '';

  /**
   * If this is for a partitioned query and this field is set to `true`, the
   * request is executed with Spanner Data Boost independent compute resources.
   * If the field is set to `true` but the request doesn't set
   * `partition_token`, the API returns an `INVALID_ARGUMENT` error.
   *
   * @param bool $dataBoostEnabled
   */
  public function setDataBoostEnabled($dataBoostEnabled)
  {
    $this->dataBoostEnabled = $dataBoostEnabled;
  }
  /**
   * @return bool
   */
  public function getDataBoostEnabled()
  {
    return $this->dataBoostEnabled;
  }
  /**
   * Directed read options for this request.
   *
   * @param DirectedReadOptions $directedReadOptions
   */
  public function setDirectedReadOptions(DirectedReadOptions $directedReadOptions)
  {
    $this->directedReadOptions = $directedReadOptions;
  }
  /**
   * @return DirectedReadOptions
   */
  public function getDirectedReadOptions()
  {
    return $this->directedReadOptions;
  }
  /**
   * Optional. If set to `true`, this statement marks the end of the
   * transaction. After this statement executes, you must commit or abort the
   * transaction. Attempts to execute any other requests against this
   * transaction (including reads and queries) are rejected. For DML statements,
   * setting this option might cause some error reporting to be deferred until
   * commit time (for example, validation of unique constraints). Given this,
   * successful execution of a DML statement shouldn't be assumed until a
   * subsequent `Commit` call completes successfully.
   *
   * @param bool $lastStatement
   */
  public function setLastStatement($lastStatement)
  {
    $this->lastStatement = $lastStatement;
  }
  /**
   * @return bool
   */
  public function getLastStatement()
  {
    return $this->lastStatement;
  }
  /**
   * It isn't always possible for Cloud Spanner to infer the right SQL type from
   * a JSON value. For example, values of type `BYTES` and values of type
   * `STRING` both appear in params as JSON strings. In these cases, you can use
   * `param_types` to specify the exact SQL type for some or all of the SQL
   * statement parameters. See the definition of Type for more information about
   * SQL types.
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
   * parameter name (for example, `@firstName`). Parameter names must conform to
   * the naming requirements of identifiers as specified at
   * https://cloud.google.com/spanner/docs/lexical#identifiers. Parameters can
   * appear anywhere that a literal value is expected. The same parameter name
   * can be used more than once, for example: `"WHERE id > @msg_id AND id <
   * @msg_id + 100"` It's an error to execute a SQL statement with unbound
   * parameters.
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
   * If present, results are restricted to the specified partition previously
   * created using `PartitionQuery`. There must be an exact match for the values
   * of fields common to this message and the `PartitionQueryRequest` message
   * used to create this `partition_token`.
   *
   * @param string $partitionToken
   */
  public function setPartitionToken($partitionToken)
  {
    $this->partitionToken = $partitionToken;
  }
  /**
   * @return string
   */
  public function getPartitionToken()
  {
    return $this->partitionToken;
  }
  /**
   * Used to control the amount of debugging information returned in
   * ResultSetStats. If partition_token is set, query_mode can only be set to
   * QueryMode.NORMAL.
   *
   * Accepted values: NORMAL, PLAN, PROFILE, WITH_STATS, WITH_PLAN_AND_STATS
   *
   * @param self::QUERY_MODE_* $queryMode
   */
  public function setQueryMode($queryMode)
  {
    $this->queryMode = $queryMode;
  }
  /**
   * @return self::QUERY_MODE_*
   */
  public function getQueryMode()
  {
    return $this->queryMode;
  }
  /**
   * Query optimizer configuration to use for the given query.
   *
   * @param QueryOptions $queryOptions
   */
  public function setQueryOptions(QueryOptions $queryOptions)
  {
    $this->queryOptions = $queryOptions;
  }
  /**
   * @return QueryOptions
   */
  public function getQueryOptions()
  {
    return $this->queryOptions;
  }
  /**
   * Common options for this request.
   *
   * @param RequestOptions $requestOptions
   */
  public function setRequestOptions(RequestOptions $requestOptions)
  {
    $this->requestOptions = $requestOptions;
  }
  /**
   * @return RequestOptions
   */
  public function getRequestOptions()
  {
    return $this->requestOptions;
  }
  /**
   * If this request is resuming a previously interrupted SQL statement
   * execution, `resume_token` should be copied from the last PartialResultSet
   * yielded before the interruption. Doing this enables the new SQL statement
   * execution to resume where the last one left off. The rest of the request
   * parameters must exactly match the request that yielded this token.
   *
   * @param string $resumeToken
   */
  public function setResumeToken($resumeToken)
  {
    $this->resumeToken = $resumeToken;
  }
  /**
   * @return string
   */
  public function getResumeToken()
  {
    return $this->resumeToken;
  }
  /**
   * A per-transaction sequence number used to identify this request. This field
   * makes each request idempotent such that if the request is received multiple
   * times, at most one succeeds. The sequence number must be monotonically
   * increasing within the transaction. If a request arrives for the first time
   * with an out-of-order sequence number, the transaction can be aborted.
   * Replays of previously handled requests yield the same response as the first
   * execution. Required for DML statements. Ignored for queries.
   *
   * @param string $seqno
   */
  public function setSeqno($seqno)
  {
    $this->seqno = $seqno;
  }
  /**
   * @return string
   */
  public function getSeqno()
  {
    return $this->seqno;
  }
  /**
   * Required. The SQL string.
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
   * The transaction to use. For queries, if none is provided, the default is a
   * temporary read-only transaction with strong concurrency. Standard DML
   * statements require a read-write transaction. To protect against replays,
   * single-use transactions are not supported. The caller must either supply an
   * existing transaction ID or begin a new transaction. Partitioned DML
   * requires an existing Partitioned DML transaction ID.
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
class_alias(ExecuteSqlRequest::class, 'Google_Service_Spanner_ExecuteSqlRequest');
