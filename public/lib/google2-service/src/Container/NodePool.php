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

class NodePool extends \Google\Collection
{
  /**
   * Not set.
   */
  public const STATUS_STATUS_UNSPECIFIED = 'STATUS_UNSPECIFIED';
  /**
   * The PROVISIONING state indicates the node pool is being created.
   */
  public const STATUS_PROVISIONING = 'PROVISIONING';
  /**
   * The RUNNING state indicates the node pool has been created and is fully
   * usable.
   */
  public const STATUS_RUNNING = 'RUNNING';
  /**
   * The RUNNING_WITH_ERROR state indicates the node pool has been created and
   * is partially usable. Some error state has occurred and some functionality
   * may be impaired. Customer may need to reissue a request or trigger a new
   * update.
   */
  public const STATUS_RUNNING_WITH_ERROR = 'RUNNING_WITH_ERROR';
  /**
   * The RECONCILING state indicates that some work is actively being done on
   * the node pool, such as upgrading node software. Details can be found in the
   * `statusMessage` field.
   */
  public const STATUS_RECONCILING = 'RECONCILING';
  /**
   * The STOPPING state indicates the node pool is being deleted.
   */
  public const STATUS_STOPPING = 'STOPPING';
  /**
   * The ERROR state indicates the node pool may be unusable. Details can be
   * found in the `statusMessage` field.
   */
  public const STATUS_ERROR = 'ERROR';
  protected $collection_key = 'locations';
  protected $autopilotConfigType = AutopilotConfig::class;
  protected $autopilotConfigDataType = '';
  protected $autoscalingType = NodePoolAutoscaling::class;
  protected $autoscalingDataType = '';
  protected $bestEffortProvisioningType = BestEffortProvisioning::class;
  protected $bestEffortProvisioningDataType = '';
  protected $conditionsType = StatusCondition::class;
  protected $conditionsDataType = 'array';
  protected $configType = NodeConfig::class;
  protected $configDataType = '';
  /**
   * This checksum is computed by the server based on the value of node pool
   * fields, and may be sent on update requests to ensure the client has an up-
   * to-date value before proceeding.
   *
   * @var string
   */
  public $etag;
  /**
   * The initial node count for the pool. You must ensure that your Compute
   * Engine [resource quota](https://cloud.google.com/compute/quotas) is
   * sufficient for this number of instances. You must also have available
   * firewall and routes quota.
   *
   * @var int
   */
  public $initialNodeCount;
  /**
   * Output only. The resource URLs of the [managed instance
   * groups](https://cloud.google.com/compute/docs/instance-groups/creating-
   * groups-of-managed-instances) associated with this node pool. During the
   * node pool blue-green upgrade operation, the URLs contain both blue and
   * green resources.
   *
   * @var string[]
   */
  public $instanceGroupUrls;
  /**
   * The list of Google Compute Engine
   * [zones](https://cloud.google.com/compute/docs/zones#available) in which the
   * NodePool's nodes should be located. If this value is unspecified during
   * node pool creation, the
   * [Cluster.Locations](https://cloud.google.com/kubernetes-engine/docs/referen
   * ce/rest/v1/projects.locations.clusters#Cluster.FIELDS.locations) value will
   * be used, instead. Warning: changing node pool locations will result in
   * nodes being added and/or removed.
   *
   * @var string[]
   */
  public $locations;
  protected $managementType = NodeManagement::class;
  protected $managementDataType = '';
  protected $maxPodsConstraintType = MaxPodsConstraint::class;
  protected $maxPodsConstraintDataType = '';
  /**
   * The name of the node pool.
   *
   * @var string
   */
  public $name;
  protected $networkConfigType = NodeNetworkConfig::class;
  protected $networkConfigDataType = '';
  protected $nodeDrainConfigType = NodeDrainConfig::class;
  protected $nodeDrainConfigDataType = '';
  protected $placementPolicyType = PlacementPolicy::class;
  protected $placementPolicyDataType = '';
  /**
   * Output only. The pod CIDR block size per node in this node pool.
   *
   * @var int
   */
  public $podIpv4CidrSize;
  protected $queuedProvisioningType = QueuedProvisioning::class;
  protected $queuedProvisioningDataType = '';
  /**
   * Output only. Server-defined URL for the resource.
   *
   * @var string
   */
  public $selfLink;
  /**
   * Output only. The status of the nodes in this pool instance.
   *
   * @var string
   */
  public $status;
  /**
   * Output only. Deprecated. Use conditions instead. Additional information
   * about the current status of this node pool instance, if available.
   *
   * @deprecated
   * @var string
   */
  public $statusMessage;
  protected $updateInfoType = UpdateInfo::class;
  protected $updateInfoDataType = '';
  protected $upgradeSettingsType = UpgradeSettings::class;
  protected $upgradeSettingsDataType = '';
  /**
   * The version of Kubernetes running on this NodePool's nodes. If unspecified,
   * it defaults as described [here](https://cloud.google.com/kubernetes-
   * engine/versioning#specifying_node_version).
   *
   * @var string
   */
  public $version;

