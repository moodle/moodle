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

class CreateNodePoolRequest extends \Google\Model
{
  /**
   * Deprecated. The name of the cluster. This field has been deprecated and
   * replaced by the parent field.
   *
   * @deprecated
   * @var string
   */
  public $clusterId;
  protected $nodePoolType = NodePool::class;
  protected $nodePoolDataType = '';
  /**
   * The parent (project, location, cluster name) where the node pool will be
   * created. Specified in the format `projects/locations/clusters`.
   *
   * @var string
   */
  public $parent;
  /**
   * Deprecated. The Google Developers Console [project ID or project
   * number](https://cloud.google.com/resource-manager/docs/creating-managing-
   * projects). This field has been deprecated and replaced by the parent field.
   *
   * @deprecated
   * @var string
   */
  public $projectId;
  /**
   * Deprecated. The name of the Google Compute Engine
   * [zone](https://cloud.google.com/compute/docs/zones#available) in which the
   * cluster resides. This field has been deprecated and replaced by the parent
   * field.
   *
   * @deprecated
   * @var string
   */
  public $zone;

  /**
   * Deprecated. The name of the cluster. This field has been deprecated and
   * replaced by the parent field.
   *
   * @deprecated
   * @param string $clusterId
   */
  public function setClusterId($clusterId)
  {
    $this->clusterId = $clusterId;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getClusterId()
  {
    return $this->clusterId;
  }
  /**
   * Required. The node pool to create.
   *
   * @param NodePool $nodePool
   */
  public function setNodePool(NodePool $nodePool)
  {
    $this->nodePool = $nodePool;
  }
  /**
   * @return NodePool
   */
  public function getNodePool()
  {
    return $this->nodePool;
  }
  /**
   * The parent (project, location, cluster name) where the node pool will be
   * created. Specified in the format `projects/locations/clusters`.
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
  /**
   * Deprecated. The Google Developers Console [project ID or project
   * number](https://cloud.google.com/resource-manager/docs/creating-managing-
   * projects). This field has been deprecated and replaced by the parent field.
   *
   * @deprecated
   * @param string $projectId
   */
  public function setProjectId($projectId)
  {
    $this->projectId = $projectId;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getProjectId()
  {
    return $this->projectId;
  }
  /**
   * Deprecated. The name of the Google Compute Engine
   * [zone](https://cloud.google.com/compute/docs/zones#available) in which the
   * cluster resides. This field has been deprecated and replaced by the parent
   * field.
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
class_alias(CreateNodePoolRequest::class, 'Google_Service_Container_CreateNodePoolRequest');
