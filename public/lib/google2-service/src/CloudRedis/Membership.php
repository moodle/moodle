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

class Membership extends \Google\Collection
{
  protected $collection_key = 'secondaryClusters';
  protected $primaryClusterType = RemoteCluster::class;
  protected $primaryClusterDataType = '';
  protected $secondaryClustersType = RemoteCluster::class;
  protected $secondaryClustersDataType = 'array';

  /**
   * Output only. The primary cluster that acts as the source of replication for
   * the secondary clusters.
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
   * Output only. The list of secondary clusters replicating from the primary
   * cluster.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Membership::class, 'Google_Service_CloudRedis_Membership');
