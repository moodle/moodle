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

class ClusterOperationMetadata extends \Google\Collection
{
  protected $collection_key = 'warnings';
  /**
   * Output only. Child operation ids
   *
   * @var string[]
   */
  public $childOperationIds;
  /**
   * Output only. Name of the cluster for the operation.
   *
   * @var string
   */
  public $clusterName;
  /**
   * Output only. Cluster UUID for the operation.
   *
   * @var string
   */
  public $clusterUuid;
  /**
   * Output only. Short description of operation.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. Labels associated with the operation
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. The operation type.
   *
   * @var string
   */
  public $operationType;
  protected $statusType = ClusterOperationStatus::class;
  protected $statusDataType = '';
  protected $statusHistoryType = ClusterOperationStatus::class;
  protected $statusHistoryDataType = 'array';
  /**
   * Output only. Errors encountered during operation execution.
   *
   * @var string[]
   */
  public $warnings;

  /**
   * Output only. Child operation ids
   *
   * @param string[] $childOperationIds
   */
  public function setChildOperationIds($childOperationIds)
  {
    $this->childOperationIds = $childOperationIds;
  }
  /**
   * @return string[]
   */
  public function getChildOperationIds()
  {
    return $this->childOperationIds;
  }
  /**
   * Output only. Name of the cluster for the operation.
   *
   * @param string $clusterName
   */
  public function setClusterName($clusterName)
  {
    $this->clusterName = $clusterName;
  }
  /**
   * @return string
   */
  public function getClusterName()
  {
    return $this->clusterName;
  }
  /**
   * Output only. Cluster UUID for the operation.
   *
   * @param string $clusterUuid
   */
  public function setClusterUuid($clusterUuid)
  {
    $this->clusterUuid = $clusterUuid;
  }
  /**
   * @return string
   */
  public function getClusterUuid()
  {
    return $this->clusterUuid;
  }
  /**
   * Output only. Short description of operation.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Output only. Labels associated with the operation
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Output only. The operation type.
   *
   * @param string $operationType
   */
  public function setOperationType($operationType)
  {
    $this->operationType = $operationType;
  }
  /**
   * @return string
   */
  public function getOperationType()
  {
    return $this->operationType;
  }
  /**
   * Output only. Current operation status.
   *
   * @param ClusterOperationStatus $status
   */
  public function setStatus(ClusterOperationStatus $status)
  {
    $this->status = $status;
  }
  /**
   * @return ClusterOperationStatus
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Output only. The previous operation status.
   *
   * @param ClusterOperationStatus[] $statusHistory
   */
  public function setStatusHistory($statusHistory)
  {
    $this->statusHistory = $statusHistory;
  }
  /**
   * @return ClusterOperationStatus[]
   */
  public function getStatusHistory()
  {
    return $this->statusHistory;
  }
  /**
   * Output only. Errors encountered during operation execution.
   *
   * @param string[] $warnings
   */
  public function setWarnings($warnings)
  {
    $this->warnings = $warnings;
  }
  /**
   * @return string[]
   */
  public function getWarnings()
  {
    return $this->warnings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ClusterOperationMetadata::class, 'Google_Service_Dataproc_ClusterOperationMetadata');
