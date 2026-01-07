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

class GkeClusterConfig extends \Google\Collection
{
  protected $collection_key = 'nodePoolTarget';
  /**
   * Optional. A target GKE cluster to deploy to. It must be in the same project
   * and region as the Dataproc cluster (the GKE cluster can be zonal or
   * regional). Format:
   * 'projects/{project}/locations/{location}/clusters/{cluster_id}'
   *
   * @var string
   */
  public $gkeClusterTarget;
  protected $namespacedGkeDeploymentTargetType = NamespacedGkeDeploymentTarget::class;
  protected $namespacedGkeDeploymentTargetDataType = '';
  protected $nodePoolTargetType = GkeNodePoolTarget::class;
  protected $nodePoolTargetDataType = 'array';

  /**
   * Optional. A target GKE cluster to deploy to. It must be in the same project
   * and region as the Dataproc cluster (the GKE cluster can be zonal or
   * regional). Format:
   * 'projects/{project}/locations/{location}/clusters/{cluster_id}'
   *
   * @param string $gkeClusterTarget
   */
  public function setGkeClusterTarget($gkeClusterTarget)
  {
    $this->gkeClusterTarget = $gkeClusterTarget;
  }
  /**
   * @return string
   */
  public function getGkeClusterTarget()
  {
    return $this->gkeClusterTarget;
  }
  /**
   * Optional. Deprecated. Use gkeClusterTarget. Used only for the deprecated
   * beta. A target for the deployment.
   *
   * @deprecated
   * @param NamespacedGkeDeploymentTarget $namespacedGkeDeploymentTarget
   */
  public function setNamespacedGkeDeploymentTarget(NamespacedGkeDeploymentTarget $namespacedGkeDeploymentTarget)
  {
    $this->namespacedGkeDeploymentTarget = $namespacedGkeDeploymentTarget;
  }
  /**
   * @deprecated
   * @return NamespacedGkeDeploymentTarget
   */
  public function getNamespacedGkeDeploymentTarget()
  {
    return $this->namespacedGkeDeploymentTarget;
  }
  /**
   * Optional. GKE node pools where workloads will be scheduled. At least one
   * node pool must be assigned the DEFAULT GkeNodePoolTarget.Role. If a
   * GkeNodePoolTarget is not specified, Dataproc constructs a DEFAULT
   * GkeNodePoolTarget. Each role can be given to only one GkeNodePoolTarget.
   * All node pools must have the same location settings.
   *
   * @param GkeNodePoolTarget[] $nodePoolTarget
   */
  public function setNodePoolTarget($nodePoolTarget)
  {
    $this->nodePoolTarget = $nodePoolTarget;
  }
  /**
   * @return GkeNodePoolTarget[]
   */
  public function getNodePoolTarget()
  {
    return $this->nodePoolTarget;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GkeClusterConfig::class, 'Google_Service_Dataproc_GkeClusterConfig');
