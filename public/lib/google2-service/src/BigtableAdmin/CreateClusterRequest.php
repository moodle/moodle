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

namespace Google\Service\BigtableAdmin;

class CreateClusterRequest extends \Google\Model
{
  protected $clusterType = Cluster::class;
  protected $clusterDataType = '';
  /**
   * Required. The ID to be used when referring to the new cluster within its
   * instance, e.g., just `mycluster` rather than
   * `projects/myproject/instances/myinstance/clusters/mycluster`.
   *
   * @var string
   */
  public $clusterId;
  /**
   * Required. The unique name of the instance in which to create the new
   * cluster. Values are of the form `projects/{project}/instances/{instance}`.
   *
   * @var string
   */
  public $parent;

  /**
   * Required. The cluster to be created. Fields marked `OutputOnly` must be
   * left blank.
   *
   * @param Cluster $cluster
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
   * Required. The ID to be used when referring to the new cluster within its
   * instance, e.g., just `mycluster` rather than
   * `projects/myproject/instances/myinstance/clusters/mycluster`.
   *
   * @param string $clusterId
   */
  public function setClusterId($clusterId)
  {
    $this->clusterId = $clusterId;
  }
  /**
   * @return string
   */
  public function getClusterId()
  {
    return $this->clusterId;
  }
  /**
   * Required. The unique name of the instance in which to create the new
   * cluster. Values are of the form `projects/{project}/instances/{instance}`.
   *
   * @param string $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }
  /**
   * @return string
   */
  public function getParent()
  {
    return $this->parent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreateClusterRequest::class, 'Google_Service_BigtableAdmin_CreateClusterRequest');
