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

class RunQueryResponse extends \Google\Model
{
  protected $documentType = Document::class;
  protected $documentDataType = '';
  /**
   * If present, Firestore has completely finished the request and no more
   * documents will be returned.
   *
   * @var bool
   */
  public $done;
  protected $explainMetricsType = ExplainMetrics::class;
  protected $explainMetricsDataType = '';
  /**
   * The time at which the document was read. This may be monotonically
   * increasing; in this case, the previous documents in the result stream are
   * guaranteed not to have changed between their `read_time` and this one. If
   * the query returns no results, a response with `read_time` and no `document`
   * will be sent, and this represents the time at which the query was run.
   *
   * @var string
   */
  public $readTime;
  /**
   * The number of results that have been skipped due to an offset between the
   * last response and the current response.
   *
   * @var int
   */
  public $skippedResults;
  /**
   * The transaction that was started as part of this request. Can only be set
   * in the first response, and only if RunQueryRequest.new_transaction was set
   * in the request. If set, no other fields will be set in this response.
   *
   * @var string
   */
  public $transaction;

  /**
   * A query result, not set when reporting partial progress.
   *
   * @param Document $document
   */
  public function setDocument(Document $document)
  {
    $this->document = $document;
  }
  /**
   * @return Document
   */
  public function getDocument()
  {
    return $this->document;
  }
  /**
   * If present, Firestore has completely finished the request and no more
   * documents will be returned.
   *
   * @param bool $done
   */
  public function setDone($done)
  {
    $this->done = $done;
  }
  /**
   * @return bool
   */
  public function getDone()
  {
    return $this->done;
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
   * The time at which the document was read. This may be monotonically
   * increasing; in this case, the previous documents in the result stream are
   * guaranteed not to have changed between their `read_time` and this one. If
   * the query returns no results, a response with `read_time` and no `document`
   * will be sent, and this represents the time at which the query was run.
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
   * The number of results that have been skipped due to an offset between the
   * last response and the current response.
   *
   * @param int $skippedResults
   */
  public function setSkippedResults($skippedResults)
  {
    $this->skippedResults = $skippedResults;
  }
  /**
   * @return int
   */
  public function getSkippedResults()
  {
    return $this->skippedResults;
  }
  /**
   * The transaction that was started as part of this request. Can only be set
   * in the first response, and only if RunQueryRequest.new_transaction was set
   * in the request. If set, no other fields will be set in this response.
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
class_alias(RunQueryResponse::class, 'Google_Service_Firestore_RunQueryResponse');
