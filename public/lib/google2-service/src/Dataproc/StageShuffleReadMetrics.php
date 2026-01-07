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

class StageShuffleReadMetrics extends \Google\Model
{
  /**
   * @var string
   */
  public $bytesRead;
  /**
   * @var string
   */
  public $fetchWaitTimeMillis;
  /**
   * @var string
   */
  public $localBlocksFetched;
  /**
   * @var string
   */
  public $localBytesRead;
  /**
   * @var string
   */
  public $recordsRead;
  /**
   * @var string
   */
  public $remoteBlocksFetched;
  /**
   * @var string
   */
  public $remoteBytesRead;
  /**
   * @var string
   */
  public $remoteBytesReadToDisk;
  /**
   * @var string
   */
  public $remoteReqsDuration;
  protected $stageShufflePushReadMetricsType = StageShufflePushReadMetrics::class;
  protected $stageShufflePushReadMetricsDataType = '';

  /**
   * @param string $bytesRead
   */
  public function setBytesRead($bytesRead)
  {
    $this->bytesRead = $bytesRead;
  }
  /**
   * @return string
   */
  public function getBytesRead()
  {
    return $this->bytesRead;
  }
  /**
   * @param string $fetchWaitTimeMillis
   */
  public function setFetchWaitTimeMillis($fetchWaitTimeMillis)
  {
    $this->fetchWaitTimeMillis = $fetchWaitTimeMillis;
  }
  /**
   * @return string
   */
  public function getFetchWaitTimeMillis()
  {
    return $this->fetchWaitTimeMillis;
  }
  /**
   * @param string $localBlocksFetched
   */
  public function setLocalBlocksFetched($localBlocksFetched)
  {
    $this->localBlocksFetched = $localBlocksFetched;
  }
  /**
   * @return string
   */
  public function getLocalBlocksFetched()
  {
    return $this->localBlocksFetched;
  }
  /**
   * @param string $localBytesRead
   */
  public function setLocalBytesRead($localBytesRead)
  {
    $this->localBytesRead = $localBytesRead;
  }
  /**
   * @return string
   */
  public function getLocalBytesRead()
  {
    return $this->localBytesRead;
  }
  /**
   * @param string $recordsRead
   */
  public function setRecordsRead($recordsRead)
  {
    $this->recordsRead = $recordsRead;
  }
  /**
   * @return string
   */
  public function getRecordsRead()
  {
    return $this->recordsRead;
  }
  /**
   * @param string $remoteBlocksFetched
   */
  public function setRemoteBlocksFetched($remoteBlocksFetched)
  {
    $this->remoteBlocksFetched = $remoteBlocksFetched;
  }
  /**
   * @return string
   */
  public function getRemoteBlocksFetched()
  {
    return $this->remoteBlocksFetched;
  }
  /**
   * @param string $remoteBytesRead
   */
  public function setRemoteBytesRead($remoteBytesRead)
  {
    $this->remoteBytesRead = $remoteBytesRead;
  }
  /**
   * @return string
   */
  public function getRemoteBytesRead()
  {
    return $this->remoteBytesRead;
  }
  /**
   * @param string $remoteBytesReadToDisk
   */
  public function setRemoteBytesReadToDisk($remoteBytesReadToDisk)
  {
    $this->remoteBytesReadToDisk = $remoteBytesReadToDisk;
  }
  /**
   * @return string
   */
  public function getRemoteBytesReadToDisk()
  {
    return $this->remoteBytesReadToDisk;
  }
  /**
   * @param string $remoteReqsDuration
   */
  public function setRemoteReqsDuration($remoteReqsDuration)
  {
    $this->remoteReqsDuration = $remoteReqsDuration;
  }
  /**
   * @return string
   */
  public function getRemoteReqsDuration()
  {
    return $this->remoteReqsDuration;
  }
  /**
   * @param StageShufflePushReadMetrics $stageShufflePushReadMetrics
   */
  public function setStageShufflePushReadMetrics(StageShufflePushReadMetrics $stageShufflePushReadMetrics)
  {
    $this->stageShufflePushReadMetrics = $stageShufflePushReadMetrics;
  }
  /**
   * @return StageShufflePushReadMetrics
   */
  public function getStageShufflePushReadMetrics()
  {
    return $this->stageShufflePushReadMetrics;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StageShuffleReadMetrics::class, 'Google_Service_Dataproc_StageShuffleReadMetrics');
