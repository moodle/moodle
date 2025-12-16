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

class GoogleCloudApigeeV1SecurityReportMetadata extends \Google\Collection
{
  protected $collection_key = 'metrics';
  /**
   * Dimensions of the SecurityReport.
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
   * Metrics of the SecurityReport. Example:
   * ["name:bot_count,func:sum,alias:sum_bot_count"]
   *
   * @var string[]
   */
  public $metrics;
  /**
   * MIME type / Output format.
   *
   * @var string
   */
  public $mimeType;
  /**
   * Start timestamp of the query range.
   *
   * @var string
   */
  public $startTimestamp;
  /**
   * Query GroupBy time unit. Example: "seconds", "minute", "hour"
   *
   * @var string
   */
  public $timeUnit;

  /**
   * Dimensions of the SecurityReport.
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
   * Metrics of the SecurityReport. Example:
   * ["name:bot_count,func:sum,alias:sum_bot_count"]
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
   * MIME type / Output format.
   *
   * @param string $mimeType
   */
  public function setMimeType($mimeType)
  {
    $this->mimeType = $mimeType;
  }
  /**
   * @return string
   */
  public function getMimeType()
  {
    return $this->mimeType;
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
   * Query GroupBy time unit. Example: "seconds", "minute", "hour"
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
class_alias(GoogleCloudApigeeV1SecurityReportMetadata::class, 'Google_Service_Apigee_GoogleCloudApigeeV1SecurityReportMetadata');
