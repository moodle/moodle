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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1SessionEventQueryDetail extends \Google\Model
{
  /**
   * An unspecified Engine type.
   */
  public const ENGINE_ENGINE_UNSPECIFIED = 'ENGINE_UNSPECIFIED';
  /**
   * Spark-sql engine is specified in Query.
   */
  public const ENGINE_SPARK_SQL = 'SPARK_SQL';
  /**
   * BigQuery engine is specified in Query.
   */
  public const ENGINE_BIGQUERY = 'BIGQUERY';
  /**
   * The data processed by the query.
   *
   * @var string
   */
  public $dataProcessedBytes;
  /**
   * Time taken for execution of the query.
   *
   * @var string
   */
  public $duration;
  /**
   * Query Execution engine.
   *
   * @var string
   */
  public $engine;
  /**
   * The unique Query id identifying the query.
   *
   * @var string
   */
  public $queryId;
  /**
   * The query text executed.
   *
   * @var string
   */
  public $queryText;
  /**
   * The size of results the query produced.
   *
   * @var string
   */
  public $resultSizeBytes;

  /**
   * The data processed by the query.
   *
   * @param string $dataProcessedBytes
   */
  public function setDataProcessedBytes($dataProcessedBytes)
  {
    $this->dataProcessedBytes = $dataProcessedBytes;
  }
  /**
   * @return string
   */
  public function getDataProcessedBytes()
  {
    return $this->dataProcessedBytes;
  }
  /**
   * Time taken for execution of the query.
   *
   * @param string $duration
   */
  public function setDuration($duration)
  {
    $this->duration = $duration;
  }
  /**
   * @return string
   */
  public function getDuration()
  {
    return $this->duration;
  }
  /**
   * Query Execution engine.
   *
   * Accepted values: ENGINE_UNSPECIFIED, SPARK_SQL, BIGQUERY
   *
   * @param self::ENGINE_* $engine
   */
  public function setEngine($engine)
  {
    $this->engine = $engine;
  }
  /**
   * @return self::ENGINE_*
   */
  public function getEngine()
  {
    return $this->engine;
  }
  /**
   * The unique Query id identifying the query.
   *
   * @param string $queryId
   */
  public function setQueryId($queryId)
  {
    $this->queryId = $queryId;
  }
  /**
   * @return string
   */
  public function getQueryId()
  {
    return $this->queryId;
  }
  /**
   * The query text executed.
   *
   * @param string $queryText
   */
  public function setQueryText($queryText)
  {
    $this->queryText = $queryText;
  }
  /**
   * @return string
   */
  public function getQueryText()
  {
    return $this->queryText;
  }
  /**
   * The size of results the query produced.
   *
   * @param string $resultSizeBytes
   */
  public function setResultSizeBytes($resultSizeBytes)
  {
    $this->resultSizeBytes = $resultSizeBytes;
  }
  /**
   * @return string
   */
  public function getResultSizeBytes()
  {
    return $this->resultSizeBytes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1SessionEventQueryDetail::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1SessionEventQueryDetail');
