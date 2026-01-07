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

namespace Google\Service\Datastore;

class RunQueryResponse extends \Google\Model
{
  protected $batchType = QueryResultBatch::class;
  protected $batchDataType = '';
  protected $explainMetricsType = ExplainMetrics::class;
  protected $explainMetricsDataType = '';
  protected $queryType = Query::class;
  protected $queryDataType = '';
  /**
   * The identifier of the transaction that was started as part of this RunQuery
   * request. Set only when ReadOptions.new_transaction was set in
   * RunQueryRequest.read_options.
   *
   * @var string
   */
  public $transaction;

  /**
   * A batch of query results. This is always present unless running a query
   * under explain-only mode: RunQueryRequest.explain_options was provided and
   * ExplainOptions.analyze was set to false.
   *
   * @param QueryResultBatch $batch
   */
  public function setBatch(QueryResultBatch $batch)
  {
    $this->batch = $batch;
  }
  /**
   * @return QueryResultBatch
   */
  public function getBatch()
  {
    return $this->batch;
  }
  /**
   * Query explain metrics. This is only present when the
   * RunQueryRequest.explain_options is provided, and it is sent only once with
   * the last response in the stream.
   *
   * @param ExplainMetrics $explainMetrics
   */
  public function setExplainMetrics(ExplainMetrics $explainMetrics)
  {
    $this->explainMetrics = $explainMetrics;
  }
  /**
   * @return ExplainMetrics
   */
  public function getExplainMetrics()
  {
    return $this->explainMetrics;
  }
  /**
   * The parsed form of the `GqlQuery` from the request, if it was set.
   *
   * @param Query $query
   */
  public function setQuery(Query $query)
  {
    $this->query = $query;
  }
  /**
   * @return Query
   */
  public function getQuery()
  {
    return $this->query;
  }
  /**
   * The identifier of the transaction that was started as part of this RunQuery
   * request. Set only when ReadOptions.new_transaction was set in
   * RunQueryRequest.read_options.
   *
   * @param string $transaction
   */
  public function setTransaction($transaction)
  {
    $this->transaction = $transaction;
  }
  /**
   * @return string
   */
  public function getTransaction()
  {
    return $this->transaction;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RunQueryResponse::class, 'Google_Service_Datastore_RunQueryResponse');