  /**
   * Specifies the autopilot configuration for this node pool. This field is
   * exclusively reserved for Cluster Autoscaler.
   *
   * @param AutopilotConfig $autopilotConfig
   */
  public function setAutopilotConfig(AutopilotConfig $autopilotConfig)
  {
    $this->autopilotConfig = $autopilotConfig;
  }
  /**
   * @return AutopilotConfig
   */
  public function getAutopilotConfig()
  {
    return $this->autopilotConfig;
  }
  /**
   * Autoscaler configuration for this NodePool. Autoscaler is enabled only if a
   * valid configuration is present.
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
   * Enable best effort provisioning for nodes
   *
   * @param BestEffortProvisioning $bestEffortProvisioning
   */
  public function setBestEffortProvisioning(BestEffortProvisioning $bestEffortProvisioning)
  {
    $this->bestEffortProvisioning = $bestEffortProvisioning;
  }
  /**
   * @return BestEffortProvisioning
   */
  public function getBestEffortProvisioning()
  {
    return $this->bestEffortProvisioning;
  }
  /**
   * Which conditions caused the current node pool state.
   *
   * @param StatusCondition[] $conditions
   */
  public function setConditions($conditions)
  {
    $this->conditions = $conditions;
  }
  /**
   * @return StatusCondition[]
   */
  public function getConditions()
  {
    return $this->conditions;
  }
  /**
   * The node configuration of the pool.
   *
   * @param NodeConfig $config
   */
  public function setConfig(NodeConfig $config)
  {
    $this->config = $config;
  }
  /**
   * @return NodeConfig
   */
  public function getConfig()
  {
    return $this->config;
  }
  /**
   * This checksum is computed by the server based on the value of node pool
   * fields, and may be sent on update requests to ensure the client has an up-
   * to-date value before proceeding.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * The initial node count for the pool. You must ensure that your Compute
   * Engine [resource quota](https://cloud.google.com/compute/quotas) is
   * sufficient for this number of instances. You must also have available
   * firewall and routes quota.
   *
   * @param int $initialNodeCount
   */
  public function setInitialNodeCount($initialNodeCount)
  {
    $this->initialNodeCount = $initialNodeCount;
  }
  /**
   * @return int
   */
  public function getInitialNodeCount()
  {
    return $this->initialNodeCount;
  }
  /**
   * Output only. The resource URLs of the [managed instance
   * groups](https://cloud.google.com/compute/docs/instance-groups/creating-
   * groups-of-managed-instances) associated with this node pool. During the
   * node pool blue-green upgrade operation, the URLs contain both blue and
   * green resources.
   *
   * @param string[] $instanceGroupUrls
   */
  public function setInstanceGroupUrls($instanceGroupUrls)
  {
    $this->instanceGroupUrls = $instanceGroupUrls;
  }
  /**
   * @return string[]
   */
  public function getInstanceGroupUrls()
  {
    return $this->instanceGroupUrls;
  }
  /**
   * The list of Google Compute Engine
   * [zones](https://cloud.google.com/compute/docs/zones#available) in which the
   * NodePool's nodes should be located. If this value is unspecified during
   * node pool creation, the
   * [Cluster.Locations](https://cloud.google.com/kubernetes-engine/docs/referen
   * ce/rest/v1/projects.locations.clusters#Cluster.FIELDS.locations) value will
   * be used, instead. Warning: changing node pool locations will result in
   * nodes being added and/or removed.
   *
   * @param string[] $locations
   */
  public function setLocations($locations)
  {
    $this->locations = $locations;
  }
  /**
   * @return string[]
   */
  public function getLocations()
  {
    return $this->locations;
  }
  /**
   * NodeManagement configuration for this NodePool.
   *
   * @param NodeManagement $management
   */
  public function setManagement(NodeManagement $management)
  {
    $this->management = $management;
  }
  /**
   * @return NodeManagement
   */
  public function getManagement()
  {
    return $this->management;
  }
  /**
   * The constraint on the maximum number of pods that can be run simultaneously
   * on a node in the node pool.
   *
   * @param MaxPodsConstraint $maxPodsConstraint
   */
  public function setMaxPodsConstraint(MaxPodsConstraint $maxPodsConstraint)
  {
    $this->maxPodsConstraint = $maxPodsConstraint;
  }
  /**
   * @return MaxPodsConstraint
   */
  public function getMaxPodsConstraint()
  {
    return $this->maxPodsConstraint;
  }
  /**
   * The name of the node pool.
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
   * Networking configuration for this NodePool. If specified, it overrides the
   * cluster-level defaults.
   *
   * @param NodeNetworkConfig $networkConfig
   */
  public function setNetworkConfig(NodeNetworkConfig $networkConfig)
  {
    $this->networkConfig = $networkConfig;
  }
  /**
   * @return NodeNetworkConfig
   */
  public function getNetworkConfig()
  {
    return $this->networkConfig;
  }
  /**
   * Specifies the node drain configuration for this node pool.
   *
   * @param NodeDrainConfig $nodeDrainConfig
   */
  public function setNodeDrainConfig(NodeDrainConfig $nodeDrainConfig)
  {
    $this->nodeDrainConfig = $nodeDrainConfig;
  }
  /**
   * @return NodeDrainConfig
   */
  public function getNodeDrainConfig()
  {
    return $this->nodeDrainConfig;
  }
  /**
   * Specifies the node placement policy.
   *
   * @param PlacementPolicy $placementPolicy
   */
  public function setPlacementPolicy(PlacementPolicy $placementPolicy)
  {
    $this->placementPolicy = $placementPolicy;
  }
  /**
   * @return PlacementPolicy
   */
  public function getPlacementPolicy()
  {
    return $this->placementPolicy;
  }
  /**
   * Output only. The pod CIDR block size per node in this node pool.
   *
   * @param int $podIpv4CidrSize
   */
  public function setPodIpv4CidrSize($podIpv4CidrSize)
  {
    $this->podIpv4CidrSize = $podIpv4CidrSize;
  }
  /**
   * @return int
   */
  public function getPodIpv4CidrSize()
  {
    return $this->podIpv4CidrSize;
  }
  /**
   * Specifies the configuration of queued provisioning.
   *
   * @param QueuedProvisioning $queuedProvisioning
   */
  public function setQueuedProvisioning(QueuedProvisioning $queuedProvisioning)
  {
    $this->queuedProvisioning = $queuedProvisioning;
  }
  /**
   * @return QueuedProvisioning
   */
  public function getQueuedProvisioning()
  {
    return $this->queuedProvisioning;
  }
  /**
   * Output only. Server-defined URL for the resource.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * Output only. The status of the nodes in this pool instance.
   *
   * Accepted values: STATUS_UNSPECIFIED, PROVISIONING, RUNNING,
   * RUNNING_WITH_ERROR, RECONCILING, STOPPING, ERROR
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Output only. Deprecated. Use conditions instead. Additional information
   * about the current status of this node pool instance, if available.
   *
   * @deprecated
   * @param string $statusMessage
   */
  public function setStatusMessage($statusMessage)
  {
    $this->statusMessage = $statusMessage;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getStatusMessage()
  {
    return $this->statusMessage;
  }
  /**
   * Output only. Update info contains relevant information during a node pool
   * update.
   *
   * @param UpdateInfo $updateInfo
   */
  public function setUpdateInfo(UpdateInfo $updateInfo)
  {
    $this->updateInfo = $updateInfo;
  }
  /**
   * @return UpdateInfo
   */
  public function getUpdateInfo()
  {
    return $this->updateInfo;
  }
  /**
   * Upgrade settings control disruption and speed of the upgrade.
   *
   * @param UpgradeSettings $upgradeSettings
   */
  public function setUpgradeSettings(UpgradeSettings $upgradeSettings)
  {
    $this->upgradeSettings = $upgradeSettings;
  }
  /**
   * @return UpgradeSettings
   */
  public function getUpgradeSettings()
  {
    return $this->upgradeSettings;
  }
  /**
   * The version of Kubernetes running on this NodePool's nodes. If unspecified,
   * it defaults as described [here](https://cloud.google.com/kubernetes-
   * engine/versioning#specifying_node_version).
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NodePool::class, 'Google_Service_Container_NodePool');
