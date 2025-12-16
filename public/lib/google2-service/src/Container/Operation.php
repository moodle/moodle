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

namespace Google\Service\Container;

class Operation extends \Google\Collection
{
  /**
   * Not set.
   */
  public const OPERATION_TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * The cluster is being created. The cluster should be assumed to be unusable
   * until the operation finishes. In the event of the operation failing, the
   * cluster will enter the ERROR state and eventually be deleted.
   */
  public const OPERATION_TYPE_CREATE_CLUSTER = 'CREATE_CLUSTER';
  /**
   * The cluster is being deleted. The cluster should be assumed to be unusable
   * as soon as this operation starts. In the event of the operation failing,
   * the cluster will enter the ERROR state and the deletion will be
   * automatically retried until completed.
   */
  public const OPERATION_TYPE_DELETE_CLUSTER = 'DELETE_CLUSTER';
  /**
   * The cluster version is being updated. Note that this includes "upgrades" to
   * the same version, which are simply a recreation. This also includes [auto-
   * upgrades](https://cloud.google.com/kubernetes-engine/docs/concepts/cluster-
   * upgrades#upgrading_automatically). For more details, see [documentation on
   * cluster upgrades](https://cloud.google.com/kubernetes-
   * engine/docs/concepts/cluster-upgrades#cluster_upgrades).
   */
  public const OPERATION_TYPE_UPGRADE_MASTER = 'UPGRADE_MASTER';
  /**
   * A node pool is being updated. Despite calling this an "upgrade", this
   * includes most forms of updates to node pools. This also includes [auto-
   * upgrades](https://cloud.google.com/kubernetes-engine/docs/how-to/node-auto-
   * upgrades). This operation sets the progress field and may be canceled. The
   * upgrade strategy depends on [node pool
   * configuration](https://cloud.google.com/kubernetes-
   * engine/docs/concepts/node-pool-upgrade-strategies). The nodes are generally
   * still usable during this operation.
   */
  public const OPERATION_TYPE_UPGRADE_NODES = 'UPGRADE_NODES';
  /**
   * A problem has been detected with the control plane and is being repaired.
   * This operation type is initiated by GKE. For more details, see
   * [documentation on repairs](https://cloud.google.com/kubernetes-
   * engine/docs/concepts/maintenance-windows-and-exclusions#repairs).
   */
  public const OPERATION_TYPE_REPAIR_CLUSTER = 'REPAIR_CLUSTER';
  /**
   * The cluster is being updated. This is a broad category of operations and
   * includes operations that only change metadata as well as those that must
   * recreate the entire cluster. If the control plane must be recreated, this
   * will cause temporary downtime for zonal clusters. Some features require
   * recreating the nodes as well. Those will be recreated as separate
   * operations and the update may not be completely functional until the node
   * pools recreations finish. Node recreations will generally follow
   * [maintenance policies](https://cloud.google.com/kubernetes-
   * engine/docs/concepts/maintenance-windows-and-exclusions). Some GKE-
   * initiated operations use this type. This includes certain types of auto-
   * upgrades and incident mitigations.
   */
  public const OPERATION_TYPE_UPDATE_CLUSTER = 'UPDATE_CLUSTER';
  /**
   * A node pool is being created. The node pool should be assumed to be
   * unusable until this operation finishes. In the event of an error, the node
   * pool may be partially created. If enabled, [node
   * autoprovisioning](https://cloud.google.com/kubernetes-engine/docs/how-
   * to/node-auto-provisioning) may have automatically initiated such
   * operations.
   */
  public const OPERATION_TYPE_CREATE_NODE_POOL = 'CREATE_NODE_POOL';
  /**
   * The node pool is being deleted. The node pool should be assumed to be
   * unusable as soon as this operation starts.
   */
  public const OPERATION_TYPE_DELETE_NODE_POOL = 'DELETE_NODE_POOL';
  /**
   * The node pool's manamagent field is being updated. These operations only
   * update metadata and may be concurrent with most other operations.
   */
  public const OPERATION_TYPE_SET_NODE_POOL_MANAGEMENT = 'SET_NODE_POOL_MANAGEMENT';
  /**
   * A problem has been detected with nodes and [they are being
   * repaired](https://cloud.google.com/kubernetes-engine/docs/how-to/node-auto-
   * repair). This operation type is initiated by GKE, typically automatically.
   * This operation may be concurrent with other operations and there may be
   * multiple repairs occurring on the same node pool.
   */
  public const OPERATION_TYPE_AUTO_REPAIR_NODES = 'AUTO_REPAIR_NODES';
  /**
   * Unused. Automatic node upgrade uses UPGRADE_NODES.
   *
   * @deprecated
   */
  public const OPERATION_TYPE_AUTO_UPGRADE_NODES = 'AUTO_UPGRADE_NODES';
  /**
   * Unused. Updating labels uses UPDATE_CLUSTER.
   *
   * @deprecated
   */
  public const OPERATION_TYPE_SET_LABELS = 'SET_LABELS';
  /**
   * Unused. Updating master auth uses UPDATE_CLUSTER.
   *
   * @deprecated
   */
  public const OPERATION_TYPE_SET_MASTER_AUTH = 'SET_MASTER_AUTH';
  /**
   * The node pool is being resized. With the exception of resizing to or from
   * size zero, the node pool is generally usable during this operation.
   */
  public const OPERATION_TYPE_SET_NODE_POOL_SIZE = 'SET_NODE_POOL_SIZE';
  /**
   * Unused. Updating network policy uses UPDATE_CLUSTER.
   *
   * @deprecated
   */
  public const OPERATION_TYPE_SET_NETWORK_POLICY = 'SET_NETWORK_POLICY';
  /**
   * Unused. Updating maintenance policy uses UPDATE_CLUSTER.
   *
   * @deprecated
   */
  public const OPERATION_TYPE_SET_MAINTENANCE_POLICY = 'SET_MAINTENANCE_POLICY';
  /**
   * The control plane is being resized. This operation type is initiated by
   * GKE. These operations are often performed preemptively to ensure that the
   * control plane has sufficient resources and is not typically an indication
   * of issues. For more details, see [documentation on
   * resizes](https://cloud.google.com/kubernetes-
   * engine/docs/concepts/maintenance-windows-and-exclusions#repairs).
   */
  public const OPERATION_TYPE_RESIZE_CLUSTER = 'RESIZE_CLUSTER';
  /**
   * Fleet features of GKE Enterprise are being upgraded. The cluster should be
   * assumed to be blocked for other upgrades until the operation finishes.
   */
  public const OPERATION_TYPE_FLEET_FEATURE_UPGRADE = 'FLEET_FEATURE_UPGRADE';
  /**
   * Not set.
   */
  public const STATUS_STATUS_UNSPECIFIED = 'STATUS_UNSPECIFIED';
  /**
   * The operation has been created.
   */
  public const STATUS_PENDING = 'PENDING';
  /**
   * The operation is currently running.
   */
  public const STATUS_RUNNING = 'RUNNING';
  /**
   * The operation is done, either cancelled or completed.
   */
  public const STATUS_DONE = 'DONE';
  /**
   * The operation is aborting.
   */
  public const STATUS_ABORTING = 'ABORTING';
  protected $collection_key = 'nodepoolConditions';
  protected $clusterConditionsType = StatusCondition::class;
  protected $clusterConditionsDataType = 'array';
  /**
   * Output only. Detailed operation progress, if available.
   *
   * @var string
   */
  public $detail;
  /**
   * Output only. The time the operation completed, in
   * [RFC3339](https://www.ietf.org/rfc/rfc3339.txt) text format.
   *
   * @var string
   */
  public $endTime;
  protected $errorType = Status::class;
  protected $errorDataType = '';
  /**
   * Output only. The name of the Google Compute Engine
   * [zone](https://cloud.google.com/compute/docs/regions-zones/regions-
   * zones#available) or [region](https://cloud.google.com/compute/docs/regions-
   * zones/regions-zones#available) in which the cluster resides.
   *
   * @var string
   */
  public $location;
  /**
   * Output only. The server-assigned ID for the operation.
   *
   * @var string
   */
  public $name;
  protected $nodepoolConditionsType = StatusCondition::class;
  protected $nodepoolConditionsDataType = 'array';
  /**
   * Output only. The operation type.
   *
   * @var string
   */
  public $operationType;
  protected $progressType = OperationProgress::class;
  protected $progressDataType = '';
  /**
   * Output only. Server-defined URI for the operation. Example:
   * `https://container.googleapis.com/v1alpha1/projects/123/locations/us-
   * central1/operations/operation-123`.
   *
   * @var string
   */
  public $selfLink;
  /**
   * Output only. The time the operation started, in
   * [RFC3339](https://www.ietf.org/rfc/rfc3339.txt) text format.
   *
   * @var string
   */
  public $startTime;
  /**
   * Output only. The current status of the operation.
   *
   * @var string
   */
  public $status;
  /**
   * Output only. If an error has occurred, a textual description of the error.
   * Deprecated. Use the field error instead.
   *
   * @deprecated
   * @var string
   */
  public $statusMessage;
  /**
   * Output only. Server-defined URI for the target of the operation. The format
   * of this is a URI to the resource being modified (such as a cluster, node
   * pool, or node). For node pool repairs, there may be multiple nodes being
   * repaired, but only one will be the target. Examples: - ##
   * `https://container.googleapis.com/v1/projects/123/locations/us-
   * central1/clusters/my-cluster` ##
   * `https://container.googleapis.com/v1/projects/123/zones/us-
   * central1-c/clusters/my-cluster/nodePools/my-np`
   * `https://container.googleapis.com/v1/projects/123/zones/us-
   * central1-c/clusters/my-cluster/nodePools/my-np/node/my-node`
   *
   * @var string
   */
  public $targetLink;
  /**
   * Output only. The name of the Google Compute Engine
   * [zone](https://cloud.google.com/compute/docs/zones#available) in which the
   * operation is taking place. This field is deprecated, use location instead.
   *
   * @deprecated
   * @var string
   */
  public $zone;

