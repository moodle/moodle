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

class UpdateNodePoolRequest extends \Google\Collection
{
  protected $collection_key = 'storagePools';
  protected $acceleratorsType = AcceleratorConfig::class;
  protected $acceleratorsDataType = 'array';
  protected $bootDiskType = BootDisk::class;
  protected $bootDiskDataType = '';
  /**
   * Deprecated. The name of the cluster to upgrade. This field has been
   * deprecated and replaced by the name field.
   *
   * @deprecated
   * @var string
   */
  public $clusterId;
  protected $confidentialNodesType = ConfidentialNodes::class;
  protected $confidentialNodesDataType = '';
  protected $containerdConfigType = ContainerdConfig::class;
  protected $containerdConfigDataType = '';
  /**
   * Optional. The desired disk size for nodes in the node pool specified in GB.
   * The smallest allowed disk size is 10GB. Initiates an upgrade operation that
   * migrates the nodes in the node pool to the specified disk size.
   *
   * @var string
   */
  public $diskSizeGb;
  /**
   * Optional. The desired disk type (e.g. 'pd-standard', 'pd-ssd' or 'pd-
   * balanced') for nodes in the node pool. Initiates an upgrade operation that
   * migrates the nodes in the node pool to the specified disk type.
   *
   * @var string
   */
  public $diskType;
  /**
   * The current etag of the node pool. If an etag is provided and does not
   * match the current etag of the node pool, update will be blocked and an
   * ABORTED error will be returned.
   *
   * @var string
   */
  public $etag;
  protected $fastSocketType = FastSocket::class;
  protected $fastSocketDataType = '';
  /**
   * Flex Start flag for enabling Flex Start VM.
   *
   * @var bool
   */
  public $flexStart;
  protected $gcfsConfigType = GcfsConfig::class;
  protected $gcfsConfigDataType = '';
  protected $gvnicType = VirtualNIC::class;
  protected $gvnicDataType = '';
  /**
   * Required. The desired image type for the node pool. Please see
   * https://cloud.google.com/kubernetes-engine/docs/concepts/node-images for
   * available image types.
   *
   * @var string
   */
  public $imageType;
  protected $kubeletConfigType = NodeKubeletConfig::class;
  protected $kubeletConfigDataType = '';
  protected $labelsType = NodeLabels::class;
  protected $labelsDataType = '';
  protected $linuxNodeConfigType = LinuxNodeConfig::class;
  protected $linuxNodeConfigDataType = '';
  /**
   * The desired list of Google Compute Engine
   * [zones](https://cloud.google.com/compute/docs/zones#available) in which the
   * node pool's nodes should be located. Changing the locations for a node pool
   * will result in nodes being either created or removed from the node pool,
   * depending on whether locations are being added or removed. Warning: It is
   * recommended to update node pool locations in a standalone API call. Do not
   * combine a location update with changes to other fields (such as `tags`,
   * `labels`, `taints`, etc.) in the same request. Otherwise, the API performs
   * a structural modification where changes to other fields will only apply to
   * newly created nodes and will not be applied to existing nodes in the node
   * pool. To ensure all nodes are updated consistently, use a separate API call
   * for location changes.
   *
   * @var string[]
   */
  public $locations;
  protected $loggingConfigType = NodePoolLoggingConfig::class;
  protected $loggingConfigDataType = '';
  /**
   * Optional. The desired [Google Compute Engine machine
   * type](https://cloud.google.com/compute/docs/machine-types) for nodes in the
   * node pool. Initiates an upgrade operation that migrates the nodes in the
   * node pool to the specified machine type.
   *
   * @var string
   */
  public $machineType;
  /**
   * The maximum duration for the nodes to exist. If unspecified, the nodes can
   * exist indefinitely.
   *
   * @var string
   */
  public $maxRunDuration;
  /**
   * The name (project, location, cluster, node pool) of the node pool to
   * update. Specified in the format `projects/locations/clusters/nodePools`.
   *
   * @var string
   */
  public $name;
  protected $nodeDrainConfigType = NodeDrainConfig::class;
  protected $nodeDrainConfigDataType = '';
  protected $nodeNetworkConfigType = NodeNetworkConfig::class;
  protected $nodeNetworkConfigDataType = '';
  /**
   * Deprecated. The name of the node pool to upgrade. This field has been
   * deprecated and replaced by the name field.
   *
   * @deprecated
   * @var string
   */
  public $nodePoolId;
  /**
   * Required. The Kubernetes version to change the nodes to (typically an
   * upgrade). Users may specify either explicit versions offered by Kubernetes
   * Engine or version aliases, which have the following behavior: - "latest":
   * picks the highest valid Kubernetes version - "1.X": picks the highest valid
   * patch+gke.N patch in the 1.X version - "1.X.Y": picks the highest valid
   * gke.N patch in the 1.X.Y version - "1.X.Y-gke.N": picks an explicit
   * Kubernetes version - "-": picks the Kubernetes master version
   *
   * @var string
   */
  public $nodeVersion;
  /**
   * Deprecated. The Google Developers Console [project ID or project
   * number](https://cloud.google.com/resource-manager/docs/creating-managing-
   * projects). This field has been deprecated and replaced by the name field.
   *
   * @deprecated
   * @var string
   */
  public $projectId;
  protected $queuedProvisioningType = QueuedProvisioning::class;
  protected $queuedProvisioningDataType = '';
  protected $resourceLabelsType = ResourceLabels::class;
  protected $resourceLabelsDataType = '';
  protected $resourceManagerTagsType = ResourceManagerTags::class;
  protected $resourceManagerTagsDataType = '';
  /**
   * List of Storage Pools where boot disks are provisioned. Existing Storage
   * Pools will be replaced with storage-pools.
   *
   * @var string[]
   */
  public $storagePools;
  protected $tagsType = NetworkTags::class;
  protected $tagsDataType = '';
  protected $taintsType = NodeTaints::class;
  protected $taintsDataType = '';
  protected $upgradeSettingsType = UpgradeSettings::class;
  protected $upgradeSettingsDataType = '';
  protected $windowsNodeConfigType = WindowsNodeConfig::class;
  protected $windowsNodeConfigDataType = '';
  protected $workloadMetadataConfigType = WorkloadMetadataConfig::class;
  protected $workloadMetadataConfigDataType = '';
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
   * A list of hardware accelerators to be attached to each node. See
   * https://cloud.google.com/compute/docs/gpus for more information about
   * support for GPUs.
   *
   * @param AcceleratorConfig[] $accelerators
   */
  public function setAccelerators($accelerators)
  {
    $this->accelerators = $accelerators;
  }
  /**
   * @return AcceleratorConfig[]
   */
  public function getAccelerators()
  {
    return $this->accelerators;
  }
  /**
   * The desired boot disk config for nodes in the node pool. Initiates an
   * upgrade operation that migrates the nodes in the node pool to the specified
   * boot disk config.
   *
   * @param BootDisk $bootDisk
   */
  public function setBootDisk(BootDisk $bootDisk)
  {
    $this->bootDisk = $bootDisk;
  }
  /**
   * @return BootDisk
   */
  public function getBootDisk()
  {
    return $this->bootDisk;
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
   * Confidential nodes config. All the nodes in the node pool will be
   * Confidential VM once enabled.
   *
   * @param ConfidentialNodes $confidentialNodes
   */
  public function setConfidentialNodes(ConfidentialNodes $confidentialNodes)
  {
    $this->confidentialNodes = $confidentialNodes;
  }
  /**
   * @return ConfidentialNodes
   */
  public function getConfidentialNodes()
  {
    return $this->confidentialNodes;
  }
  /**
   * The desired containerd config for nodes in the node pool. Initiates an
   * upgrade operation that recreates the nodes with the new config.
   *
   * @param ContainerdConfig $containerdConfig
   */
  public function setContainerdConfig(ContainerdConfig $containerdConfig)
  {
    $this->containerdConfig = $containerdConfig;
  }
  /**
   * @return ContainerdConfig
   */
  public function getContainerdConfig()
  {
    return $this->containerdConfig;
  }
  /**
   * Optional. The desired disk size for nodes in the node pool specified in GB.
   * The smallest allowed disk size is 10GB. Initiates an upgrade operation that
   * migrates the nodes in the node pool to the specified disk size.
   *
   * @param string $diskSizeGb
   */
  public function setDiskSizeGb($diskSizeGb)
  {
    $this->diskSizeGb = $diskSizeGb;
  }
  /**
   * @return string
   */
  public function getDiskSizeGb()
  {
    return $this->diskSizeGb;
  }
  /**
   * Optional. The desired disk type (e.g. 'pd-standard', 'pd-ssd' or 'pd-
   * balanced') for nodes in the node pool. Initiates an upgrade operation that
   * migrates the nodes in the node pool to the specified disk type.
   *
   * @param string $diskType
   */
  public function setDiskType($diskType)
  {
    $this->diskType = $diskType;
  }
  /**
   * @return string
   */
  public function getDiskType()
  {
    return $this->diskType;
  }
  /**
   * The current etag of the node pool. If an etag is provided and does not
   * match the current etag of the node pool, update will be blocked and an
   * ABORTED error will be returned.
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
   * Enable or disable NCCL fast socket for the node pool.
   *
   * @param FastSocket $fastSocket
   */
  public function setFastSocket(FastSocket $fastSocket)
  {
    $this->fastSocket = $fastSocket;
  }
  /**
   * @return FastSocket
   */
  public function getFastSocket()
  {
    return $this->fastSocket;
  }
  /**
   * Flex Start flag for enabling Flex Start VM.
   *
   * @param bool $flexStart
   */
  public function setFlexStart($flexStart)
  {
    $this->flexStart = $flexStart;
  }
  /**
   * @return bool
   */
  public function getFlexStart()
  {
    return $this->flexStart;
  }
  /**
   * GCFS config.
   *
   * @param GcfsConfig $gcfsConfig
   */
  public function setGcfsConfig(GcfsConfig $gcfsConfig)
  {
    $this->gcfsConfig = $gcfsConfig;
  }
  /**
   * @return GcfsConfig
   */
  public function getGcfsConfig()
  {
    return $this->gcfsConfig;
  }
  /**
   * Enable or disable gvnic on the node pool.
   *
   * @param VirtualNIC $gvnic
   */
  public function setGvnic(VirtualNIC $gvnic)
  {
    $this->gvnic = $gvnic;
  }
  /**
   * @return VirtualNIC
   */
  public function getGvnic()
  {
    return $this->gvnic;
  }
  /**
   * Required. The desired image type for the node pool. Please see
   * https://cloud.google.com/kubernetes-engine/docs/concepts/node-images for
   * available image types.
   *
   * @param string $imageType
   */
  public function setImageType($imageType)
  {
    $this->imageType = $imageType;
  }
  /**
   * @return string
   */
  public function getImageType()
  {
    return $this->imageType;
  }
  /**
   * Node kubelet configs.
   *
   * @param NodeKubeletConfig $kubeletConfig
   */
  public function setKubeletConfig(NodeKubeletConfig $kubeletConfig)
  {
    $this->kubeletConfig = $kubeletConfig;
  }
  /**
   * @return NodeKubeletConfig
   */
  public function getKubeletConfig()
  {
    return $this->kubeletConfig;
  }
  /**
   * The desired node labels to be applied to all nodes in the node pool. If
   * this field is not present, the labels will not be changed. Otherwise, the
   * existing node labels will be *replaced* with the provided labels.
   *
   * @param NodeLabels $labels
   */
  public function setLabels(NodeLabels $labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return NodeLabels
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Parameters that can be configured on Linux nodes.
   *
   * @param LinuxNodeConfig $linuxNodeConfig
   */
  public function setLinuxNodeConfig(LinuxNodeConfig $linuxNodeConfig)
  {
    $this->linuxNodeConfig = $linuxNodeConfig;
  }
  /**
   * @return LinuxNodeConfig
   */
  public function getLinuxNodeConfig()
  {
    return $this->linuxNodeConfig;
  }
  /**
   * The desired list of Google Compute Engine
   * [zones](https://cloud.google.com/compute/docs/zones#available) in which the
   * node pool's nodes should be located. Changing the locations for a node pool
   * will result in nodes being either created or removed from the node pool,
   * depending on whether locations are being added or removed. Warning: It is
   * recommended to update node pool locations in a standalone API call. Do not
   * combine a location update with changes to other fields (such as `tags`,
   * `labels`, `taints`, etc.) in the same request. Otherwise, the API performs
   * a structural modification where changes to other fields will only apply to
   * newly created nodes and will not be applied to existing nodes in the node
   * pool. To ensure all nodes are updated consistently, use a separate API call
   * for location changes.
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
   * Logging configuration.
   *
   * @param NodePoolLoggingConfig $loggingConfig
   */
  public function setLoggingConfig(NodePoolLoggingConfig $loggingConfig)
  {
    $this->loggingConfig = $loggingConfig;
  }
  /**
   * @return NodePoolLoggingConfig
   */
  public function getLoggingConfig()
  {
    return $this->loggingConfig;
  }
  /**
   * Optional. The desired [Google Compute Engine machine
   * type](https://cloud.google.com/compute/docs/machine-types) for nodes in the
   * node pool. Initiates an upgrade operation that migrates the nodes in the
   * node pool to the specified machine type.
   *
   * @param string $machineType
   */
  public function setMachineType($machineType)
  {
    $this->machineType = $machineType;
  }
  /**
   * @return string
   */
  public function getMachineType()
  {
    return $this->machineType;
  }
  /**
   * The maximum duration for the nodes to exist. If unspecified, the nodes can
   * exist indefinitely.
   *
   * @param string $maxRunDuration
   */
  public function setMaxRunDuration($maxRunDuration)
  {
    $this->maxRunDuration = $maxRunDuration;
  }
  /**
   * @return string
   */
  public function getMaxRunDuration()
  {
    return $this->maxRunDuration;
  }
  /**
   * The name (project, location, cluster, node pool) of the node pool to
   * update. Specified in the format `projects/locations/clusters/nodePools`.
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
   * The desired node drain configuration for nodes in the node pool.
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
   * Node network config.
   *
   * @param NodeNetworkConfig $nodeNetworkConfig
   */
  public function setNodeNetworkConfig(NodeNetworkConfig $nodeNetworkConfig)
  {
    $this->nodeNetworkConfig = $nodeNetworkConfig;
  }
  /**
   * @return NodeNetworkConfig
   */
  public function getNodeNetworkConfig()
  {
    return $this->nodeNetworkConfig;
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
   * Required. The Kubernetes version to change the nodes to (typically an
   * upgrade). Users may specify either explicit versions offered by Kubernetes
   * Engine or version aliases, which have the following behavior: - "latest":
   * picks the highest valid Kubernetes version - "1.X": picks the highest valid
   * patch+gke.N patch in the 1.X version - "1.X.Y": picks the highest valid
   * gke.N patch in the 1.X.Y version - "1.X.Y-gke.N": picks an explicit
   * Kubernetes version - "-": picks the Kubernetes master version
   *
   * @param string $nodeVersion
   */
  public function setNodeVersion($nodeVersion)
  {
    $this->nodeVersion = $nodeVersion;
  }
  /**
   * @return string
   */
  public function getNodeVersion()
  {
    return $this->nodeVersion;
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
   * The resource labels for the node pool to use to annotate any related Google
   * Compute Engine resources.
   *
   * @param ResourceLabels $resourceLabels
   */
  public function setResourceLabels(ResourceLabels $resourceLabels)
  {
    $this->resourceLabels = $resourceLabels;
  }
  /**
   * @return ResourceLabels
   */
  public function getResourceLabels()
  {
    return $this->resourceLabels;
  }
  /**
   * Desired resource manager tag keys and values to be attached to the nodes
   * for managing Compute Engine firewalls using Network Firewall Policies.
   * Existing tags will be replaced with new values.
   *
   * @param ResourceManagerTags $resourceManagerTags
   */
  public function setResourceManagerTags(ResourceManagerTags $resourceManagerTags)
  {
    $this->resourceManagerTags = $resourceManagerTags;
  }
  /**
   * @return ResourceManagerTags
   */
  public function getResourceManagerTags()
  {
    return $this->resourceManagerTags;
  }
  /**
   * List of Storage Pools where boot disks are provisioned. Existing Storage
   * Pools will be replaced with storage-pools.
   *
   * @param string[] $storagePools
   */
  public function setStoragePools($storagePools)
  {
    $this->storagePools = $storagePools;
  }
  /**
   * @return string[]
   */
  public function getStoragePools()
  {
    return $this->storagePools;
  }
  /**
   * The desired network tags to be applied to all nodes in the node pool. If
   * this field is not present, the tags will not be changed. Otherwise, the
   * existing network tags will be *replaced* with the provided tags.
   *
   * @param NetworkTags $tags
   */
  public function setTags(NetworkTags $tags)
  {
    $this->tags = $tags;
  }
  /**
   * @return NetworkTags
   */
  public function getTags()
  {
    return $this->tags;
  }
  /**
   * The desired node taints to be applied to all nodes in the node pool. If
   * this field is not present, the taints will not be changed. Otherwise, the
   * existing node taints will be *replaced* with the provided taints.
   *
   * @param NodeTaints $taints
   */
  public function setTaints(NodeTaints $taints)
  {
    $this->taints = $taints;
  }
  /**
   * @return NodeTaints
   */
  public function getTaints()
  {
    return $this->taints;
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
   * Parameters that can be configured on Windows nodes.
   *
   * @param WindowsNodeConfig $windowsNodeConfig
   */
  public function setWindowsNodeConfig(WindowsNodeConfig $windowsNodeConfig)
  {
    $this->windowsNodeConfig = $windowsNodeConfig;
  }
  /**
   * @return WindowsNodeConfig
   */
  public function getWindowsNodeConfig()
  {
    return $this->windowsNodeConfig;
  }
  /**
   * The desired workload metadata config for the node pool.
   *
   * @param WorkloadMetadataConfig $workloadMetadataConfig
   */
  public function setWorkloadMetadataConfig(WorkloadMetadataConfig $workloadMetadataConfig)
  {
    $this->workloadMetadataConfig = $workloadMetadataConfig;
  }
  /**
   * @return WorkloadMetadataConfig
   */
  public function getWorkloadMetadataConfig()
  {
    return $this->workloadMetadataConfig;
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
class_alias(UpdateNodePoolRequest::class, 'Google_Service_Container_UpdateNodePoolRequest');
