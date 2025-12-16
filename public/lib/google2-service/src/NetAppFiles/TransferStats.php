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

namespace Google\Service\NetAppFiles;

class TransferStats extends \Google\Model
{
  /**
   * Lag duration indicates the duration by which Destination region volume
   * content lags behind the primary region volume content.
   *
   * @var string
   */
  public $lagDuration;
  /**
   * Last transfer size in bytes.
   *
   * @var string
   */
  public $lastTransferBytes;
  /**
   * Time taken during last transfer.
   *
   * @var string
   */
  public $lastTransferDuration;
  /**
   * Time when last transfer completed.
   *
   * @var string
   */
  public $lastTransferEndTime;
  /**
   * A message describing the cause of the last transfer failure.
   *
   * @var string
   */
  public $lastTransferError;
  /**
   * Cumulative time taken across all transfers for the replication
   * relationship.
   *
   * @var string
   */
  public $totalTransferDuration;
  /**
   * Cumulative bytes transferred so far for the replication relationship.
   *
   * @var string
   */
  public $transferBytes;
  /**
   * Time when progress was updated last.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Lag duration indicates the duration by which Destination region volume
   * content lags behind the primary region volume content.
   *
   * @param string $lagDuration
   */
  public function setLagDuration($lagDuration)
  {
    $this->lagDuration = $lagDuration;
  }
  /**
   * @return string
   */
  public function getLagDuration()
  {
    return $this->lagDuration;
  }
  /**
   * Last transfer size in bytes.
   *
   * @param string $lastTransferBytes
   */
  public function setLastTransferBytes($lastTransferBytes)
  {
    $this->lastTransferBytes = $lastTransferBytes;
  }
  /**
   * @return string
   */
  public function getLastTransferBytes()
  {
    return $this->lastTransferBytes;
  }
  /**
   * Time taken during last transfer.
   *
   * @param string $lastTransferDuration
   */
  public function setLastTransferDuration($lastTransferDuration)
  {
    $this->lastTransferDuration = $lastTransferDuration;
  }
  /**
   * @return string
   */
  public function getLastTransferDuration()
  {
    return $this->lastTransferDuration;
  }
  /**
   * Time when last transfer completed.
   *
   * @param string $lastTransferEndTime
   */
  public function setLastTransferEndTime($lastTransferEndTime)
  {
    $this->lastTransferEndTime = $lastTransferEndTime;
  }
  /**
   * @return string
   */
  public function getLastTransferEndTime()
  {
    return $this->lastTransferEndTime;
  }
  /**
   * A message describing the cause of the last transfer failure.
   *
   * @param string $lastTransferError
   */
  public function setLastTransferError($lastTransferError)
  {
    $this->lastTransferError = $lastTransferError;
  }
  /**
   * @return string
   */
  public function getLastTransferError()
  {
    return $this->lastTransferError;
  }
  /**
   * Cumulative time taken across all transfers for the replication
   * relationship.
   *
   * @param string $totalTransferDuration
   */
  public function setTotalTransferDuration($totalTransferDuration)
  {
    $this->totalTransferDuration = $totalTransferDuration;
  }
  /**
   * @return string
   */
  public function getTotalTransferDuration()
  {
    return $this->totalTransferDuration;
  }
  /**
   * Cumulative bytes transferred so far for the replication relationship.
   *
   * @param string $transferBytes
   */
  public function setTransferBytes($transferBytes)
  {
    $this->transferBytes = $transferBytes;
  }
  /**
   * @return string
   */
  public function getTransferBytes()
  {
    return $this->transferBytes;
  }
  /**
   * Time when progress was updated last.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TransferStats::class, 'Google_Service_NetAppFiles_TransferStats');
