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

class ShuffleReadQuantileMetrics extends \Google\Model
{
  protected $fetchWaitTimeMillisType = Quantiles::class;
  protected $fetchWaitTimeMillisDataType = '';
  protected $localBlocksFetchedType = Quantiles::class;
  protected $localBlocksFetchedDataType = '';
  protected $readBytesType = Quantiles::class;
  protected $readBytesDataType = '';
  protected $readRecordsType = Quantiles::class;
  protected $readRecordsDataType = '';
  protected $remoteBlocksFetchedType = Quantiles::class;
  protected $remoteBlocksFetchedDataType = '';
  protected $remoteBytesReadType = Quantiles::class;
  protected $remoteBytesReadDataType = '';
  protected $remoteBytesReadToDiskType = Quantiles::class;
  protected $remoteBytesReadToDiskDataType = '';
  protected $remoteReqsDurationType = Quantiles::class;
  protected $remoteReqsDurationDataType = '';
  protected $shufflePushReadMetricsType = ShufflePushReadQuantileMetrics::class;
  protected $shufflePushReadMetricsDataType = '';
  protected $totalBlocksFetchedType = Quantiles::class;
  protected $totalBlocksFetchedDataType = '';

  /**
   * @param Quantiles $fetchWaitTimeMillis
   */
  public function setFetchWaitTimeMillis(Quantiles $fetchWaitTimeMillis)
  {
    $this->fetchWaitTimeMillis = $fetchWaitTimeMillis;
  }
  /**
   * @return Quantiles
   */
  public function getFetchWaitTimeMillis()
  {
    return $this->fetchWaitTimeMillis;
  }
  /**
   * @param Quantiles $localBlocksFetched
   */
  public function setLocalBlocksFetched(Quantiles $localBlocksFetched)
  {
    $this->localBlocksFetched = $localBlocksFetched;
  }
  /**
   * @return Quantiles
   */
  public function getLocalBlocksFetched()
  {
    return $this->localBlocksFetched;
  }
  /**
   * @param Quantiles $readBytes
   */
  public function setReadBytes(Quantiles $readBytes)
  {
    $this->readBytes = $readBytes;
  }
  /**
   * @return Quantiles
   */
  public function getReadBytes()
  {
    return $this->readBytes;
  }
  /**
   * @param Quantiles $readRecords
   */
  public function setReadRecords(Quantiles $readRecords)
  {
    $this->readRecords = $readRecords;
  }
  /**
   * @return Quantiles
   */
  public function getReadRecords()
  {
    return $this->readRecords;
  }
  /**
   * @param Quantiles $remoteBlocksFetched
   */
  public function setRemoteBlocksFetched(Quantiles $remoteBlocksFetched)
  {
    $this->remoteBlocksFetched = $remoteBlocksFetched;
  }
  /**
   * @return Quantiles
   */
  public function getRemoteBlocksFetched()
  {
    return $this->remoteBlocksFetched;
  }
  /**
   * @param Quantiles $remoteBytesRead
   */
  public function setRemoteBytesRead(Quantiles $remoteBytesRead)
  {
    $this->remoteBytesRead = $remoteBytesRead;
  }
  /**
   * @return Quantiles
   */
  public function getRemoteBytesRead()
  {
    return $this->remoteBytesRead;
  }
  /**
   * @param Quantiles $remoteBytesReadToDisk
   */
  public function setRemoteBytesReadToDisk(Quantiles $remoteBytesReadToDisk)
  {
    $this->remoteBytesReadToDisk = $remoteBytesReadToDisk;
  }
  /**
   * @return Quantiles
   */
  public function getRemoteBytesReadToDisk()
  {
    return $this->remoteBytesReadToDisk;
  }
  /**
   * @param Quantiles $remoteReqsDuration
   */
  public function setRemoteReqsDuration(Quantiles $remoteReqsDuration)
  {
    $this->remoteReqsDuration = $remoteReqsDuration;
  }
  /**
   * @return Quantiles
   */
  public function getRemoteReqsDuration()
  {
    return $this->remoteReqsDuration;
  }
  /**
   * @param ShufflePushReadQuantileMetrics $shufflePushReadMetrics
   */
  public function setShufflePushReadMetrics(ShufflePushReadQuantileMetrics $shufflePushReadMetrics)
  {
    $this->shufflePushReadMetrics = $shufflePushReadMetrics;
  }
  /**
   * @return ShufflePushReadQuantileMetrics
   */
  public function getShufflePushReadMetrics()
  {
    return $this->shufflePushReadMetrics;
  }
  /**
   * @param Quantiles $totalBlocksFetched
   */
  public function setTotalBlocksFetched(Quantiles $totalBlocksFetched)
  {
    $this->totalBlocksFetched = $totalBlocksFetched;
  }
  /**
   * @return Quantiles
   */
  public function getTotalBlocksFetched()
  {
    return $this->totalBlocksFetched;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ShuffleReadQuantileMetrics::class, 'Google_Service_Dataproc_ShuffleReadQuantileMetrics');
