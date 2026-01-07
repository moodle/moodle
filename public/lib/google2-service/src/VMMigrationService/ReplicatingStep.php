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

namespace Google\Service\VMMigrationService;

class ReplicatingStep extends \Google\Model
{
  /**
   * The source disks replication rate for the last 30 minutes in bytes per
   * second.
   *
   * @var string
   */
  public $lastThirtyMinutesAverageBytesPerSecond;
  /**
   * The source disks replication rate for the last 2 minutes in bytes per
   * second.
   *
   * @var string
   */
  public $lastTwoMinutesAverageBytesPerSecond;
  /**
   * Replicated bytes in the step.
   *
   * @var string
   */
  public $replicatedBytes;
  /**
   * Total bytes to be handled in the step.
   *
   * @var string
   */
  public $totalBytes;

  /**
   * The source disks replication rate for the last 30 minutes in bytes per
   * second.
   *
   * @param string $lastThirtyMinutesAverageBytesPerSecond
   */
  public function setLastThirtyMinutesAverageBytesPerSecond($lastThirtyMinutesAverageBytesPerSecond)
  {
    $this->lastThirtyMinutesAverageBytesPerSecond = $lastThirtyMinutesAverageBytesPerSecond;
  }
  /**
   * @return string
   */
  public function getLastThirtyMinutesAverageBytesPerSecond()
  {
    return $this->lastThirtyMinutesAverageBytesPerSecond;
  }
  /**
   * The source disks replication rate for the last 2 minutes in bytes per
   * second.
   *
   * @param string $lastTwoMinutesAverageBytesPerSecond
   */
  public function setLastTwoMinutesAverageBytesPerSecond($lastTwoMinutesAverageBytesPerSecond)
  {
    $this->lastTwoMinutesAverageBytesPerSecond = $lastTwoMinutesAverageBytesPerSecond;
  }
  /**
   * @return string
   */
  public function getLastTwoMinutesAverageBytesPerSecond()
  {
    return $this->lastTwoMinutesAverageBytesPerSecond;
  }
  /**
   * Replicated bytes in the step.
   *
   * @param string $replicatedBytes
   */
  public function setReplicatedBytes($replicatedBytes)
  {
    $this->replicatedBytes = $replicatedBytes;
  }
  /**
   * @return string
   */
  public function getReplicatedBytes()
  {
    return $this->replicatedBytes;
  }
  /**
   * Total bytes to be handled in the step.
   *
   * @param string $totalBytes
   */
  public function setTotalBytes($totalBytes)
  {
    $this->totalBytes = $totalBytes;
  }
  /**
   * @return string
   */
  public function getTotalBytes()
  {
    return $this->totalBytes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReplicatingStep::class, 'Google_Service_VMMigrationService_ReplicatingStep');
