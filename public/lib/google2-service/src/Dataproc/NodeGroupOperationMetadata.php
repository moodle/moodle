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

class NodeGroupOperationMetadata extends \Google\Collection
{
  /**
   * Node group operation type is unknown.
   */
  public const OPERATION_TYPE_NODE_GROUP_OPERATION_TYPE_UNSPECIFIED = 'NODE_GROUP_OPERATION_TYPE_UNSPECIFIED';
  /**
   * Create node group operation type.
   */
  public const OPERATION_TYPE_CREATE = 'CREATE';
  /**
   * Update node group operation type.
   */
  public const OPERATION_TYPE_UPDATE = 'UPDATE';
  /**
   * Delete node group operation type.
   */
  public const OPERATION_TYPE_DELETE = 'DELETE';
  /**
   * Resize node group operation type.
   */
  public const OPERATION_TYPE_RESIZE = 'RESIZE';
  /**
   * Repair node group operation type.
   */
  public const OPERATION_TYPE_REPAIR = 'REPAIR';
  /**
   * Update node group label operation type.
   */
  public const OPERATION_TYPE_UPDATE_LABELS = 'UPDATE_LABELS';
  /**
   * Start node group operation type.
   */
  public const OPERATION_TYPE_START = 'START';
  /**
   * Stop node group operation type.
   */
  public const OPERATION_TYPE_STOP = 'STOP';
  /**
   * This operation type is used to update the metadata config of a node group.
   * We update the metadata of the VMs in the node group and await for intended
   * config change to be completed at the node group level. Currently, only the
   * identity config update is supported.
   */
  public const OPERATION_TYPE_UPDATE_METADATA_CONFIG = 'UPDATE_METADATA_CONFIG';
  protected $collection_key = 'warnings';
  /**
   * Output only. Cluster UUID associated with the node group operation.
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
   * Output only. Labels associated with the operation.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. Node group ID for the operation.
   *
   * @var string
   */
  public $nodeGroupId;
  /**
   * The operation type.
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
   * Output only. Cluster UUID associated with the node group operation.
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
   * Output only. Labels associated with the operation.
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
   * Output only. Node group ID for the operation.
   *
   * @param string $nodeGroupId
   */
  public function setNodeGroupId($nodeGroupId)
  {
    $this->nodeGroupId = $nodeGroupId;
  }
  /**
   * @return string
   */
  public function getNodeGroupId()
  {
    return $this->nodeGroupId;
  }
  /**
   * The operation type.
   *
   * Accepted values: NODE_GROUP_OPERATION_TYPE_UNSPECIFIED, CREATE, UPDATE,
   * DELETE, RESIZE, REPAIR, UPDATE_LABELS, START, STOP, UPDATE_METADATA_CONFIG
   *
   * @param self::OPERATION_TYPE_* $operationType
   */
  public function setOperationType($operationType)
  {
    $this->operationType = $operationType;
  }
  /**
   * @return self::OPERATION_TYPE_*
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
class_alias(NodeGroupOperationMetadata::class, 'Google_Service_Dataproc_NodeGroupOperationMetadata');