  /**
   * Which conditions caused the current cluster state. Deprecated. Use field
   * error instead.
   *
   * @deprecated
   * @param StatusCondition[] $clusterConditions
   */
  public function setClusterConditions($clusterConditions)
  {
    $this->clusterConditions = $clusterConditions;
  }
  /**
   * @deprecated
   * @return StatusCondition[]
   */
  public function getClusterConditions()
  {
    return $this->clusterConditions;
  }
  /**
   * Output only. Detailed operation progress, if available.
   *
   * @param string $detail
   */
  public function setDetail($detail)
  {
    $this->detail = $detail;
  }
  /**
   * @return string
   */
  public function getDetail()
  {
    return $this->detail;
  }
  /**
   * Output only. The time the operation completed, in
   * [RFC3339](https://www.ietf.org/rfc/rfc3339.txt) text format.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * The error result of the operation in case of failure.
   *
   * @param Status $error
   */
  public function setError(Status $error)
  {
    $this->error = $error;
  }
  /**
   * @return Status
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * Output only. The name of the Google Compute Engine
   * [zone](https://cloud.google.com/compute/docs/regions-zones/regions-
   * zones#available) or [region](https://cloud.google.com/compute/docs/regions-
   * zones/regions-zones#available) in which the cluster resides.
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * Output only. The server-assigned ID for the operation.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Which conditions caused the current node pool state. Deprecated. Use field
   * error instead.
   *
   * @deprecated
   * @param StatusCondition[] $nodepoolConditions
   */
  public function setNodepoolConditions($nodepoolConditions)
  {
    $this->nodepoolConditions = $nodepoolConditions;
  }
  /**
   * @deprecated
   * @return StatusCondition[]
   */
  public function getNodepoolConditions()
  {
    return $this->nodepoolConditions;
  }
  /**
   * Output only. The operation type.
   *
   * Accepted values: TYPE_UNSPECIFIED, CREATE_CLUSTER, DELETE_CLUSTER,
   * UPGRADE_MASTER, UPGRADE_NODES, REPAIR_CLUSTER, UPDATE_CLUSTER,
   * CREATE_NODE_POOL, DELETE_NODE_POOL, SET_NODE_POOL_MANAGEMENT,
   * AUTO_REPAIR_NODES, AUTO_UPGRADE_NODES, SET_LABELS, SET_MASTER_AUTH,
   * SET_NODE_POOL_SIZE, SET_NETWORK_POLICY, SET_MAINTENANCE_POLICY,
   * RESIZE_CLUSTER, FLEET_FEATURE_UPGRADE
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
   * Output only. Progress information for an operation.
   *
   * @param OperationProgress $progress
   */
  public function setProgress(OperationProgress $progress)
  {
    $this->progress = $progress;
  }
  /**
   * @return OperationProgress
   */
  public function getProgress()
  {
    return $this->progress;
  }
  /**
   * Output only. Server-defined URI for the operation. Example:
   * `https://container.googleapis.com/v1alpha1/projects/123/locations/us-
   * central1/operations/operation-123`.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * Output only. The time the operation started, in
   * [RFC3339](https://www.ietf.org/rfc/rfc3339.txt) text format.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * Output only. The current status of the operation.
   *
   * Accepted values: STATUS_UNSPECIFIED, PENDING, RUNNING, DONE, ABORTING
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Output only. If an error has occurred, a textual description of the error.
   * Deprecated. Use the field error instead.
   *
   * @deprecated
   * @param string $statusMessage
   */
  public function setStatusMessage($statusMessage)
  {
    $this->statusMessage = $statusMessage;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getStatusMessage()
  {
    return $this->statusMessage;
  }
  /**
   * Output only. Server-defined URI for the target of the operation. The format
   * of this is a URI to the resource being modified (such as a cluster, node
   * pool, or node). For node pool repairs, there may be multiple nodes being
   * repaired, but only one will be the target. Examples: - ##
   * `https://container.googleapis.com/v1/projects/123/locations/us-
   * central1/clusters/my-cluster` ##
   * `https://container.googleapis.com/v1/projects/123/zones/us-
   * central1-c/clusters/my-cluster/nodePools/my-np`
   * `https://container.googleapis.com/v1/projects/123/zones/us-
   * central1-c/clusters/my-cluster/nodePools/my-np/node/my-node`
   *
   * @param string $targetLink
   */
  public function setTargetLink($targetLink)
  {
    $this->targetLink = $targetLink;
  }
  /**
   * @return string
   */
  public function getTargetLink()
  {
    return $this->targetLink;
  }
  /**
   * Output only. The name of the Google Compute Engine
   * [zone](https://cloud.google.com/compute/docs/zones#available) in which the
   * operation is taking place. This field is deprecated, use location instead.
   *
   * @deprecated
   * @param string $zone
   */
  public function setZone($zone)
  {
    $this->zone = $zone;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getZone()
  {
    return $this->zone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Operation::class, 'Google_Service_Container_Operation');
