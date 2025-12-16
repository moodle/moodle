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

class TaskQuantileMetrics extends \Google\Model
{
  protected $diskBytesSpilledType = Quantiles::class;
  protected $diskBytesSpilledDataType = '';
  protected $durationMillisType = Quantiles::class;
  protected $durationMillisDataType = '';
  protected $executorCpuTimeNanosType = Quantiles::class;
  protected $executorCpuTimeNanosDataType = '';
  protected $executorDeserializeCpuTimeNanosType = Quantiles::class;
  protected $executorDeserializeCpuTimeNanosDataType = '';
  protected $executorDeserializeTimeMillisType = Quantiles::class;
  protected $executorDeserializeTimeMillisDataType = '';
  protected $executorRunTimeMillisType = Quantiles::class;
  protected $executorRunTimeMillisDataType = '';
  protected $gettingResultTimeMillisType = Quantiles::class;
  protected $gettingResultTimeMillisDataType = '';
  protected $inputMetricsType = InputQuantileMetrics::class;
  protected $inputMetricsDataType = '';
  protected $jvmGcTimeMillisType = Quantiles::class;
  protected $jvmGcTimeMillisDataType = '';
  protected $memoryBytesSpilledType = Quantiles::class;
  protected $memoryBytesSpilledDataType = '';
  protected $outputMetricsType = OutputQuantileMetrics::class;
  protected $outputMetricsDataType = '';
  protected $peakExecutionMemoryBytesType = Quantiles::class;
  protected $peakExecutionMemoryBytesDataType = '';
  protected $resultSerializationTimeMillisType = Quantiles::class;
  protected $resultSerializationTimeMillisDataType = '';
  protected $resultSizeType = Quantiles::class;
  protected $resultSizeDataType = '';
  protected $schedulerDelayMillisType = Quantiles::class;
  protected $schedulerDelayMillisDataType = '';
  protected $shuffleReadMetricsType = ShuffleReadQuantileMetrics::class;
  protected $shuffleReadMetricsDataType = '';
  protected $shuffleWriteMetricsType = ShuffleWriteQuantileMetrics::class;
  protected $shuffleWriteMetricsDataType = '';

