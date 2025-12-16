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

class SetNodePoolAutoscalingRequest extends \Google\Model
{
  protected $autoscalingType = NodePoolAutoscaling::class;
  protected $autoscalingDataType = '';
  /**
   * Deprecated. The name of the cluster to upgrade. This field has been
   * deprecated and replaced by the name field.
   *
   * @deprecated
   * @var string
   */
  public $clusterId;
  /**
   * The name (project, location, cluster, node pool) of the node pool to set
   * autoscaler settings. Specified in the format
   * `projects/locations/clusters/nodePools`.
   *
   * @var string
   */
  public $name;
  /**
   * Deprecated. The name of the node pool to upgrade. This field has been
   * deprecated and replaced by the name field.
   *
   * @deprecated
   * @var string
   */
  public $nodePoolId;
  /**
   * Deprecated. The Google Developers Console [project ID or project
   * number](https://cloud.google.com/resource-manager/docs/creating-managing-
   * projects). This field has been deprecated and replaced by the name field.
   *
   * @deprecated
   * @var string
   */
  public $projectId;
  /**
   * Deprecated. The name of the Google Compute Engine
   * [zone](https://cloud.google.com/compute/docs/zones#available) in which the
   * cluster resides. This field has been deprecated and replaced by the name
   * field.
   *
   * @deprecated
   * @var string
   */
  public $zone;

  /**
   * Required. Autoscaling configuration for the node pool.
   *
   * @param NodePoolAutoscaling $autoscaling
   */
  public function setAutoscaling(NodePoolAutoscaling $autoscaling)
  {
    $this->autoscaling = $autoscaling;
  }
  /**
   * @return NodePoolAutoscaling
   */
  public function getAutoscaling()
  {
    return $this->autoscaling;
  }
  /**
   * Deprecated. The name of the cluster to upgrade. This field has been
   * deprecated and replaced by the name field.
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
   * The name (project, location, cluster, node pool) of the node pool to set
   * autoscaler settings. Specified in the format
   * `projects/locations/clusters/nodePools`.
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
   * Deprecated. The name of the node pool to upgrade. This field has been
   * deprecated and replaced by the name field.
   *
   * @deprecated
   * @param string $nodePoolId
   */
  public function setNodePoolId($nodePoolId)
  {
    $this->nodePoolId = $nodePoolId;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getNodePoolId()
  {
    return $this->nodePoolId;
  }
  /**
   * Deprecated. The Google Developers Console [project ID or project
   * number](https://cloud.google.com/resource-manager/docs/creating-managing-
   * projects). This field has been deprecated and replaced by the name field.
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
   * cluster resides. This field has been deprecated and replaced by the name
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
class_alias(SetNodePoolAutoscalingRequest::class, 'Google_Service_Container_SetNodePoolAutoscalingRequest');
