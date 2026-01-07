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

class RepairClusterRequest extends \Google\Collection
{
  protected $collection_key = 'nodePools';
  protected $clusterType = ClusterToRepair::class;
  protected $clusterDataType = '';
  /**
   * Optional. Specifying the cluster_uuid means the RPC will fail (with error
   * NOT_FOUND) if a cluster with the specified UUID does not exist.
   *
   * @var string
   */
  public $clusterUuid;
  /**
   * Optional. Whether the request is submitted by Dataproc super user. If true,
   * IAM will check 'dataproc.clusters.repair' permission instead of
   * 'dataproc.clusters.update' permission. This is to give Dataproc superuser
   * the ability to repair clusters without granting the overly broad update
   * permission.
   *
   * @var bool
   */
  public $dataprocSuperUser;
  /**
   * Optional. Timeout for graceful YARN decommissioning. Graceful
   * decommissioning facilitates the removal of cluster nodes without
   * interrupting jobs in progress. The timeout specifies the amount of time to
   * wait for jobs finish before forcefully removing nodes. The default timeout
   * is 0 for forceful decommissioning, and the maximum timeout period is 1 day.
   * (see JSON Mapping—Duration (https://developers.google.com/protocol-
   * buffers/docs/proto3#json)).graceful_decommission_timeout is supported in
   * Dataproc image versions 1.2+.
   *
   * @var string
   */
  public $gracefulDecommissionTimeout;
  protected $nodePoolsType = NodePool::class;
  protected $nodePoolsDataType = 'array';
  /**
   * Optional. operation id of the parent operation sending the repair request
   *
   * @var string
   */
  public $parentOperationId;
  /**
   * Optional. A unique ID used to identify the request. If the server receives
   * two RepairClusterRequests with the same ID, the second request is ignored,
   * and the first google.longrunning.Operation created and stored in the
   * backend is returned.Recommendation: Set this value to a UUID
   * (https://en.wikipedia.org/wiki/Universally_unique_identifier).The ID must
   * contain only letters (a-z, A-Z), numbers (0-9), underscores (_), and
   * hyphens (-). The maximum length is 40 characters.
   *
   * @var string
   */
  public $requestId;

  /**
   * Optional. Cluster to be repaired
   *
   * @param ClusterToRepair $cluster
   */
  public function setCluster(ClusterToRepair $cluster)
  {
    $this->cluster = $cluster;
  }
  /**
   * @return ClusterToRepair
   */
  public function getCluster()
  {
    return $this->cluster;
  }
  /**
   * Optional. Specifying the cluster_uuid means the RPC will fail (with error
   * NOT_FOUND) if a cluster with the specified UUID does not exist.
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
   * Optional. Whether the request is submitted by Dataproc super user. If true,
   * IAM will check 'dataproc.clusters.repair' permission instead of
   * 'dataproc.clusters.update' permission. This is to give Dataproc superuser
   * the ability to repair clusters without granting the overly broad update
   * permission.
   *
   * @param bool $dataprocSuperUser
   */
  public function setDataprocSuperUser($dataprocSuperUser)
  {
    $this->dataprocSuperUser = $dataprocSuperUser;
  }
  /**
   * @return bool
   */
  public function getDataprocSuperUser()
  {
    return $this->dataprocSuperUser;
  }
  /**
   * Optional. Timeout for graceful YARN decommissioning. Graceful
   * decommissioning facilitates the removal of cluster nodes without
   * interrupting jobs in progress. The timeout specifies the amount of time to
   * wait for jobs finish before forcefully removing nodes. The default timeout
   * is 0 for forceful decommissioning, and the maximum timeout period is 1 day.
   * (see JSON Mapping—Duration (https://developers.google.com/protocol-
   * buffers/docs/proto3#json)).graceful_decommission_timeout is supported in
   * Dataproc image versions 1.2+.
   *
   * @param string $gracefulDecommissionTimeout
   */
  public function setGracefulDecommissionTimeout($gracefulDecommissionTimeout)
  {
    $this->gracefulDecommissionTimeout = $gracefulDecommissionTimeout;
  }
  /**
   * @return string
   */
  public function getGracefulDecommissionTimeout()
  {
    return $this->gracefulDecommissionTimeout;
  }
  /**
   * Optional. Node pools and corresponding repair action to be taken. All node
   * pools should be unique in this request. i.e. Multiple entries for the same
   * node pool id are not allowed.
   *
   * @param NodePool[] $nodePools
   */
  public function setNodePools($nodePools)
  {
    $this->nodePools = $nodePools;
  }
  /**
   * @return NodePool[]
   */
  public function getNodePools()
  {
    return $this->nodePools;
  }
  /**
   * Optional. operation id of the parent operation sending the repair request
   *
   * @param string $parentOperationId
   */
  public function setParentOperationId($parentOperationId)
  {
    $this->parentOperationId = $parentOperationId;
  }
  /**
   * @return string
   */
  public function getParentOperationId()
  {
    return $this->parentOperationId;
  }
  /**
   * Optional. A unique ID used to identify the request. If the server receives
   * two RepairClusterRequests with the same ID, the second request is ignored,
   * and the first google.longrunning.Operation created and stored in the
   * backend is returned.Recommendation: Set this value to a UUID
   * (https://en.wikipedia.org/wiki/Universally_unique_identifier).The ID must
   * contain only letters (a-z, A-Z), numbers (0-9), underscores (_), and
   * hyphens (-). The maximum length is 40 characters.
   *
   * @param string $requestId
   */
  public function setRequestId($requestId)
  {
    $this->requestId = $requestId;
  }
  /**
   * @return string
   */
  public function getRequestId()
  {
    return $this->requestId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RepairClusterRequest::class, 'Google_Service_Dataproc_RepairClusterRequest');
