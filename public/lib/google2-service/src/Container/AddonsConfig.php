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

class AddonsConfig extends \Google\Model
{
  protected $cloudRunConfigType = CloudRunConfig::class;
  protected $cloudRunConfigDataType = '';
  protected $configConnectorConfigType = ConfigConnectorConfig::class;
  protected $configConnectorConfigDataType = '';
  protected $dnsCacheConfigType = DnsCacheConfig::class;
  protected $dnsCacheConfigDataType = '';
  protected $gcePersistentDiskCsiDriverConfigType = GcePersistentDiskCsiDriverConfig::class;
  protected $gcePersistentDiskCsiDriverConfigDataType = '';
  protected $gcpFilestoreCsiDriverConfigType = GcpFilestoreCsiDriverConfig::class;
  protected $gcpFilestoreCsiDriverConfigDataType = '';
  protected $gcsFuseCsiDriverConfigType = GcsFuseCsiDriverConfig::class;
  protected $gcsFuseCsiDriverConfigDataType = '';
  protected $gkeBackupAgentConfigType = GkeBackupAgentConfig::class;
  protected $gkeBackupAgentConfigDataType = '';
  protected $highScaleCheckpointingConfigType = HighScaleCheckpointingConfig::class;
  protected $highScaleCheckpointingConfigDataType = '';
  protected $horizontalPodAutoscalingType = HorizontalPodAutoscaling::class;
  protected $horizontalPodAutoscalingDataType = '';
  protected $httpLoadBalancingType = HttpLoadBalancing::class;
  protected $httpLoadBalancingDataType = '';
  protected $kubernetesDashboardType = KubernetesDashboard::class;
  protected $kubernetesDashboardDataType = '';
  protected $lustreCsiDriverConfigType = LustreCsiDriverConfig::class;
  protected $lustreCsiDriverConfigDataType = '';
  protected $networkPolicyConfigType = NetworkPolicyConfig::class;
  protected $networkPolicyConfigDataType = '';
  protected $parallelstoreCsiDriverConfigType = ParallelstoreCsiDriverConfig::class;
  protected $parallelstoreCsiDriverConfigDataType = '';
  protected $rayOperatorConfigType = RayOperatorConfig::class;
  protected $rayOperatorConfigDataType = '';
  protected $statefulHaConfigType = StatefulHAConfig::class;
  protected $statefulHaConfigDataType = '';

