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

class TaskMetrics extends \Google\Model
{
  /**
   * @var string
   */
  public $diskBytesSpilled;
  /**
   * @var string
   */
  public $executorCpuTimeNanos;
  /**
   * @var string
   */
  public $executorDeserializeCpuTimeNanos;
  /**
   * @var string
   */
  public $executorDeserializeTimeMillis;
  /**
   * @var string
   */
  public $executorRunTimeMillis;
  protected $inputMetricsType = InputMetrics::class;
  protected $inputMetricsDataType = '';
  /**
   * @var string
   */
  public $jvmGcTimeMillis;
  /**
   * @var string
   */
  public $memoryBytesSpilled;
  protected $outputMetricsType = OutputMetrics::class;
  protected $outputMetricsDataType = '';
  /**
   * @var string
   */
  public $peakExecutionMemoryBytes;
  /**
   * @var string
   */
  public $resultSerializationTimeMillis;
  /**
   * @var string
   */
  public $resultSize;
  protected $shuffleReadMetricsType = ShuffleReadMetrics::class;
  protected $shuffleReadMetricsDataType = '';
  protected $shuffleWriteMetricsType = ShuffleWriteMetrics::class;
  protected $shuffleWriteMetricsDataType = '';

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
   * @param string $executorCpuTimeNanos
   */
  public function setExecutorCpuTimeNanos($executorCpuTimeNanos)
  {
    $this->executorCpuTimeNanos = $executorCpuTimeNanos;
  }
  /**
   * @return string
   */
  public function getExecutorCpuTimeNanos()
  {
    return $this->executorCpuTimeNanos;
  }
  /**
   * @param string $executorDeserializeCpuTimeNanos
   */
  public function setExecutorDeserializeCpuTimeNanos($executorDeserializeCpuTimeNanos)
  {
    $this->executorDeserializeCpuTimeNanos = $executorDeserializeCpuTimeNanos;
  }
  /**
   * @return string
   */
  public function getExecutorDeserializeCpuTimeNanos()
  {
    return $this->executorDeserializeCpuTimeNanos;
  }
  /**
   * @param string $executorDeserializeTimeMillis
   */
  public function setExecutorDeserializeTimeMillis($executorDeserializeTimeMillis)
  {
    $this->executorDeserializeTimeMillis = $executorDeserializeTimeMillis;
  }
  /**
   * @return string
   */
  public function getExecutorDeserializeTimeMillis()
  {
    return $this->executorDeserializeTimeMillis;
  }
  /**
   * @param string $executorRunTimeMillis
   */
  public function setExecutorRunTimeMillis($executorRunTimeMillis)
  {
    $this->executorRunTimeMillis = $executorRunTimeMillis;
  }
  /**
   * @return string
   */
  public function getExecutorRunTimeMillis()
  {
    return $this->executorRunTimeMillis;
  }
  /**
   * @param InputMetrics $inputMetrics
   */
  public function setInputMetrics(InputMetrics $inputMetrics)
  {
    $this->inputMetrics = $inputMetrics;
  }
  /**
   * @return InputMetrics
   */
  public function getInputMetrics()
  {
    return $this->inputMetrics;
  }
  /**
   * @param string $jvmGcTimeMillis
   */
  public function setJvmGcTimeMillis($jvmGcTimeMillis)
  {
    $this->jvmGcTimeMillis = $jvmGcTimeMillis;
  }
  /**
   * @return string
   */
  public function getJvmGcTimeMillis()
  {
    return $this->jvmGcTimeMillis;
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
   * @param OutputMetrics $outputMetrics
   */
  public function setOutputMetrics(OutputMetrics $outputMetrics)
  {
    $this->outputMetrics = $outputMetrics;
  }
  /**
   * @return OutputMetrics
   */
  public function getOutputMetrics()
  {
    return $this->outputMetrics;
  }
  /**
   * @param string $peakExecutionMemoryBytes
   */
  public function setPeakExecutionMemoryBytes($peakExecutionMemoryBytes)
  {
    $this->peakExecutionMemoryBytes = $peakExecutionMemoryBytes;
  }
  /**
   * @return string
   */
  public function getPeakExecutionMemoryBytes()
  {
    return $this->peakExecutionMemoryBytes;
  }
  /**
   * @param string $resultSerializationTimeMillis
   */
  public function setResultSerializationTimeMillis($resultSerializationTimeMillis)
  {
    $this->resultSerializationTimeMillis = $resultSerializationTimeMillis;
  }
  /**
   * @return string
   */
  public function getResultSerializationTimeMillis()
  {
    return $this->resultSerializationTimeMillis;
  }
  /**
   * @param string $resultSize
   */
  public function setResultSize($resultSize)
  {
    $this->resultSize = $resultSize;
  }
  /**
   * @return string
   */
  public function getResultSize()
  {
    return $this->resultSize;
  }
  /**
   * @param ShuffleReadMetrics $shuffleReadMetrics
   */
  public function setShuffleReadMetrics(ShuffleReadMetrics $shuffleReadMetrics)
  {
    $this->shuffleReadMetrics = $shuffleReadMetrics;
  }
  /**
   * @return ShuffleReadMetrics
   */
  public function getShuffleReadMetrics()
  {
    return $this->shuffleReadMetrics;
  }
  /**
   * @param ShuffleWriteMetrics $shuffleWriteMetrics
   */
  public function setShuffleWriteMetrics(ShuffleWriteMetrics $shuffleWriteMetrics)
  {
    $this->shuffleWriteMetrics = $shuffleWriteMetrics;
  }
  /**
   * @return ShuffleWriteMetrics
   */
  public function getShuffleWriteMetrics()
  {
    return $this->shuffleWriteMetrics;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TaskMetrics::class, 'Google_Service_Dataproc_TaskMetrics');
