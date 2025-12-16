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

namespace Google\Service\CloudRedis;

class CrossClusterReplicationConfig extends \Google\Collection
{
  /**
   * Cluster role is not set. The behavior is equivalent to NONE.
   */
  public const CLUSTER_ROLE_CLUSTER_ROLE_UNSPECIFIED = 'CLUSTER_ROLE_UNSPECIFIED';
  /**
   * This cluster does not participate in cross cluster replication. It is an
   * independent cluster and does not replicate to or from any other clusters.
   */
  public const CLUSTER_ROLE_NONE = 'NONE';
  /**
   * A cluster that allows both reads and writes. Any data written to this
   * cluster is also replicated to the attached secondary clusters.
   */
  public const CLUSTER_ROLE_PRIMARY = 'PRIMARY';
  /**
   * A cluster that allows only reads and replicates data from a primary
   * cluster.
   */
  public const CLUSTER_ROLE_SECONDARY = 'SECONDARY';
  protected $collection_key = 'secondaryClusters';
  /**
   * Output only. The role of the cluster in cross cluster replication.
   *
   * @var string
   */
  public $clusterRole;
  protected $membershipType = Membership::class;
  protected $membershipDataType = '';
  protected $primaryClusterType = RemoteCluster::class;
  protected $primaryClusterDataType = '';
  protected $secondaryClustersType = RemoteCluster::class;
  protected $secondaryClustersDataType = 'array';
  /**
   * Output only. The last time cross cluster replication config was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The role of the cluster in cross cluster replication.
   *
   * Accepted values: CLUSTER_ROLE_UNSPECIFIED, NONE, PRIMARY, SECONDARY
   *
   * @param self::CLUSTER_ROLE_* $clusterRole
   */
  public function setClusterRole($clusterRole)
  {
    $this->clusterRole = $clusterRole;
  }
  /**
   * @return self::CLUSTER_ROLE_*
   */
  public function getClusterRole()
  {
    return $this->clusterRole;
  }
  /**
   * Output only. An output only view of all the member clusters participating
   * in the cross cluster replication. This view will be provided by every
   * member cluster irrespective of its cluster role(primary or secondary). A
   * primary cluster can provide information about all the secondary clusters
   * replicating from it. However, a secondary cluster only knows about the
   * primary cluster from which it is replicating. However, for scenarios, where
   * the primary cluster is unavailable(e.g. regional outage), a GetCluster
   * request can be sent to any other member cluster and this field will list
   * all the member clusters participating in cross cluster replication.
   *
   * @param Membership $membership
   */
  public function setMembership(Membership $membership)
  {
    $this->membership = $membership;
  }
  /**
   * @return Membership
   */
  public function getMembership()
  {
    return $this->membership;
  }
  /**
   * Details of the primary cluster that is used as the replication source for
   * this secondary cluster. This field is only set for a secondary cluster.
   *
   * @param RemoteCluster $primaryCluster
   */
  public function setPrimaryCluster(RemoteCluster $primaryCluster)
  {
    $this->primaryCluster = $primaryCluster;
  }
  /**
   * @return RemoteCluster
   */
  public function getPrimaryCluster()
  {
    return $this->primaryCluster;
  }
  /**
   * List of secondary clusters that are replicating from this primary cluster.
   * This field is only set for a primary cluster.
   *
   * @param RemoteCluster[] $secondaryClusters
   */
  public function setSecondaryClusters($secondaryClusters)
  {
    $this->secondaryClusters = $secondaryClusters;
  }
  /**
   * @return RemoteCluster[]
   */
  public function getSecondaryClusters()
  {
    return $this->secondaryClusters;
  }
  /**
   * Output only. The last time cross cluster replication config was updated.
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
class_alias(CrossClusterReplicationConfig::class, 'Google_Service_CloudRedis_CrossClusterReplicationConfig');
