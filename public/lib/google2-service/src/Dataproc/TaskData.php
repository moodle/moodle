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

namespace Google\Service\Dataproc;

class TaskData extends \Google\Collection
{
  protected $collection_key = 'accumulatorUpdates';
  protected $accumulatorUpdatesType = AccumulableInfo::class;
  protected $accumulatorUpdatesDataType = 'array';
  /**
   * @var int
   */
  public $attempt;
  /**
   * @var string
   */
  public $durationMillis;
  /**
   * @var string
   */
  public $errorMessage;
  /**
   * @var string
   */
  public $executorId;
  /**
   * @var string[]
   */
  public $executorLogs;
  /**
   * @var string
   */
  public $gettingResultTimeMillis;
  /**
   * @var bool
   */
  public $hasMetrics;
  /**
   * @var string
   */
  public $host;
  /**
   * @var int
   */
  public $index;
  /**
   * @var string
   */
  public $launchTime;
  /**
   * @var int
   */
  public $partitionId;
  /**
   * @var string
   */
  public $resultFetchStart;
  /**
   * @var string
   */
  public $schedulerDelayMillis;
  /**
   * @var bool
   */
  public $speculative;
  /**
   * @var int
   */
  public $stageAttemptId;
  /**
   * @var string
   */
  public $stageId;
  /**
   * @var string
   */
  public $status;
  /**
   * @var string
   */
  public $taskId;
  /**
   * @var string
   */
  public $taskLocality;
  protected $taskMetricsType = TaskMetrics::class;
  protected $taskMetricsDataType = '';

  /**
   * @param AccumulableInfo[] $accumulatorUpdates
   */
  public function setAccumulatorUpdates($accumulatorUpdates)
  {
    $this->accumulatorUpdates = $accumulatorUpdates;
  }
  /**
   * @return AccumulableInfo[]
   */
  public function getAccumulatorUpdates()
  {
    return $this->accumulatorUpdates;
  }
  /**
   * @param int $attempt
   */
  public function setAttempt($attempt)
  {
    $this->attempt = $attempt;
  }
  /**
   * @return int
   */
  public function getAttempt()
  {
    return $this->attempt;
  }
  /**
   * @param string $durationMillis
   */
  public function setDurationMillis($durationMillis)
  {
    $this->durationMillis = $durationMillis;
  }
  /**
   * @return string
   */
  public function getDurationMillis()
  {
    return $this->durationMillis;
  }
  /**
   * @param string $errorMessage
   */
  public function setErrorMessage($errorMessage)
  {
    $this->errorMessage = $errorMessage;
  }
  /**
   * @return string
   */
  public function getErrorMessage()
  {
    return $this->errorMessage;
  }
  /**
   * @param string $executorId
   */
  public function setExecutorId($executorId)
  {
    $this->executorId = $executorId;
  }
  /**
   * @return string
   */
  public function getExecutorId()
  {
    return $this->executorId;
  }
  /**
   * @param string[] $executorLogs
   */
  public function setExecutorLogs($executorLogs)
  {
    $this->executorLogs = $executorLogs;
  }
  /**
   * @return string[]
   */
  public function getExecutorLogs()
  {
    return $this->executorLogs;
  }
  /**
   * @param string $gettingResultTimeMillis
   */
  public function setGettingResultTimeMillis($gettingResultTimeMillis)
  {
    $this->gettingResultTimeMillis = $gettingResultTimeMillis;
  }
  /**
   * @return string
   */
  public function getGettingResultTimeMillis()
  {
    return $this->gettingResultTimeMillis;
  }
  /**
   * @param bool $hasMetrics
   */
  public function setHasMetrics($hasMetrics)
  {
    $this->hasMetrics = $hasMetrics;
  }
  /**
   * @return bool
   */
  public function getHasMetrics()
  {
    return $this->hasMetrics;
  }
  /**
   * @param string $host
   */
  public function setHost($host)
  {
    $this->host = $host;
  }
  /**
   * @return string
   */
  public function getHost()
  {
    return $this->host;
  }
  /**
   * @param int $index
   */
  public function setIndex($index)
  {
    $this->index = $index;
  }
  /**
   * @return int
   */
  public function getIndex()
  {
    return $this->index;
  }
  /**
   * @param string $launchTime
   */
  public function setLaunchTime($launchTime)
  {
    $this->launchTime = $launchTime;
  }
  /**
   * @return string
   */
  public function getLaunchTime()
  {
    return $this->launchTime;
  }
  /**
   * @param int $partitionId
   */
  public function setPartitionId($partitionId)
  {
    $this->partitionId = $partitionId;
  }
  /**
   * @return int
   */
  public function getPartitionId()
  {
    return $this->partitionId;
  }
  /**
   * @param string $resultFetchStart
   */
  public function setResultFetchStart($resultFetchStart)
  {
    $this->resultFetchStart = $resultFetchStart;
  }
  /**
   * @return string
   */
  public function getResultFetchStart()
  {
    return $this->resultFetchStart;
  }
  /**
   * @param string $schedulerDelayMillis
   */
  public function setSchedulerDelayMillis($schedulerDelayMillis)
  {
    $this->schedulerDelayMillis = $schedulerDelayMillis;
  }
  /**
   * @return string
   */
  public function getSchedulerDelayMillis()
  {
    return $this->schedulerDelayMillis;
  }
  /**
   * @param bool $speculative
   */
  public function setSpeculative($speculative)
  {
    $this->speculative = $speculative;
  }
  /**
   * @return bool
   */
  public function getSpeculative()
  {
    return $this->speculative;
  }
  /**
   * @param int $stageAttemptId
   */
  public function setStageAttemptId($stageAttemptId)
  {
    $this->stageAttemptId = $stageAttemptId;
  }
  /**
   * @return int
   */
  public function getStageAttemptId()
  {
    return $this->stageAttemptId;
  }
  /**
   * @param string $stageId
   */
  public function setStageId($stageId)
  {
    $this->stageId = $stageId;
  }
  /**
   * @return string
   */
  public function getStageId()
  {
    return $this->stageId;
  }
  /**
   * @param string $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return string
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * @param string $taskId
   */
  public function setTaskId($taskId)
  {
    $this->taskId = $taskId;
  }
  /**
   * @return string
   */
  public function getTaskId()
  {
    return $this->taskId;
  }
  /**
   * @param string $taskLocality
   */
  public function setTaskLocality($taskLocality)
  {
    $this->taskLocality = $taskLocality;
  }
  /**
   * @return string
   */
  public function getTaskLocality()
  {
    return $this->taskLocality;
  }
  /**
   * @param TaskMetrics $taskMetrics
   */
  public function setTaskMetrics(TaskMetrics $taskMetrics)
  {
    $this->taskMetrics = $taskMetrics;
  }
  /**
   * @return TaskMetrics
   */
  public function getTaskMetrics()
  {
    return $this->taskMetrics;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TaskData::class, 'Google_Service_Dataproc_TaskData');
