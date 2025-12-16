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

namespace Google\Service\WorkloadManager;

class SqlserverWorkload extends \Google\Collection
{
  protected $collection_key = 'databases';
  protected $agsType = AvailabilityGroup::class;
  protected $agsDataType = 'array';
  protected $clusterType = Cluster::class;
  protected $clusterDataType = '';
  protected $databasesType = Database::class;
  protected $databasesDataType = 'array';
  protected $loadBalancerServerType = LoadBalancerServer::class;
  protected $loadBalancerServerDataType = '';

  /**
   * @param AvailabilityGroup[]
   */
  public function setAgs($ags)
  {
    $this->ags = $ags;
  }
  /**
   * @return AvailabilityGroup[]
   */
  public function getAgs()
  {
    return $this->ags;
  }
  /**
   * @param Cluster
   */
  public function setCluster(Cluster $cluster)
  {
    $this->cluster = $cluster;
  }
  /**
   * @return Cluster
   */
  public function getCluster()
  {
    return $this->cluster;
  }
  /**
   * @param Database[]
   */
  public function setDatabases($databases)
  {
    $this->databases = $databases;
  }
  /**
   * @return Database[]
   */
  public function getDatabases()
  {
    return $this->databases;
  }
  /**
   * @param LoadBalancerServer
   */
  public function setLoadBalancerServer(LoadBalancerServer $loadBalancerServer)
  {
    $this->loadBalancerServer = $loadBalancerServer;
  }
  /**
   * @return LoadBalancerServer
   */
  public function getLoadBalancerServer()
  {
    return $this->loadBalancerServer;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SqlserverWorkload::class, 'Google_Service_WorkloadManager_SqlserverWorkload');
