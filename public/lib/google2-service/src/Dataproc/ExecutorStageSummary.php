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

class ExecutorStageSummary extends \Google\Model
{
  /**
   * @var string
   */
  public $diskBytesSpilled;
  /**
   * @var string
   */
  public $executorId;
  /**
   * @var int
   */
  public $failedTasks;
  /**
   * @var string
   */
  public $inputBytes;
  /**
   * @var string
   */
  public $inputRecords;
  /**
   * @var bool
   */
  public $isExcludedForStage;
  /**
   * @var int
   */
  public $killedTasks;
  /**
   * @var string
   */
  public $memoryBytesSpilled;
  /**
   * @var string
   */
  public $outputBytes;
  /**
   * @var string
   */
  public $outputRecords;
  protected $peakMemoryMetricsType = ExecutorMetrics::class;
  protected $peakMemoryMetricsDataType = '';
  /**
   * @var string
   */
  public $shuffleRead;
  /**
   * @var string
   */
  public $shuffleReadRecords;
  /**
   * @var string
   */
  public $shuffleWrite;
  /**
   * @var string
   */
  public $shuffleWriteRecords;
  /**
   * @var int
   */
  public $stageAttemptId;
  /**
   * @var string
   */
  public $stageId;
  /**
   * @var int
   */
  public $succeededTasks;
  /**
   * @var string
   */
  public $taskTimeMillis;

  /**
   * @param string $diskBytesSpilled
   */
  public function setDiskBytesSpilled($diskBytesSpilled)
  {
    $this->diskBytesSpilled = $diskBytesSpilled;
  }
  /**
   * @return string
   */
  public function getDiskBytesSpilled()
  {
    return $this->diskBytesSpilled;
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
   * @param int $failedTasks
   */
  public function setFailedTasks($failedTasks)
  {
    $this->failedTasks = $failedTasks;
  }
  /**
   * @return int
   */
  public function getFailedTasks()
  {
    return $this->failedTasks;
  }
  /**
   * @param string $inputBytes
   */
  public function setInputBytes($inputBytes)
  {
    $this->inputBytes = $inputBytes;
  }
  /**
   * @return string
   */
  public function getInputBytes()
  {
    return $this->inputBytes;
  }
  /**
   * @param string $inputRecords
   */
  public function setInputRecords($inputRecords)
  {
    $this->inputRecords = $inputRecords;
  }
  /**
   * @return string
   */
  public function getInputRecords()
  {
    return $this->inputRecords;
  }
  /**
   * @param bool $isExcludedForStage
   */
  public function setIsExcludedForStage($isExcludedForStage)
  {
    $this->isExcludedForStage = $isExcludedForStage;
  }
  /**
   * @return bool
   */
  public function getIsExcludedForStage()
  {
    return $this->isExcludedForStage;
  }
  /**
   * @param int $killedTasks
   */
  public function setKilledTasks($killedTasks)
  {
    $this->killedTasks = $killedTasks;
  }
  /**
   * @return int
   */
  public function getKilledTasks()
  {
    return $this->killedTasks;
  }
  /**
   * @param string $memoryBytesSpilled
   */
  public function setMemoryBytesSpilled($memoryBytesSpilled)
  {
    $this->memoryBytesSpilled = $memoryBytesSpilled;
  }
  /**
   * @return string
   */
  public function getMemoryBytesSpilled()
  {
    return $this->memoryBytesSpilled;
  }
  /**
   * @param string $outputBytes
   */
  public function setOutputBytes($outputBytes)
  {
    $this->outputBytes = $outputBytes;
  }
  /**
   * @return string
   */
  public function getOutputBytes()
  {
    return $this->outputBytes;
  }
  /**
   * @param string $outputRecords
   */
  public function setOutputRecords($outputRecords)
  {
    $this->outputRecords = $outputRecords;
  }
  /**
   * @return string
   */
  public function getOutputRecords()
  {
    return $this->outputRecords;
  }
  /**
   * @param ExecutorMetrics $peakMemoryMetrics
   */
  public function setPeakMemoryMetrics(ExecutorMetrics $peakMemoryMetrics)
  {
    $this->peakMemoryMetrics = $peakMemoryMetrics;
  }
  /**
   * @return ExecutorMetrics
   */
  public function getPeakMemoryMetrics()
  {
    return $this->peakMemoryMetrics;
  }
  /**
   * @param string $shuffleRead
   */
  public function setShuffleRead($shuffleRead)
  {
    $this->shuffleRead = $shuffleRead;
  }
  /**
   * @return string
   */
  public function getShuffleRead()
  {
    return $this->shuffleRead;
  }
  /**
   * @param string $shuffleReadRecords
   */
  public function setShuffleReadRecords($shuffleReadRecords)
  {
    $this->shuffleReadRecords = $shuffleReadRecords;
  }
  /**
   * @return string
   */
  public function getShuffleReadRecords()
  {
    return $this->shuffleReadRecords;
  }
  /**
   * @param string $shuffleWrite
   */
  public function setShuffleWrite($shuffleWrite)
  {
    $this->shuffleWrite = $shuffleWrite;
  }
  /**
   * @return string
   */
  public function getShuffleWrite()
  {
    return $this->shuffleWrite;
  }
  /**
   * @param string $shuffleWriteRecords
   */
  public function setShuffleWriteRecords($shuffleWriteRecords)
  {
    $this->shuffleWriteRecords = $shuffleWriteRecords;
  }
  /**
   * @return string
   */
  public function getShuffleWriteRecords()
  {
    return $this->shuffleWriteRecords;
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
   * @param int $succeededTasks
   */
  public function setSucceededTasks($succeededTasks)
  {
    $this->succeededTasks = $succeededTasks;
  }
  /**
   * @return int
   */
  public function getSucceededTasks()
  {
    return $this->succeededTasks;
  }
  /**
   * @param string $taskTimeMillis
   */
  public function setTaskTimeMillis($taskTimeMillis)
  {
    $this->taskTimeMillis = $taskTimeMillis;
  }
  /**
   * @return string
   */
  public function getTaskTimeMillis()
  {
    return $this->taskTimeMillis;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExecutorStageSummary::class, 'Google_Service_Dataproc_ExecutorStageSummary');
