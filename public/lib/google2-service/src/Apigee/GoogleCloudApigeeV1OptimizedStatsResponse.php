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

class GoogleCloudApigeeV1OptimizedStatsResponse extends \Google\Collection
{
  protected $collection_key = 'TimeUnit';
  protected $internal_gapi_mappings = [
        "timeUnit" => "TimeUnit",
  ];
  /**
   * List of time unit values. Time unit refers to an epoch timestamp value.
   *
   * @var string[]
   */
  public $timeUnit;
  protected $metaDataType = GoogleCloudApigeeV1Metadata::class;
  protected $metaDataDataType = '';
  /**
   * Boolean flag that indicates whether the results were truncated based on the
   * limit parameter.
   *
   * @var bool
   */
  public $resultTruncated;
  protected $statsType = GoogleCloudApigeeV1OptimizedStatsNode::class;
  protected $statsDataType = '';

  /**
   * List of time unit values. Time unit refers to an epoch timestamp value.
   *
   * @param string[] $timeUnit
   */
  public function setTimeUnit($timeUnit)
  {
    $this->timeUnit = $timeUnit;
  }
  /**
   * @return string[]
   */
  public function getTimeUnit()
  {
    return $this->timeUnit;
  }
  /**
   * Metadata information about the query executed.
   *
   * @param GoogleCloudApigeeV1Metadata $metaData
   */
  public function setMetaData(GoogleCloudApigeeV1Metadata $metaData)
  {
    $this->metaData = $metaData;
  }
  /**
   * @return GoogleCloudApigeeV1Metadata
   */
  public function getMetaData()
  {
    return $this->metaData;
  }
  /**
   * Boolean flag that indicates whether the results were truncated based on the
   * limit parameter.
   *
   * @param bool $resultTruncated
   */
  public function setResultTruncated($resultTruncated)
  {
    $this->resultTruncated = $resultTruncated;
  }
  /**
   * @return bool
   */
  public function getResultTruncated()
  {
    return $this->resultTruncated;
  }
  /**
   * `stats` results.
   *
   * @param GoogleCloudApigeeV1OptimizedStatsNode $stats
   */
  public function setStats(GoogleCloudApigeeV1OptimizedStatsNode $stats)
  {
    $this->stats = $stats;
  }
  /**
   * @return GoogleCloudApigeeV1OptimizedStatsNode
   */
  public function getStats()
  {
    return $this->stats;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1OptimizedStatsResponse::class, 'Google_Service_Apigee_GoogleCloudApigeeV1OptimizedStatsResponse');
