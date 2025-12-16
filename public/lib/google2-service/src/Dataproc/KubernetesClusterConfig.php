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

class KubernetesClusterConfig extends \Google\Model
{
  protected $gkeClusterConfigType = GkeClusterConfig::class;
  protected $gkeClusterConfigDataType = '';
  /**
   * Optional. A namespace within the Kubernetes cluster to deploy into. If this
   * namespace does not exist, it is created. If it exists, Dataproc verifies
   * that another Dataproc VirtualCluster is not installed into it. If not
   * specified, the name of the Dataproc Cluster is used.
   *
   * @var string
   */
  public $kubernetesNamespace;
  protected $kubernetesSoftwareConfigType = KubernetesSoftwareConfig::class;
  protected $kubernetesSoftwareConfigDataType = '';

  /**
   * Required. The configuration for running the Dataproc cluster on GKE.
   *
   * @param GkeClusterConfig $gkeClusterConfig
   */
  public function setGkeClusterConfig(GkeClusterConfig $gkeClusterConfig)
  {
    $this->gkeClusterConfig = $gkeClusterConfig;
  }
  /**
   * @return GkeClusterConfig
   */
  public function getGkeClusterConfig()
  {
    return $this->gkeClusterConfig;
  }
  /**
   * Optional. A namespace within the Kubernetes cluster to deploy into. If this
   * namespace does not exist, it is created. If it exists, Dataproc verifies
   * that another Dataproc VirtualCluster is not installed into it. If not
   * specified, the name of the Dataproc Cluster is used.
   *
   * @param string $kubernetesNamespace
   */
  public function setKubernetesNamespace($kubernetesNamespace)
  {
    $this->kubernetesNamespace = $kubernetesNamespace;
  }
  /**
   * @return string
   */
  public function getKubernetesNamespace()
  {
    return $this->kubernetesNamespace;
  }
  /**
   * Optional. The software configuration for this Dataproc cluster running on
   * Kubernetes.
   *
   * @param KubernetesSoftwareConfig $kubernetesSoftwareConfig
   */
  public function setKubernetesSoftwareConfig(KubernetesSoftwareConfig $kubernetesSoftwareConfig)
  {
    $this->kubernetesSoftwareConfig = $kubernetesSoftwareConfig;
  }
  /**
   * @return KubernetesSoftwareConfig
   */
  public function getKubernetesSoftwareConfig()
  {
    return $this->kubernetesSoftwareConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(KubernetesClusterConfig::class, 'Google_Service_Dataproc_KubernetesClusterConfig');
