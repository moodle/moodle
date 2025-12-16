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

class ExecutorMetricsDistributions extends \Google\Collection
{
  protected $collection_key = 'taskTimeMillis';
  public $diskBytesSpilled;
  public $failedTasks;
  public $inputBytes;
  public $inputRecords;
  public $killedTasks;
  public $memoryBytesSpilled;
  public $outputBytes;
  public $outputRecords;
  protected $peakMemoryMetricsType = ExecutorPeakMetricsDistributions::class;
  protected $peakMemoryMetricsDataType = '';
  public $quantiles;
  public $shuffleRead;
  public $shuffleReadRecords;
  public $shuffleWrite;
  public $shuffleWriteRecords;
  public $succeededTasks;
  public $taskTimeMillis;

  public function setDiskBytesSpilled($diskBytesSpilled)
  {
    $this->diskBytesSpilled = $diskBytesSpilled;
  }
  public function getDiskBytesSpilled()
  {
    return $this->diskBytesSpilled;
  }
  public function setFailedTasks($failedTasks)
  {
    $this->failedTasks = $failedTasks;
  }
  public function getFailedTasks()
  {
    return $this->failedTasks;
  }
  public function setInputBytes($inputBytes)
  {
    $this->inputBytes = $inputBytes;
  }
  public function getInputBytes()
  {
    return $this->inputBytes;
  }
  public function setInputRecords($inputRecords)
  {
    $this->inputRecords = $inputRecords;
  }
  public function getInputRecords()
  {
    return $this->inputRecords;
  }
  public function setKilledTasks($killedTasks)
  {
    $this->killedTasks = $killedTasks;
  }
  public function getKilledTasks()
  {
    return $this->killedTasks;
  }
  public function setMemoryBytesSpilled($memoryBytesSpilled)
  {
    $this->memoryBytesSpilled = $memoryBytesSpilled;
  }
  public function getMemoryBytesSpilled()
  {
    return $this->memoryBytesSpilled;
  }
  public function setOutputBytes($outputBytes)
  {
    $this->outputBytes = $outputBytes;
  }
  public function getOutputBytes()
  {
    return $this->outputBytes;
  }
  public function setOutputRecords($outputRecords)
  {
    $this->outputRecords = $outputRecords;
  }
  public function getOutputRecords()
  {
    return $this->outputRecords;
  }
  /**
   * @param ExecutorPeakMetricsDistributions $peakMemoryMetrics
   */
  public function setPeakMemoryMetrics(ExecutorPeakMetricsDistributions $peakMemoryMetrics)
  {
    $this->peakMemoryMetrics = $peakMemoryMetrics;
  }
  /**
   * @return ExecutorPeakMetricsDistributions
   */
  public function getPeakMemoryMetrics()
  {
    return $this->peakMemoryMetrics;
  }
  public function setQuantiles($quantiles)
  {
    $this->quantiles = $quantiles;
  }
  public function getQuantiles()
  {
    return $this->quantiles;
  }
  public function setShuffleRead($shuffleRead)
  {
    $this->shuffleRead = $shuffleRead;
  }
  public function getShuffleRead()
  {
    return $this->shuffleRead;
  }
  public function setShuffleReadRecords($shuffleReadRecords)
  {
    $this->shuffleReadRecords = $shuffleReadRecords;
  }
  public function getShuffleReadRecords()
  {
    return $this->shuffleReadRecords;
  }
  public function setShuffleWrite($shuffleWrite)
  {
    $this->shuffleWrite = $shuffleWrite;
  }
  public function getShuffleWrite()
  {
    return $this->shuffleWrite;
  }
  public function setShuffleWriteRecords($shuffleWriteRecords)
  {
    $this->shuffleWriteRecords = $shuffleWriteRecords;
  }
  public function getShuffleWriteRecords()
  {
    return $this->shuffleWriteRecords;
  }
  public function setSucceededTasks($succeededTasks)
  {
    $this->succeededTasks = $succeededTasks;
  }
  public function getSucceededTasks()
  {
    return $this->succeededTasks;
  }
  public function setTaskTimeMillis($taskTimeMillis)
  {
    $this->taskTimeMillis = $taskTimeMillis;
  }
  public function getTaskTimeMillis()
  {
    return $this->taskTimeMillis;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExecutorMetricsDistributions::class, 'Google_Service_Dataproc_ExecutorMetricsDistributions');
