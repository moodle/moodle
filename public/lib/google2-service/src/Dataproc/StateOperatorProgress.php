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

class StateOperatorProgress extends \Google\Model
{
  /**
   * @var string
   */
  public $allRemovalsTimeMs;
  /**
   * @var string
   */
  public $allUpdatesTimeMs;
  /**
   * @var string
   */
  public $commitTimeMs;
  /**
   * @var string[]
   */
  public $customMetrics;
  /**
   * @var string
   */
  public $memoryUsedBytes;
  /**
   * @var string
   */
  public $numRowsDroppedByWatermark;
  /**
   * @var string
   */
  public $numRowsRemoved;
  /**
   * @var string
   */
  public $numRowsTotal;
  /**
   * @var string
   */
  public $numRowsUpdated;
  /**
   * @var string
   */
  public $numShufflePartitions;
  /**
   * @var string
   */
  public $numStateStoreInstances;
  /**
   * @var string
   */
  public $operatorName;

  /**
   * @param string $allRemovalsTimeMs
   */
  public function setAllRemovalsTimeMs($allRemovalsTimeMs)
  {
    $this->allRemovalsTimeMs = $allRemovalsTimeMs;
  }
  /**
   * @return string
   */
  public function getAllRemovalsTimeMs()
  {
    return $this->allRemovalsTimeMs;
  }
  /**
   * @param string $allUpdatesTimeMs
   */
  public function setAllUpdatesTimeMs($allUpdatesTimeMs)
  {
    $this->allUpdatesTimeMs = $allUpdatesTimeMs;
  }
  /**
   * @return string
   */
  public function getAllUpdatesTimeMs()
  {
    return $this->allUpdatesTimeMs;
  }
  /**
   * @param string $commitTimeMs
   */
  public function setCommitTimeMs($commitTimeMs)
  {
    $this->commitTimeMs = $commitTimeMs;
  }
  /**
   * @return string
   */
  public function getCommitTimeMs()
  {
    return $this->commitTimeMs;
  }
  /**
   * @param string[] $customMetrics
   */
  public function setCustomMetrics($customMetrics)
  {
    $this->customMetrics = $customMetrics;
  }
  /**
   * @return string[]
   */
  public function getCustomMetrics()
  {
    return $this->customMetrics;
  }
  /**
   * @param string $memoryUsedBytes
   */
  public function setMemoryUsedBytes($memoryUsedBytes)
  {
    $this->memoryUsedBytes = $memoryUsedBytes;
  }
  /**
   * @return string
   */
  public function getMemoryUsedBytes()
  {
    return $this->memoryUsedBytes;
  }
  /**
   * @param string $numRowsDroppedByWatermark
   */
  public function setNumRowsDroppedByWatermark($numRowsDroppedByWatermark)
  {
    $this->numRowsDroppedByWatermark = $numRowsDroppedByWatermark;
  }
  /**
   * @return string
   */
  public function getNumRowsDroppedByWatermark()
  {
    return $this->numRowsDroppedByWatermark;
  }
  /**
   * @param string $numRowsRemoved
   */
  public function setNumRowsRemoved($numRowsRemoved)
  {
    $this->numRowsRemoved = $numRowsRemoved;
  }
  /**
   * @return string
   */
  public function getNumRowsRemoved()
  {
    return $this->numRowsRemoved;
  }
  /**
   * @param string $numRowsTotal
   */
  public function setNumRowsTotal($numRowsTotal)
  {
    $this->numRowsTotal = $numRowsTotal;
  }
  /**
   * @return string
   */
  public function getNumRowsTotal()
  {
    return $this->numRowsTotal;
  }
  /**
   * @param string $numRowsUpdated
   */
  public function setNumRowsUpdated($numRowsUpdated)
  {
    $this->numRowsUpdated = $numRowsUpdated;
  }
  /**
   * @return string
   */
  public function getNumRowsUpdated()
  {
    return $this->numRowsUpdated;
  }
  /**
   * @param string $numShufflePartitions
   */
  public function setNumShufflePartitions($numShufflePartitions)
  {
    $this->numShufflePartitions = $numShufflePartitions;
  }
  /**
   * @return string
   */
  public function getNumShufflePartitions()
  {
    return $this->numShufflePartitions;
  }
  /**
   * @param string $numStateStoreInstances
   */
  public function setNumStateStoreInstances($numStateStoreInstances)
  {
    $this->numStateStoreInstances = $numStateStoreInstances;
  }
  /**
   * @return string
   */
  public function getNumStateStoreInstances()
  {
    return $this->numStateStoreInstances;
  }
  /**
   * @param string $operatorName
   */
  public function setOperatorName($operatorName)
  {
    $this->operatorName = $operatorName;
  }
  /**
   * @return string
   */
  public function getOperatorName()
  {
    return $this->operatorName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StateOperatorProgress::class, 'Google_Service_Dataproc_StateOperatorProgress');
