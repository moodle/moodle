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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1QueryMetadata extends \Google\Collection
{
  protected $collection_key = 'metrics';
  /**
   * Dimensions of the AsyncQuery.
   *
   * @var string[]
   */
  public $dimensions;
  /**
   * End timestamp of the query range.
   *
   * @var string
   */
  public $endTimestamp;
  /**
   * Metrics of the AsyncQuery. Example:
   * ["name:message_count,func:sum,alias:sum_message_count"]
   *
   * @var string[]
   */
  public $metrics;
  /**
   * Output format.
   *
   * @var string
   */
  public $outputFormat;
  /**
   * Start timestamp of the query range.
   *
   * @var string
   */
  public $startTimestamp;
  /**
   * Query GroupBy time unit.
   *
   * @var string
   */
  public $timeUnit;

  /**
   * Dimensions of the AsyncQuery.
   *
   * @param string[] $dimensions
   */
  public function setDimensions($dimensions)
  {
    $this->dimensions = $dimensions;
  }
  /**
   * @return string[]
   */
  public function getDimensions()
  {
    return $this->dimensions;
  }
  /**
   * End timestamp of the query range.
   *
   * @param string $endTimestamp
   */
  public function setEndTimestamp($endTimestamp)
  {
    $this->endTimestamp = $endTimestamp;
  }
  /**
   * @return string
   */
  public function getEndTimestamp()
  {
    return $this->endTimestamp;
  }
  /**
   * Metrics of the AsyncQuery. Example:
   * ["name:message_count,func:sum,alias:sum_message_count"]
   *
   * @param string[] $metrics
   */
  public function setMetrics($metrics)
  {
    $this->metrics = $metrics;
  }
  /**
   * @return string[]
   */
  public function getMetrics()
  {
    return $this->metrics;
  }
  /**
   * Output format.
   *
   * @param string $outputFormat
   */
  public function setOutputFormat($outputFormat)
  {
    $this->outputFormat = $outputFormat;
  }
  /**
   * @return string
   */
  public function getOutputFormat()
  {
    return $this->outputFormat;
  }
  /**
   * Start timestamp of the query range.
   *
   * @param string $startTimestamp
   */
  public function setStartTimestamp($startTimestamp)
  {
    $this->startTimestamp = $startTimestamp;
  }
  /**
   * @return string
   */
  public function getStartTimestamp()
  {
    return $this->startTimestamp;
  }
  /**
   * Query GroupBy time unit.
   *
   * @param string $timeUnit
   */
  public function setTimeUnit($timeUnit)
  {
    $this->timeUnit = $timeUnit;
  }
  /**
   * @return string
   */
  public function getTimeUnit()
  {
    return $this->timeUnit;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1QueryMetadata::class, 'Google_Service_Apigee_GoogleCloudApigeeV1QueryMetadata');