  /**
   * @param Quantiles $diskBytesSpilled
   */
  public function setDiskBytesSpilled(Quantiles $diskBytesSpilled)
  {
    $this->diskBytesSpilled = $diskBytesSpilled;
  }
  /**
   * @return Quantiles
   */
  public function getDiskBytesSpilled()
  {
    return $this->diskBytesSpilled;
  }
  /**
   * @param Quantiles $durationMillis
   */
  public function setDurationMillis(Quantiles $durationMillis)
  {
    $this->durationMillis = $durationMillis;
  }
  /**
   * @return Quantiles
   */
  public function getDurationMillis()
  {
    return $this->durationMillis;
  }
  /**
   * @param Quantiles $executorCpuTimeNanos
   */
  public function setExecutorCpuTimeNanos(Quantiles $executorCpuTimeNanos)
  {
    $this->executorCpuTimeNanos = $executorCpuTimeNanos;
  }
  /**
   * @return Quantiles
   */
  public function getExecutorCpuTimeNanos()
  {
    return $this->executorCpuTimeNanos;
  }
  /**
   * @param Quantiles $executorDeserializeCpuTimeNanos
   */
  public function setExecutorDeserializeCpuTimeNanos(Quantiles $executorDeserializeCpuTimeNanos)
  {
    $this->executorDeserializeCpuTimeNanos = $executorDeserializeCpuTimeNanos;
  }
  /**
   * @return Quantiles
   */
  public function getExecutorDeserializeCpuTimeNanos()
  {
    return $this->executorDeserializeCpuTimeNanos;
  }
  /**
   * @param Quantiles $executorDeserializeTimeMillis
   */
  public function setExecutorDeserializeTimeMillis(Quantiles $executorDeserializeTimeMillis)
  {
    $this->executorDeserializeTimeMillis = $executorDeserializeTimeMillis;
  }
  /**
   * @return Quantiles
   */
  public function getExecutorDeserializeTimeMillis()
  {
    return $this->executorDeserializeTimeMillis;
  }
  /**
   * @param Quantiles $executorRunTimeMillis
   */
  public function setExecutorRunTimeMillis(Quantiles $executorRunTimeMillis)
  {
    $this->executorRunTimeMillis = $executorRunTimeMillis;
  }
  /**
   * @return Quantiles
   */
  public function getExecutorRunTimeMillis()
  {
    return $this->executorRunTimeMillis;
  }
  /**
   * @param Quantiles $gettingResultTimeMillis
   */
  public function setGettingResultTimeMillis(Quantiles $gettingResultTimeMillis)
  {
    $this->gettingResultTimeMillis = $gettingResultTimeMillis;
  }
  /**
   * @return Quantiles
   */
  public function getGettingResultTimeMillis()
  {
    return $this->gettingResultTimeMillis;
  }
  /**
   * @param InputQuantileMetrics $inputMetrics
   */
  public function setInputMetrics(InputQuantileMetrics $inputMetrics)
  {
    $this->inputMetrics = $inputMetrics;
  }
  /**
   * @return InputQuantileMetrics
   */
  public function getInputMetrics()
  {
    return $this->inputMetrics;
  }
  /**
   * @param Quantiles $jvmGcTimeMillis
   */
  public function setJvmGcTimeMillis(Quantiles $jvmGcTimeMillis)
  {
    $this->jvmGcTimeMillis = $jvmGcTimeMillis;
  }
  /**
   * @return Quantiles
   */
  public function getJvmGcTimeMillis()
  {
    return $this->jvmGcTimeMillis;
  }
  /**
   * @param Quantiles $memoryBytesSpilled
   */
  public function setMemoryBytesSpilled(Quantiles $memoryBytesSpilled)
  {
    $this->memoryBytesSpilled = $memoryBytesSpilled;
  }
  /**
   * @return Quantiles
   */
  public function getMemoryBytesSpilled()
  {
    return $this->memoryBytesSpilled;
  }
  /**
   * @param OutputQuantileMetrics $outputMetrics
   */
  public function setOutputMetrics(OutputQuantileMetrics $outputMetrics)
  {
    $this->outputMetrics = $outputMetrics;
  }
  /**
   * @return OutputQuantileMetrics
   */
  public function getOutputMetrics()
  {
    return $this->outputMetrics;
  }
  /**
   * @param Quantiles $peakExecutionMemoryBytes
   */
  public function setPeakExecutionMemoryBytes(Quantiles $peakExecutionMemoryBytes)
  {
    $this->peakExecutionMemoryBytes = $peakExecutionMemoryBytes;
  }
  /**
   * @return Quantiles
   */
  public function getPeakExecutionMemoryBytes()
  {
    return $this->peakExecutionMemoryBytes;
  }
  /**
   * @param Quantiles $resultSerializationTimeMillis
   */
  public function setResultSerializationTimeMillis(Quantiles $resultSerializationTimeMillis)
  {
    $this->resultSerializationTimeMillis = $resultSerializationTimeMillis;
  }
  /**
   * @return Quantiles
   */
  public function getResultSerializationTimeMillis()
  {
    return $this->resultSerializationTimeMillis;
  }
  /**
   * @param Quantiles $resultSize
   */
  public function setResultSize(Quantiles $resultSize)
  {
    $this->resultSize = $resultSize;
  }
  /**
   * @return Quantiles
   */
  public function getResultSize()
  {
    return $this->resultSize;
  }
  /**
   * @param Quantiles $schedulerDelayMillis
   */
  public function setSchedulerDelayMillis(Quantiles $schedulerDelayMillis)
  {
    $this->schedulerDelayMillis = $schedulerDelayMillis;
  }
  /**
   * @return Quantiles
   */
  public function getSchedulerDelayMillis()
  {
    return $this->schedulerDelayMillis;
  }
  /**
   * @param ShuffleReadQuantileMetrics $shuffleReadMetrics
   */
  public function setShuffleReadMetrics(ShuffleReadQuantileMetrics $shuffleReadMetrics)
  {
    $this->shuffleReadMetrics = $shuffleReadMetrics;
  }
  /**
   * @return ShuffleReadQuantileMetrics
   */
  public function getShuffleReadMetrics()
  {
    return $this->shuffleReadMetrics;
  }
  /**
   * @param ShuffleWriteQuantileMetrics $shuffleWriteMetrics
   */
  public function setShuffleWriteMetrics(ShuffleWriteQuantileMetrics $shuffleWriteMetrics)
  {
    $this->shuffleWriteMetrics = $shuffleWriteMetrics;
  }
  /**
   * @return ShuffleWriteQuantileMetrics
   */
  public function getShuffleWriteMetrics()
  {
    return $this->shuffleWriteMetrics;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TaskQuantileMetrics::class, 'Google_Service_Dataproc_TaskQuantileMetrics');
