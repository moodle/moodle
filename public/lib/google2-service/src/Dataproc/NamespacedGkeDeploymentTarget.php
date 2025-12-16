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

class NamespacedGkeDeploymentTarget extends \Google\Model
{
  /**
   * Optional. A namespace within the GKE cluster to deploy into.
   *
   * @var string
   */
  public $clusterNamespace;
  /**
   * Optional. The target GKE cluster to deploy to. Format:
   * 'projects/{project}/locations/{location}/clusters/{cluster_id}'
   *
   * @var string
   */
  public $targetGkeCluster;

  /**
   * Optional. A namespace within the GKE cluster to deploy into.
   *
   * @param string $clusterNamespace
   */
  public function setClusterNamespace($clusterNamespace)
  {
    $this->clusterNamespace = $clusterNamespace;
  }
  /**
   * @return string
   */
  public function getClusterNamespace()
  {
    return $this->clusterNamespace;
  }
  /**
   * Optional. The target GKE cluster to deploy to. Format:
   * 'projects/{project}/locations/{location}/clusters/{cluster_id}'
   *
   * @param string $targetGkeCluster
   */
  public function setTargetGkeCluster($targetGkeCluster)
  {
    $this->targetGkeCluster = $targetGkeCluster;
  }
  /**
   * @return string
   */
  public function getTargetGkeCluster()
  {
    return $this->targetGkeCluster;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NamespacedGkeDeploymentTarget::class, 'Google_Service_Dataproc_NamespacedGkeDeploymentTarget');