  /**
   * Configuration for the Cloud Run addon, which allows the user to use a
   * managed Knative service.
   *
   * @param CloudRunConfig $cloudRunConfig
   */
  public function setCloudRunConfig(CloudRunConfig $cloudRunConfig)
  {
    $this->cloudRunConfig = $cloudRunConfig;
  }
  /**
   * @return CloudRunConfig
   */
  public function getCloudRunConfig()
  {
    return $this->cloudRunConfig;
  }
  /**
   * Configuration for the ConfigConnector add-on, a Kubernetes extension to
   * manage hosted Google Cloud services through the Kubernetes API.
   *
   * @param ConfigConnectorConfig $configConnectorConfig
   */
  public function setConfigConnectorConfig(ConfigConnectorConfig $configConnectorConfig)
  {
    $this->configConnectorConfig = $configConnectorConfig;
  }
  /**
   * @return ConfigConnectorConfig
   */
  public function getConfigConnectorConfig()
  {
    return $this->configConnectorConfig;
  }
  /**
   * Configuration for NodeLocalDNS, a dns cache running on cluster nodes
   *
   * @param DnsCacheConfig $dnsCacheConfig
   */
  public function setDnsCacheConfig(DnsCacheConfig $dnsCacheConfig)
  {
    $this->dnsCacheConfig = $dnsCacheConfig;
  }
  /**
   * @return DnsCacheConfig
   */
  public function getDnsCacheConfig()
  {
    return $this->dnsCacheConfig;
  }
  /**
   * Configuration for the Compute Engine Persistent Disk CSI driver.
   *
   * @param GcePersistentDiskCsiDriverConfig $gcePersistentDiskCsiDriverConfig
   */
  public function setGcePersistentDiskCsiDriverConfig(GcePersistentDiskCsiDriverConfig $gcePersistentDiskCsiDriverConfig)
  {
    $this->gcePersistentDiskCsiDriverConfig = $gcePersistentDiskCsiDriverConfig;
  }
  /**
   * @return GcePersistentDiskCsiDriverConfig
   */
  public function getGcePersistentDiskCsiDriverConfig()
  {
    return $this->gcePersistentDiskCsiDriverConfig;
  }
  /**
   * Configuration for the Filestore CSI driver.
   *
   * @param GcpFilestoreCsiDriverConfig $gcpFilestoreCsiDriverConfig
   */
  public function setGcpFilestoreCsiDriverConfig(GcpFilestoreCsiDriverConfig $gcpFilestoreCsiDriverConfig)
  {
    $this->gcpFilestoreCsiDriverConfig = $gcpFilestoreCsiDriverConfig;
  }
  /**
   * @return GcpFilestoreCsiDriverConfig
   */
  public function getGcpFilestoreCsiDriverConfig()
  {
    return $this->gcpFilestoreCsiDriverConfig;
  }
  /**
   * Configuration for the Cloud Storage Fuse CSI driver.
   *
   * @param GcsFuseCsiDriverConfig $gcsFuseCsiDriverConfig
   */
  public function setGcsFuseCsiDriverConfig(GcsFuseCsiDriverConfig $gcsFuseCsiDriverConfig)
  {
    $this->gcsFuseCsiDriverConfig = $gcsFuseCsiDriverConfig;
  }
  /**
   * @return GcsFuseCsiDriverConfig
   */
  public function getGcsFuseCsiDriverConfig()
  {
    return $this->gcsFuseCsiDriverConfig;
  }
  /**
   * Configuration for the Backup for GKE agent addon.
   *
   * @param GkeBackupAgentConfig $gkeBackupAgentConfig
   */
  public function setGkeBackupAgentConfig(GkeBackupAgentConfig $gkeBackupAgentConfig)
  {
    $this->gkeBackupAgentConfig = $gkeBackupAgentConfig;
  }
  /**
   * @return GkeBackupAgentConfig
   */
  public function getGkeBackupAgentConfig()
  {
    return $this->gkeBackupAgentConfig;
  }
  /**
   * Configuration for the High Scale Checkpointing add-on.
   *
   * @param HighScaleCheckpointingConfig $highScaleCheckpointingConfig
   */
  public function setHighScaleCheckpointingConfig(HighScaleCheckpointingConfig $highScaleCheckpointingConfig)
  {
    $this->highScaleCheckpointingConfig = $highScaleCheckpointingConfig;
  }
  /**
   * @return HighScaleCheckpointingConfig
   */
  public function getHighScaleCheckpointingConfig()
  {
    return $this->highScaleCheckpointingConfig;
  }
  /**
   * Configuration for the horizontal pod autoscaling feature, which increases
   * or decreases the number of replica pods a replication controller has based
   * on the resource usage of the existing pods.
   *
   * @param HorizontalPodAutoscaling $horizontalPodAutoscaling
   */
  public function setHorizontalPodAutoscaling(HorizontalPodAutoscaling $horizontalPodAutoscaling)
  {
    $this->horizontalPodAutoscaling = $horizontalPodAutoscaling;
  }
  /**
   * @return HorizontalPodAutoscaling
   */
  public function getHorizontalPodAutoscaling()
  {
    return $this->horizontalPodAutoscaling;
  }
  /**
   * Configuration for the HTTP (L7) load balancing controller addon, which
   * makes it easy to set up HTTP load balancers for services in a cluster.
   *
   * @param HttpLoadBalancing $httpLoadBalancing
   */
  public function setHttpLoadBalancing(HttpLoadBalancing $httpLoadBalancing)
  {
    $this->httpLoadBalancing = $httpLoadBalancing;
  }
  /**
   * @return HttpLoadBalancing
   */
  public function getHttpLoadBalancing()
  {
    return $this->httpLoadBalancing;
  }
  /**
   * Configuration for the Kubernetes Dashboard. This addon is deprecated, and
   * will be disabled in 1.15. It is recommended to use the Cloud Console to
   * manage and monitor your Kubernetes clusters, workloads and applications.
   * For more information, see: https://cloud.google.com/kubernetes-
   * engine/docs/concepts/dashboards
   *
   * @deprecated
   * @param KubernetesDashboard $kubernetesDashboard
   */
  public function setKubernetesDashboard(KubernetesDashboard $kubernetesDashboard)
  {
    $this->kubernetesDashboard = $kubernetesDashboard;
  }
  /**
   * @deprecated
   * @return KubernetesDashboard
   */
  public function getKubernetesDashboard()
  {
    return $this->kubernetesDashboard;
  }
  /**
   * Configuration for the Lustre CSI driver.
   *
   * @param LustreCsiDriverConfig $lustreCsiDriverConfig
   */
  public function setLustreCsiDriverConfig(LustreCsiDriverConfig $lustreCsiDriverConfig)
  {
    $this->lustreCsiDriverConfig = $lustreCsiDriverConfig;
  }
  /**
   * @return LustreCsiDriverConfig
   */
  public function getLustreCsiDriverConfig()
  {
    return $this->lustreCsiDriverConfig;
  }
  /**
   * Configuration for NetworkPolicy. This only tracks whether the addon is
   * enabled or not on the Master, it does not track whether network policy is
   * enabled for the nodes.
   *
   * @param NetworkPolicyConfig $networkPolicyConfig
   */
  public function setNetworkPolicyConfig(NetworkPolicyConfig $networkPolicyConfig)
  {
    $this->networkPolicyConfig = $networkPolicyConfig;
  }
  /**
   * @return NetworkPolicyConfig
   */
  public function getNetworkPolicyConfig()
  {
    return $this->networkPolicyConfig;
  }
  /**
   * Configuration for the Cloud Storage Parallelstore CSI driver.
   *
   * @param ParallelstoreCsiDriverConfig $parallelstoreCsiDriverConfig
   */
  public function setParallelstoreCsiDriverConfig(ParallelstoreCsiDriverConfig $parallelstoreCsiDriverConfig)
  {
    $this->parallelstoreCsiDriverConfig = $parallelstoreCsiDriverConfig;
  }
  /**
   * @return ParallelstoreCsiDriverConfig
   */
  public function getParallelstoreCsiDriverConfig()
  {
    return $this->parallelstoreCsiDriverConfig;
  }
  /**
   * Optional. Configuration for Ray Operator addon.
   *
   * @param RayOperatorConfig $rayOperatorConfig
   */
  public function setRayOperatorConfig(RayOperatorConfig $rayOperatorConfig)
  {
    $this->rayOperatorConfig = $rayOperatorConfig;
  }
  /**
   * @return RayOperatorConfig
   */
  public function getRayOperatorConfig()
  {
    return $this->rayOperatorConfig;
  }
  /**
   * Optional. Configuration for the StatefulHA add-on.
   *
   * @param StatefulHAConfig $statefulHaConfig
   */
  public function setStatefulHaConfig(StatefulHAConfig $statefulHaConfig)
  {
    $this->statefulHaConfig = $statefulHaConfig;
  }
  /**
   * @return StatefulHAConfig
   */
  public function getStatefulHaConfig()
  {
    return $this->statefulHaConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AddonsConfig::class, 'Google_Service_Container_AddonsConfig');
