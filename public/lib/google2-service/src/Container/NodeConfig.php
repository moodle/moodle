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

class NodeConfig extends \Google\Collection
{
  /**
   * EFFECTIVE_CGROUP_MODE_UNSPECIFIED means the cgroup configuration for the
   * node pool is unspecified, i.e. the node pool is a Windows node pool.
   */
  public const EFFECTIVE_CGROUP_MODE_EFFECTIVE_CGROUP_MODE_UNSPECIFIED = 'EFFECTIVE_CGROUP_MODE_UNSPECIFIED';
  /**
   * CGROUP_MODE_V1 means the node pool is configured to use cgroupv1 for the
   * cgroup configuration.
   */
  public const EFFECTIVE_CGROUP_MODE_EFFECTIVE_CGROUP_MODE_V1 = 'EFFECTIVE_CGROUP_MODE_V1';
  /**
   * CGROUP_MODE_V2 means the node pool is configured to use cgroupv2 for the
   * cgroup configuration.
   */
  public const EFFECTIVE_CGROUP_MODE_EFFECTIVE_CGROUP_MODE_V2 = 'EFFECTIVE_CGROUP_MODE_V2';
  /**
   * The given node will be encrypted using keys managed by Google
   * infrastructure and the keys will be deleted when the node is deleted.
   */
  public const LOCAL_SSD_ENCRYPTION_MODE_LOCAL_SSD_ENCRYPTION_MODE_UNSPECIFIED = 'LOCAL_SSD_ENCRYPTION_MODE_UNSPECIFIED';
  /**
   * The given node will be encrypted using keys managed by Google
   * infrastructure and the keys will be deleted when the node is deleted.
   */
  public const LOCAL_SSD_ENCRYPTION_MODE_STANDARD_ENCRYPTION = 'STANDARD_ENCRYPTION';
  /**
   * The given node will opt-in for using ephemeral key for encryption of Local
   * SSDs. The Local SSDs will not be able to recover data in case of node
   * crash.
   */
  public const LOCAL_SSD_ENCRYPTION_MODE_EPHEMERAL_KEY_ENCRYPTION = 'EPHEMERAL_KEY_ENCRYPTION';
  protected $collection_key = 'taints';
  protected $acceleratorsType = AcceleratorConfig::class;
  protected $acceleratorsDataType = 'array';
  protected $advancedMachineFeaturesType = AdvancedMachineFeatures::class;
  protected $advancedMachineFeaturesDataType = '';
  protected $bootDiskType = BootDisk::class;
  protected $bootDiskDataType = '';
  /**
   * The Customer Managed Encryption Key used to encrypt the boot disk attached
   * to each node in the node pool. This should be of the form projects/[KEY_PRO
   * JECT_ID]/locations/[LOCATION]/keyRings/[RING_NAME]/cryptoKeys/[KEY_NAME].
   * For more information about protecting resources with Cloud KMS Keys please
   * see: https://cloud.google.com/compute/docs/disks/customer-managed-
   * encryption
   *
   * @var string
   */
  public $bootDiskKmsKey;
  protected $confidentialNodesType = ConfidentialNodes::class;
  protected $confidentialNodesDataType = '';
  protected $containerdConfigType = ContainerdConfig::class;
  protected $containerdConfigDataType = '';
  /**
   * Size of the disk attached to each node, specified in GB. The smallest
   * allowed disk size is 10GB. If unspecified, the default disk size is 100GB.
   *
   * @var int
   */
  public $diskSizeGb;
  /**
   * Type of the disk attached to each node (e.g. 'pd-standard', 'pd-ssd' or
   * 'pd-balanced') If unspecified, the default disk type is 'pd-standard'
   *
   * @var string
   */
  public $diskType;
  /**
   * Output only. effective_cgroup_mode is the cgroup mode actually used by the
   * node pool. It is determined by the cgroup mode specified in the
   * LinuxNodeConfig or the default cgroup mode based on the cluster creation
   * version.
   *
   * @var string
   */
  public $effectiveCgroupMode;
  /**
   * Optional. Reserved for future use.
   *
   * @var bool
   */
  public $enableConfidentialStorage;
  protected $ephemeralStorageLocalSsdConfigType = EphemeralStorageLocalSsdConfig::class;
  protected $ephemeralStorageLocalSsdConfigDataType = '';
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
   * The image type to use for this node. Note that for a given image type, the
   * latest version of it will be used. Please see
   * https://cloud.google.com/kubernetes-engine/docs/concepts/node-images for
   * available image types.
   *
   * @var string
   */
  public $imageType;
  protected $kubeletConfigType = NodeKubeletConfig::class;
  protected $kubeletConfigDataType = '';
  /**
   * The map of Kubernetes labels (key/value pairs) to be applied to each node.
   * These will added in addition to any default label(s) that Kubernetes may
   * apply to the node. In case of conflict in label keys, the applied set may
   * differ depending on the Kubernetes version -- it's best to assume the
   * behavior is undefined and conflicts should be avoided. For more
   * information, including usage and the valid values, see:
   * https://kubernetes.io/docs/concepts/overview/working-with-objects/labels/
   *
   * @var string[]
   */
  public $labels;
  protected $linuxNodeConfigType = LinuxNodeConfig::class;
  protected $linuxNodeConfigDataType = '';
  protected $localNvmeSsdBlockConfigType = LocalNvmeSsdBlockConfig::class;
  protected $localNvmeSsdBlockConfigDataType = '';
  /**
   * The number of local SSD disks to be attached to the node. The limit for
   * this value is dependent upon the maximum number of disks available on a
   * machine per zone. See: https://cloud.google.com/compute/docs/disks/local-
   * ssd for more information.
   *
   * @var int
   */
  public $localSsdCount;
  /**
   * Specifies which method should be used for encrypting the Local SSDs
   * attached to the node.
   *
   * @var string
   */
  public $localSsdEncryptionMode;
  protected $loggingConfigType = NodePoolLoggingConfig::class;
  protected $loggingConfigDataType = '';
  /**
   * The name of a Google Compute Engine [machine
   * type](https://cloud.google.com/compute/docs/machine-types) If unspecified,
   * the default machine type is `e2-medium`.
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
   * The metadata key/value pairs assigned to instances in the cluster. Keys
   * must conform to the regexp `[a-zA-Z0-9-_]+` and be less than 128 bytes in
   * length. These are reflected as part of a URL in the metadata server.
   * Additionally, to avoid ambiguity, keys must not conflict with any other
   * metadata keys for the project or be one of the reserved keys: - "cluster-
   * location" - "cluster-name" - "cluster-uid" - "configure-sh" - "containerd-
   * configure-sh" - "enable-os-login" - "gci-ensure-gke-docker" - "gci-metrics-
   * enabled" - "gci-update-strategy" - "instance-template" - "kube-env" -
   * "startup-script" - "user-data" - "disable-address-manager" - "windows-
   * startup-script-ps1" - "common-psm1" - "k8s-node-setup-psm1" - "install-ssh-
   * psm1" - "user-profile-psm1" Values are free-form strings, and only have
   * meaning as interpreted by the image running in the instance. The only
   * restriction placed on them is that each value's size must be less than or
   * equal to 32 KB. The total size of all keys and values must be less than 512
   * KB.
   *
   * @var string[]
   */
  public $metadata;
  /**
   * Minimum CPU platform to be used by this instance. The instance may be
   * scheduled on the specified or newer CPU platform. Applicable values are the
   * friendly names of CPU platforms, such as `minCpuPlatform: "Intel Haswell"`
   * or `minCpuPlatform: "Intel Sandy Bridge"`. For more information, read [how
   * to specify min CPU
   * platform](https://cloud.google.com/compute/docs/instances/specify-min-cpu-
   * platform)
   *
   * @var string
   */
  public $minCpuPlatform;
  /**
   * Setting this field will assign instances of this pool to run on the
   * specified node group. This is useful for running workloads on [sole tenant
   * nodes](https://cloud.google.com/compute/docs/nodes/sole-tenant-nodes).
   *
   * @var string
   */
  public $nodeGroup;
  /**
   * The set of Google API scopes to be made available on all of the node VMs
   * under the "default" service account. The following scopes are recommended,
   * but not required, and by default are not included: *
   * `https://www.googleapis.com/auth/compute` is required for mounting
   * persistent storage on your nodes. *
   * `https://www.googleapis.com/auth/devstorage.read_only` is required for
   * communicating with **gcr.io** (the [Artifact
   * Registry](https://cloud.google.com/artifact-registry/)). If unspecified, no
   * scopes are added, unless Cloud Logging or Cloud Monitoring are enabled, in
   * which case their required scopes will be added.
   *
   * @var string[]
   */
  public $oauthScopes;
  /**
   * Whether the nodes are created as preemptible VM instances. See:
   * https://cloud.google.com/compute/docs/instances/preemptible for more
   * information about preemptible VM instances.
   *
   * @var bool
   */
  public $preemptible;
  protected $reservationAffinityType = ReservationAffinity::class;
  protected $reservationAffinityDataType = '';
  /**
   * The resource labels for the node pool to use to annotate any related Google
   * Compute Engine resources.
   *
   * @var string[]
   */
  public $resourceLabels;
  protected $resourceManagerTagsType = ResourceManagerTags::class;
  protected $resourceManagerTagsDataType = '';
  protected $sandboxConfigType = SandboxConfig::class;
  protected $sandboxConfigDataType = '';
  protected $secondaryBootDiskUpdateStrategyType = SecondaryBootDiskUpdateStrategy::class;
  protected $secondaryBootDiskUpdateStrategyDataType = '';
  protected $secondaryBootDisksType = SecondaryBootDisk::class;
  protected $secondaryBootDisksDataType = 'array';
  /**
   * The Google Cloud Platform Service Account to be used by the node VMs.
   * Specify the email address of the Service Account; otherwise, if no Service
   * Account is specified, the "default" service account is used.
   *
   * @var string
   */
  public $serviceAccount;
  protected $shieldedInstanceConfigType = ShieldedInstanceConfig::class;
  protected $shieldedInstanceConfigDataType = '';
  protected $soleTenantConfigType = SoleTenantConfig::class;
  protected $soleTenantConfigDataType = '';
  /**
   * Spot flag for enabling Spot VM, which is a rebrand of the existing
   * preemptible flag.
   *
   * @var bool
   */
  public $spot;
  /**
   * List of Storage Pools where boot disks are provisioned.
   *
   * @var string[]
   */
  public $storagePools;
  /**
   * The list of instance tags applied to all nodes. Tags are used to identify
   * valid sources or targets for network firewalls and are specified by the
   * client during cluster or node pool creation. Each tag within the list must
   * comply with RFC1035.
   *
   * @var string[]
   */
  public $tags;
  protected $taintsType = NodeTaint::class;
  protected $taintsDataType = 'array';
  protected $windowsNodeConfigType = WindowsNodeConfig::class;
  protected $windowsNodeConfigDataType = '';
  protected $workloadMetadataConfigType = WorkloadMetadataConfig::class;
  protected $workloadMetadataConfigDataType = '';

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
   * Advanced features for the Compute Engine VM.
   *
   * @param AdvancedMachineFeatures $advancedMachineFeatures
   */
  public function setAdvancedMachineFeatures(AdvancedMachineFeatures $advancedMachineFeatures)
  {
    $this->advancedMachineFeatures = $advancedMachineFeatures;
  }
  /**
   * @return AdvancedMachineFeatures
   */
  public function getAdvancedMachineFeatures()
  {
    return $this->advancedMachineFeatures;
  }
  /**
   * The boot disk configuration for the node pool.
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
   * The Customer Managed Encryption Key used to encrypt the boot disk attached
   * to each node in the node pool. This should be of the form projects/[KEY_PRO
   * JECT_ID]/locations/[LOCATION]/keyRings/[RING_NAME]/cryptoKeys/[KEY_NAME].
   * For more information about protecting resources with Cloud KMS Keys please
   * see: https://cloud.google.com/compute/docs/disks/customer-managed-
   * encryption
   *
   * @param string $bootDiskKmsKey
   */
  public function setBootDiskKmsKey($bootDiskKmsKey)
  {
    $this->bootDiskKmsKey = $bootDiskKmsKey;
  }
  /**
   * @return string
   */
  public function getBootDiskKmsKey()
  {
    return $this->bootDiskKmsKey;
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
   * Parameters for containerd customization.
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
   * Size of the disk attached to each node, specified in GB. The smallest
   * allowed disk size is 10GB. If unspecified, the default disk size is 100GB.
   *
   * @param int $diskSizeGb
   */
  public function setDiskSizeGb($diskSizeGb)
  {
    $this->diskSizeGb = $diskSizeGb;
  }
  /**
   * @return int
   */
  public function getDiskSizeGb()
  {
    return $this->diskSizeGb;
  }
  /**
   * Type of the disk attached to each node (e.g. 'pd-standard', 'pd-ssd' or
   * 'pd-balanced') If unspecified, the default disk type is 'pd-standard'
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
   * Output only. effective_cgroup_mode is the cgroup mode actually used by the
   * node pool. It is determined by the cgroup mode specified in the
   * LinuxNodeConfig or the default cgroup mode based on the cluster creation
   * version.
   *
   * Accepted values: EFFECTIVE_CGROUP_MODE_UNSPECIFIED,
   * EFFECTIVE_CGROUP_MODE_V1, EFFECTIVE_CGROUP_MODE_V2
   *
   * @param self::EFFECTIVE_CGROUP_MODE_* $effectiveCgroupMode
   */
  public function setEffectiveCgroupMode($effectiveCgroupMode)
  {
    $this->effectiveCgroupMode = $effectiveCgroupMode;
  }
  /**
   * @return self::EFFECTIVE_CGROUP_MODE_*
   */
  public function getEffectiveCgroupMode()
  {
    return $this->effectiveCgroupMode;
  }
  /**
   * Optional. Reserved for future use.
   *
   * @param bool $enableConfidentialStorage
   */
  public function setEnableConfidentialStorage($enableConfidentialStorage)
  {
    $this->enableConfidentialStorage = $enableConfidentialStorage;
  }
  /**
   * @return bool
   */
  public function getEnableConfidentialStorage()
  {
    return $this->enableConfidentialStorage;
  }
  /**
   * Parameters for the node ephemeral storage using Local SSDs. If unspecified,
   * ephemeral storage is backed by the boot disk.
   *
   * @param EphemeralStorageLocalSsdConfig $ephemeralStorageLocalSsdConfig
   */
  public function setEphemeralStorageLocalSsdConfig(EphemeralStorageLocalSsdConfig $ephemeralStorageLocalSsdConfig)
  {
    $this->ephemeralStorageLocalSsdConfig = $ephemeralStorageLocalSsdConfig;
  }
  /**
   * @return EphemeralStorageLocalSsdConfig
   */
  public function getEphemeralStorageLocalSsdConfig()
  {
    return $this->ephemeralStorageLocalSsdConfig;
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
   * Google Container File System (image streaming) configs.
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
   * Enable or disable gvnic in the node pool.
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
   * The image type to use for this node. Note that for a given image type, the
   * latest version of it will be used. Please see
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
   * The map of Kubernetes labels (key/value pairs) to be applied to each node.
   * These will added in addition to any default label(s) that Kubernetes may
   * apply to the node. In case of conflict in label keys, the applied set may
   * differ depending on the Kubernetes version -- it's best to assume the
   * behavior is undefined and conflicts should be avoided. For more
   * information, including usage and the valid values, see:
   * https://kubernetes.io/docs/concepts/overview/working-with-objects/labels/
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
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
   * Parameters for using raw-block Local NVMe SSDs.
   *
   * @param LocalNvmeSsdBlockConfig $localNvmeSsdBlockConfig
   */
  public function setLocalNvmeSsdBlockConfig(LocalNvmeSsdBlockConfig $localNvmeSsdBlockConfig)
  {
    $this->localNvmeSsdBlockConfig = $localNvmeSsdBlockConfig;
  }
  /**
   * @return LocalNvmeSsdBlockConfig
   */
  public function getLocalNvmeSsdBlockConfig()
  {
    return $this->localNvmeSsdBlockConfig;
  }
  /**
   * The number of local SSD disks to be attached to the node. The limit for
   * this value is dependent upon the maximum number of disks available on a
   * machine per zone. See: https://cloud.google.com/compute/docs/disks/local-
   * ssd for more information.
   *
   * @param int $localSsdCount
   */
  public function setLocalSsdCount($localSsdCount)
  {
    $this->localSsdCount = $localSsdCount;
  }
  /**
   * @return int
   */
  public function getLocalSsdCount()
  {
    return $this->localSsdCount;
  }
  /**
   * Specifies which method should be used for encrypting the Local SSDs
   * attached to the node.
   *
   * Accepted values: LOCAL_SSD_ENCRYPTION_MODE_UNSPECIFIED,
   * STANDARD_ENCRYPTION, EPHEMERAL_KEY_ENCRYPTION
   *
   * @param self::LOCAL_SSD_ENCRYPTION_MODE_* $localSsdEncryptionMode
   */
  public function setLocalSsdEncryptionMode($localSsdEncryptionMode)
  {
    $this->localSsdEncryptionMode = $localSsdEncryptionMode;
  }
  /**
   * @return self::LOCAL_SSD_ENCRYPTION_MODE_*
   */
  public function getLocalSsdEncryptionMode()
  {
    return $this->localSsdEncryptionMode;
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
   * The name of a Google Compute Engine [machine
   * type](https://cloud.google.com/compute/docs/machine-types) If unspecified,
   * the default machine type is `e2-medium`.
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
   * The metadata key/value pairs assigned to instances in the cluster. Keys
   * must conform to the regexp `[a-zA-Z0-9-_]+` and be less than 128 bytes in
   * length. These are reflected as part of a URL in the metadata server.
   * Additionally, to avoid ambiguity, keys must not conflict with any other
   * metadata keys for the project or be one of the reserved keys: - "cluster-
   * location" - "cluster-name" - "cluster-uid" - "configure-sh" - "containerd-
   * configure-sh" - "enable-os-login" - "gci-ensure-gke-docker" - "gci-metrics-
   * enabled" - "gci-update-strategy" - "instance-template" - "kube-env" -
   * "startup-script" - "user-data" - "disable-address-manager" - "windows-
   * startup-script-ps1" - "common-psm1" - "k8s-node-setup-psm1" - "install-ssh-
   * psm1" - "user-profile-psm1" Values are free-form strings, and only have
   * meaning as interpreted by the image running in the instance. The only
   * restriction placed on them is that each value's size must be less than or
   * equal to 32 KB. The total size of all keys and values must be less than 512
   * KB.
   *
   * @param string[] $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return string[]
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Minimum CPU platform to be used by this instance. The instance may be
   * scheduled on the specified or newer CPU platform. Applicable values are the
   * friendly names of CPU platforms, such as `minCpuPlatform: "Intel Haswell"`
   * or `minCpuPlatform: "Intel Sandy Bridge"`. For more information, read [how
   * to specify min CPU
   * platform](https://cloud.google.com/compute/docs/instances/specify-min-cpu-
   * platform)
   *
   * @param string $minCpuPlatform
   */
  public function setMinCpuPlatform($minCpuPlatform)
  {
    $this->minCpuPlatform = $minCpuPlatform;
  }
  /**
   * @return string
   */
  public function getMinCpuPlatform()
  {
    return $this->minCpuPlatform;
  }
  /**
   * Setting this field will assign instances of this pool to run on the
   * specified node group. This is useful for running workloads on [sole tenant
   * nodes](https://cloud.google.com/compute/docs/nodes/sole-tenant-nodes).
   *
   * @param string $nodeGroup
   */
  public function setNodeGroup($nodeGroup)
  {
    $this->nodeGroup = $nodeGroup;
  }
  /**
   * @return string
   */
  public function getNodeGroup()
  {
    return $this->nodeGroup;
  }
  /**
   * The set of Google API scopes to be made available on all of the node VMs
   * under the "default" service account. The following scopes are recommended,
   * but not required, and by default are not included: *
   * `https://www.googleapis.com/auth/compute` is required for mounting
   * persistent storage on your nodes. *
   * `https://www.googleapis.com/auth/devstorage.read_only` is required for
   * communicating with **gcr.io** (the [Artifact
   * Registry](https://cloud.google.com/artifact-registry/)). If unspecified, no
   * scopes are added, unless Cloud Logging or Cloud Monitoring are enabled, in
   * which case their required scopes will be added.
   *
   * @param string[] $oauthScopes
   */
  public function setOauthScopes($oauthScopes)
  {
    $this->oauthScopes = $oauthScopes;
  }
  /**
   * @return string[]
   */
  public function getOauthScopes()
  {
    return $this->oauthScopes;
  }
  /**
   * Whether the nodes are created as preemptible VM instances. See:
   * https://cloud.google.com/compute/docs/instances/preemptible for more
   * information about preemptible VM instances.
   *
   * @param bool $preemptible
   */
  public function setPreemptible($preemptible)
  {
    $this->preemptible = $preemptible;
  }
  /**
   * @return bool
   */
  public function getPreemptible()
  {
    return $this->preemptible;
  }
  /**
   * The optional reservation affinity. Setting this field will apply the
   * specified [Zonal Compute
   * Reservation](https://cloud.google.com/compute/docs/instances/reserving-
   * zonal-resources) to this node pool.
   *
   * @param ReservationAffinity $reservationAffinity
   */
  public function setReservationAffinity(ReservationAffinity $reservationAffinity)
  {
    $this->reservationAffinity = $reservationAffinity;
  }
  /**
   * @return ReservationAffinity
   */
  public function getReservationAffinity()
  {
    return $this->reservationAffinity;
  }
  /**
   * The resource labels for the node pool to use to annotate any related Google
   * Compute Engine resources.
   *
   * @param string[] $resourceLabels
   */
  public function setResourceLabels($resourceLabels)
  {
    $this->resourceLabels = $resourceLabels;
  }
  /**
   * @return string[]
   */
  public function getResourceLabels()
  {
    return $this->resourceLabels;
  }
  /**
   * A map of resource manager tag keys and values to be attached to the nodes.
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
   * Sandbox configuration for this node.
   *
   * @param SandboxConfig $sandboxConfig
   */
  public function setSandboxConfig(SandboxConfig $sandboxConfig)
  {
    $this->sandboxConfig = $sandboxConfig;
  }
  /**
   * @return SandboxConfig
   */
  public function getSandboxConfig()
  {
    return $this->sandboxConfig;
  }
  /**
   * Secondary boot disk update strategy.
   *
   * @param SecondaryBootDiskUpdateStrategy $secondaryBootDiskUpdateStrategy
   */
  public function setSecondaryBootDiskUpdateStrategy(SecondaryBootDiskUpdateStrategy $secondaryBootDiskUpdateStrategy)
  {
    $this->secondaryBootDiskUpdateStrategy = $secondaryBootDiskUpdateStrategy;
  }
  /**
   * @return SecondaryBootDiskUpdateStrategy
   */
  public function getSecondaryBootDiskUpdateStrategy()
  {
    return $this->secondaryBootDiskUpdateStrategy;
  }
  /**
   * List of secondary boot disks attached to the nodes.
   *
   * @param SecondaryBootDisk[] $secondaryBootDisks
   */
  public function setSecondaryBootDisks($secondaryBootDisks)
  {
    $this->secondaryBootDisks = $secondaryBootDisks;
  }
  /**
   * @return SecondaryBootDisk[]
   */
  public function getSecondaryBootDisks()
  {
    return $this->secondaryBootDisks;
  }
  /**
   * The Google Cloud Platform Service Account to be used by the node VMs.
   * Specify the email address of the Service Account; otherwise, if no Service
   * Account is specified, the "default" service account is used.
   *
   * @param string $serviceAccount
   */
  public function setServiceAccount($serviceAccount)
  {
    $this->serviceAccount = $serviceAccount;
  }
  /**
   * @return string
   */
  public function getServiceAccount()
  {
    return $this->serviceAccount;
  }
  /**
   * Shielded Instance options.
   *
   * @param ShieldedInstanceConfig $shieldedInstanceConfig
   */
  public function setShieldedInstanceConfig(ShieldedInstanceConfig $shieldedInstanceConfig)
  {
    $this->shieldedInstanceConfig = $shieldedInstanceConfig;
  }
  /**
   * @return ShieldedInstanceConfig
   */
  public function getShieldedInstanceConfig()
  {
    return $this->shieldedInstanceConfig;
  }
  /**
   * Parameters for node pools to be backed by shared sole tenant node groups.
   *
   * @param SoleTenantConfig $soleTenantConfig
   */
  public function setSoleTenantConfig(SoleTenantConfig $soleTenantConfig)
  {
    $this->soleTenantConfig = $soleTenantConfig;
  }
  /**
   * @return SoleTenantConfig
   */
  public function getSoleTenantConfig()
  {
    return $this->soleTenantConfig;
  }
  /**
   * Spot flag for enabling Spot VM, which is a rebrand of the existing
   * preemptible flag.
   *
   * @param bool $spot
   */
  public function setSpot($spot)
  {
    $this->spot = $spot;
  }
  /**
   * @return bool
   */
  public function getSpot()
  {
    return $this->spot;
  }
  /**
   * List of Storage Pools where boot disks are provisioned.
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
   * The list of instance tags applied to all nodes. Tags are used to identify
   * valid sources or targets for network firewalls and are specified by the
   * client during cluster or node pool creation. Each tag within the list must
   * comply with RFC1035.
   *
   * @param string[] $tags
   */
  public function setTags($tags)
  {
    $this->tags = $tags;
  }
  /**
   * @return string[]
   */
  public function getTags()
  {
    return $this->tags;
  }
  /**
   * List of kubernetes taints to be applied to each node. For more information,
   * including usage and the valid values, see:
   * https://kubernetes.io/docs/concepts/configuration/taint-and-toleration/
   *
   * @param NodeTaint[] $taints
   */
  public function setTaints($taints)
  {
    $this->taints = $taints;
  }
  /**
   * @return NodeTaint[]
   */
  public function getTaints()
  {
    return $this->taints;
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
   * The workload metadata configuration for this node.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NodeConfig::class, 'Google_Service_Container_NodeConfig');
