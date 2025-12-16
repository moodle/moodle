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

namespace Google\Service\VMwareEngine;

class AutoscalingPolicy extends \Google\Model
{
  protected $consumedMemoryThresholdsType = Thresholds::class;
  protected $consumedMemoryThresholdsDataType = '';
  protected $cpuThresholdsType = Thresholds::class;
  protected $cpuThresholdsDataType = '';
  protected $grantedMemoryThresholdsType = Thresholds::class;
  protected $grantedMemoryThresholdsDataType = '';
  /**
   * Required. The canonical identifier of the node type to add or remove.
   * Corresponds to the `NodeType`.
   *
   * @var string
   */
  public $nodeTypeId;
  /**
   * Required. Number of nodes to add to a cluster during a scale-out operation.
   * Must be divisible by 2 for stretched clusters. During a scale-in operation
   * only one node (or 2 for stretched clusters) are removed in a single
   * iteration.
   *
   * @var int
   */
  public $scaleOutSize;
  protected $storageThresholdsType = Thresholds::class;
  protected $storageThresholdsDataType = '';

  /**
   * Optional. Utilization thresholds pertaining to amount of consumed memory.
   *
   * @param Thresholds $consumedMemoryThresholds
   */
  public function setConsumedMemoryThresholds(Thresholds $consumedMemoryThresholds)
  {
    $this->consumedMemoryThresholds = $consumedMemoryThresholds;
  }
  /**
   * @return Thresholds
   */
  public function getConsumedMemoryThresholds()
  {
    return $this->consumedMemoryThresholds;
  }
  /**
   * Optional. Utilization thresholds pertaining to CPU utilization.
   *
   * @param Thresholds $cpuThresholds
   */
  public function setCpuThresholds(Thresholds $cpuThresholds)
  {
    $this->cpuThresholds = $cpuThresholds;
  }
  /**
   * @return Thresholds
   */
  public function getCpuThresholds()
  {
    return $this->cpuThresholds;
  }
  /**
   * Optional. Utilization thresholds pertaining to amount of granted memory.
   *
   * @param Thresholds $grantedMemoryThresholds
   */
  public function setGrantedMemoryThresholds(Thresholds $grantedMemoryThresholds)
  {
    $this->grantedMemoryThresholds = $grantedMemoryThresholds;
  }
  /**
   * @return Thresholds
   */
  public function getGrantedMemoryThresholds()
  {
    return $this->grantedMemoryThresholds;
  }
  /**
   * Required. The canonical identifier of the node type to add or remove.
   * Corresponds to the `NodeType`.
   *
   * @param string $nodeTypeId
   */
  public function setNodeTypeId($nodeTypeId)
  {
    $this->nodeTypeId = $nodeTypeId;
  }
  /**
   * @return string
   */
  public function getNodeTypeId()
  {
    return $this->nodeTypeId;
  }
  /**
   * Required. Number of nodes to add to a cluster during a scale-out operation.
   * Must be divisible by 2 for stretched clusters. During a scale-in operation
   * only one node (or 2 for stretched clusters) are removed in a single
   * iteration.
   *
   * @param int $scaleOutSize
   */
  public function setScaleOutSize($scaleOutSize)
  {
    $this->scaleOutSize = $scaleOutSize;
  }
  /**
   * @return int
   */
  public function getScaleOutSize()
  {
    return $this->scaleOutSize;
  }
  /**
   * Optional. Utilization thresholds pertaining to amount of consumed storage.
   *
   * @param Thresholds $storageThresholds
   */
  public function setStorageThresholds(Thresholds $storageThresholds)
  {
    $this->storageThresholds = $storageThresholds;
  }
  /**
   * @return Thresholds
   */
  public function getStorageThresholds()
  {
    return $this->storageThresholds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AutoscalingPolicy::class, 'Google_Service_VMwareEngine_AutoscalingPolicy');
