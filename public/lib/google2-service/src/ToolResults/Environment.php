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

namespace Google\Service\ToolResults;

class Environment extends \Google\Collection
{
  protected $collection_key = 'shardSummaries';
  protected $completionTimeType = Timestamp::class;
  protected $completionTimeDataType = '';
  protected $creationTimeType = Timestamp::class;
  protected $creationTimeDataType = '';
  protected $dimensionValueType = EnvironmentDimensionValueEntry::class;
  protected $dimensionValueDataType = 'array';
  /**
   * A short human-readable name to display in the UI. Maximum of 100
   * characters. For example: Nexus 5, API 27.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. An Environment id.
   *
   * @var string
   */
  public $environmentId;
  protected $environmentResultType = MergedResult::class;
  protected $environmentResultDataType = '';
  /**
   * Output only. An Execution id.
   *
   * @var string
   */
  public $executionId;
  /**
   * Output only. A History id.
   *
   * @var string
   */
  public $historyId;
  /**
   * Output only. A Project id.
   *
   * @var string
   */
  public $projectId;
  protected $resultsStorageType = ResultsStorage::class;
  protected $resultsStorageDataType = '';
  protected $shardSummariesType = ShardSummary::class;
  protected $shardSummariesDataType = 'array';

  /**
   * Output only. The time when the Environment status was set to complete. This
   * value will be set automatically when state transitions to COMPLETE.
   *
   * @param Timestamp $completionTime
   */
  public function setCompletionTime(Timestamp $completionTime)
  {
    $this->completionTime = $completionTime;
  }
  /**
   * @return Timestamp
   */
  public function getCompletionTime()
  {
    return $this->completionTime;
  }
  /**
   * Output only. The time when the Environment was created.
   *
   * @param Timestamp $creationTime
   */
  public function setCreationTime(Timestamp $creationTime)
  {
    $this->creationTime = $creationTime;
  }
  /**
   * @return Timestamp
   */
  public function getCreationTime()
  {
    return $this->creationTime;
  }
  /**
   * Dimension values describing the environment. Dimension values always
   * consist of "Model", "Version", "Locale", and "Orientation". - In response:
   * always set - In create request: always set - In update request: never set
   *
   * @param EnvironmentDimensionValueEntry[] $dimensionValue
   */
  public function setDimensionValue($dimensionValue)
  {
    $this->dimensionValue = $dimensionValue;
  }
  /**
   * @return EnvironmentDimensionValueEntry[]
   */
  public function getDimensionValue()
  {
    return $this->dimensionValue;
  }
  /**
   * A short human-readable name to display in the UI. Maximum of 100
   * characters. For example: Nexus 5, API 27.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. An Environment id.
   *
   * @param string $environmentId
   */
  public function setEnvironmentId($environmentId)
  {
    $this->environmentId = $environmentId;
  }
  /**
   * @return string
   */
  public function getEnvironmentId()
  {
    return $this->environmentId;
  }
  /**
   * Merged result of the environment.
   *
   * @param MergedResult $environmentResult
   */
  public function setEnvironmentResult(MergedResult $environmentResult)
  {
    $this->environmentResult = $environmentResult;
  }
  /**
   * @return MergedResult
   */
  public function getEnvironmentResult()
  {
    return $this->environmentResult;
  }
  /**
   * Output only. An Execution id.
   *
   * @param string $executionId
   */
  public function setExecutionId($executionId)
  {
    $this->executionId = $executionId;
  }
  /**
   * @return string
   */
  public function getExecutionId()
  {
    return $this->executionId;
  }
  /**
   * Output only. A History id.
   *
   * @param string $historyId
   */
  public function setHistoryId($historyId)
  {
    $this->historyId = $historyId;
  }
  /**
   * @return string
   */
  public function getHistoryId()
  {
    return $this->historyId;
  }
  /**
   * Output only. A Project id.
   *
   * @param string $projectId
   */
  public function setProjectId($projectId)
  {
    $this->projectId = $projectId;
  }
  /**
   * @return string
   */
  public function getProjectId()
  {
    return $this->projectId;
  }
  /**
   * The location where output files are stored in the user bucket.
   *
   * @param ResultsStorage $resultsStorage
   */
  public function setResultsStorage(ResultsStorage $resultsStorage)
  {
    $this->resultsStorage = $resultsStorage;
  }
  /**
   * @return ResultsStorage
   */
  public function getResultsStorage()
  {
    return $this->resultsStorage;
  }
  /**
   * Output only. Summaries of shards. Only one shard will present unless
   * sharding feature is enabled in TestExecutionService.
   *
   * @param ShardSummary[] $shardSummaries
   */
  public function setShardSummaries($shardSummaries)
  {
    $this->shardSummaries = $shardSummaries;
  }
  /**
   * @return ShardSummary[]
   */
  public function getShardSummaries()
  {
    return $this->shardSummaries;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Environment::class, 'Google_Service_ToolResults_Environment');
