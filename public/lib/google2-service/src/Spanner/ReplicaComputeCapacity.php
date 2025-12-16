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

namespace Google\Service\Spanner;

class ReplicaComputeCapacity extends \Google\Model
{
  /**
   * The number of nodes allocated to each replica. This may be zero in API
   * responses for instances that are not yet in state `READY`.
   *
   * @var int
   */
  public $nodeCount;
  /**
   * The number of processing units allocated to each replica. This may be zero
   * in API responses for instances that are not yet in state `READY`.
   *
   * @var int
   */
  public $processingUnits;
  protected $replicaSelectionType = InstanceReplicaSelection::class;
  protected $replicaSelectionDataType = '';

  /**
   * The number of nodes allocated to each replica. This may be zero in API
   * responses for instances that are not yet in state `READY`.
   *
   * @param int $nodeCount
   */
  public function setNodeCount($nodeCount)
  {
    $this->nodeCount = $nodeCount;
  }
  /**
   * @return int
   */
  public function getNodeCount()
  {
    return $this->nodeCount;
  }
  /**
   * The number of processing units allocated to each replica. This may be zero
   * in API responses for instances that are not yet in state `READY`.
   *
   * @param int $processingUnits
   */
  public function setProcessingUnits($processingUnits)
  {
    $this->processingUnits = $processingUnits;
  }
  /**
   * @return int
   */
  public function getProcessingUnits()
  {
    return $this->processingUnits;
  }
  /**
   * Required. Identifies replicas by specified properties. All replicas in the
   * selection have the same amount of compute capacity.
   *
   * @param InstanceReplicaSelection $replicaSelection
   */
  public function setReplicaSelection(InstanceReplicaSelection $replicaSelection)
  {
    $this->replicaSelection = $replicaSelection;
  }
  /**
   * @return InstanceReplicaSelection
   */
  public function getReplicaSelection()
  {
    return $this->replicaSelection;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReplicaComputeCapacity::class, 'Google_Service_Spanner_ReplicaComputeCapacity');
