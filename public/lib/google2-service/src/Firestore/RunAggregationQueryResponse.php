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

namespace Google\Service\Firestore;

class RunAggregationQueryResponse extends \Google\Model
{
  protected $explainMetricsType = ExplainMetrics::class;
  protected $explainMetricsDataType = '';
  /**
   * The time at which the aggregate result was computed. This is always
   * monotonically increasing; in this case, the previous AggregationResult in
   * the result stream are guaranteed not to have changed between their
   * `read_time` and this one. If the query returns no results, a response with
   * `read_time` and no `result` will be sent, and this represents the time at
   * which the query was run.
   *
   * @var string
   */
  public $readTime;
  protected $resultType = AggregationResult::class;
  protected $resultDataType = '';
  /**
   * The transaction that was started as part of this request. Only present on
   * the first response when the request requested to start a new transaction.
   *
   * @var string
   */
  public $transaction;

  /**
   * Query explain metrics. This is only present when the
   * RunAggregationQueryRequest.explain_options is provided, and it is sent only
   * once with the last response in the stream.
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
   * The time at which the aggregate result was computed. This is always
   * monotonically increasing; in this case, the previous AggregationResult in
   * the result stream are guaranteed not to have changed between their
   * `read_time` and this one. If the query returns no results, a response with
   * `read_time` and no `result` will be sent, and this represents the time at
   * which the query was run.
   *
   * @param string $readTime
   */
  public function setReadTime($readTime)
  {
    $this->readTime = $readTime;
  }
  /**
   * @return string
   */
  public function getReadTime()
  {
    return $this->readTime;
  }
  /**
   * A single aggregation result. Not present when reporting partial progress.
   *
   * @param AggregationResult $result
   */
  public function setResult(AggregationResult $result)
  {
    $this->result = $result;
  }
  /**
   * @return AggregationResult
   */
  public function getResult()
  {
    return $this->result;
  }
  /**
   * The transaction that was started as part of this request. Only present on
   * the first response when the request requested to start a new transaction.
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
class_alias(RunAggregationQueryResponse::class, 'Google_Service_Firestore_RunAggregationQueryResponse');
