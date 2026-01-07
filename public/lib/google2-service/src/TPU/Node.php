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

namespace Google\Service\TPU;

class Node extends \Google\Collection
{
  /**
   * API version is unknown.
   */
  public const API_VERSION_API_VERSION_UNSPECIFIED = 'API_VERSION_UNSPECIFIED';
  /**
   * TPU API V1Alpha1 version.
   */
  public const API_VERSION_V1_ALPHA1 = 'V1_ALPHA1';
  /**
   * TPU API V1 version.
   */
  public const API_VERSION_V1 = 'V1';
  /**
   * TPU API V2Alpha1 version.
   */
  public const API_VERSION_V2_ALPHA1 = 'V2_ALPHA1';
  /**
   * TPU API V2 version.
   */
  public const API_VERSION_V2 = 'V2';
  /**
   * Health status is unknown: not initialized or failed to retrieve.
   */
  public const HEALTH_HEALTH_UNSPECIFIED = 'HEALTH_UNSPECIFIED';
  /**
   * The resource is healthy.
   */
  public const HEALTH_HEALTHY = 'HEALTHY';
  /**
   * The resource is unresponsive.
   */
  public const HEALTH_TIMEOUT = 'TIMEOUT';
  /**
   * The in-guest ML stack is unhealthy.
   */
  public const HEALTH_UNHEALTHY_TENSORFLOW = 'UNHEALTHY_TENSORFLOW';
  /**
   * The node is under maintenance/priority boost caused rescheduling and will
   * resume running once rescheduled.
   */
  public const HEALTH_UNHEALTHY_MAINTENANCE = 'UNHEALTHY_MAINTENANCE';
  /**
   * TPU node state is not known/set.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * TPU node is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * TPU node has been created.
   */
  public const STATE_READY = 'READY';
  /**
   * TPU node is restarting.
   */
  public const STATE_RESTARTING = 'RESTARTING';
  /**
   * TPU node is undergoing reimaging.
   */
  public const STATE_REIMAGING = 'REIMAGING';
  /**
   * TPU node is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * TPU node is being repaired and may be unusable. Details can be found in the
   * 'help_description' field.
   */
  public const STATE_REPAIRING = 'REPAIRING';
  /**
   * TPU node is stopped.
   */
  public const STATE_STOPPED = 'STOPPED';
  /**
   * TPU node is currently stopping.
   */
  public const STATE_STOPPING = 'STOPPING';
  /**
   * TPU node is currently starting.
   */
  public const STATE_STARTING = 'STARTING';
  /**
   * TPU node has been preempted. Only applies to Preemptible TPU Nodes.
   */
  public const STATE_PREEMPTED = 'PREEMPTED';
  /**
   * TPU node has been terminated due to maintenance or has reached the end of
   * its life cycle (for preemptible nodes).
   */
  public const STATE_TERMINATED = 'TERMINATED';
  /**
   * TPU node is currently hiding.
   */
  public const STATE_HIDING = 'HIDING';
  /**
   * TPU node has been hidden.
   */
  public const STATE_HIDDEN = 'HIDDEN';
  /**
   * TPU node is currently unhiding.
   */
  public const STATE_UNHIDING = 'UNHIDING';
  /**
   * TPU node has unknown state after a failed repair.
   */
  public const STATE_UNKNOWN = 'UNKNOWN';
  protected $collection_key = 'tags';
  protected $acceleratorConfigType = AcceleratorConfig::class;
  protected $acceleratorConfigDataType = '';
  /**
   * Optional. The type of hardware accelerators associated with this node.
   *
   * @var string
   */
  public $acceleratorType;
  /**
   * Output only. The API version that created this Node.
   *
   * @var string
   */
  public $apiVersion;
  protected $bootDiskConfigType = BootDiskConfig::class;
  protected $bootDiskConfigDataType = '';
  /**
   * The CIDR block that the TPU node will use when selecting an IP address.
   * This CIDR block must be a /29 block; the Compute Engine networks API
   * forbids a smaller block, and using a larger block would be wasteful (a node
   * can only consume one IP address). Errors will occur if the CIDR block has
   * already been used for a currently existing TPU node, the CIDR block
   * conflicts with any subnetworks in the user's provided network, or the
   * provided network is peered with another network that is using that CIDR
   * block.
   *
   * @var string
   */
  public $cidrBlock;
  /**
   * Output only. The time when the node was created.
   *
   * @var string
   */
  public $createTime;
  protected $dataDisksType = AttachedDisk::class;
  protected $dataDisksDataType = 'array';
  /**
   * The user-supplied description of the TPU. Maximum of 512 characters.
   *
   * @var string
   */
  public $description;
  /**
   * The health status of the TPU node.
   *
   * @var string
   */
  public $health;
  /**
   * Output only. If this field is populated, it contains a description of why
   * the TPU Node is unhealthy.
   *
   * @var string
   */
  public $healthDescription;
  /**
   * Output only. The unique identifier for the TPU Node.
   *
   * @var string
   */
  public $id;
  /**
   * Resource labels to represent user-provided metadata.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Custom metadata to apply to the TPU Node. Can set startup-script and
   * shutdown-script
   *
   * @var string[]
   */
  public $metadata;
  /**
   * Output only. Whether the Node belongs to a Multislice group.
   *
   * @var bool
   */
  public $multisliceNode;
  /**
   * Output only. Immutable. The name of the TPU.
   *
   * @var string
   */
  public $name;
  protected $networkConfigType = NetworkConfig::class;
  protected $networkConfigDataType = '';
  protected $networkConfigsType = NetworkConfig::class;
  protected $networkConfigsDataType = 'array';
  protected $networkEndpointsType = NetworkEndpoint::class;
  protected $networkEndpointsDataType = 'array';
  /**
   * Output only. The qualified name of the QueuedResource that requested this
   * Node.
   *
   * @var string
   */
  public $queuedResource;
  /**
   * Required. The runtime version running in the Node.
   *
   * @var string
   */
  public $runtimeVersion;
  protected $schedulingConfigType = SchedulingConfig::class;
  protected $schedulingConfigDataType = '';
  protected $serviceAccountType = ServiceAccount::class;
  protected $serviceAccountDataType = '';
  protected $shieldedInstanceConfigType = ShieldedInstanceConfig::class;
  protected $shieldedInstanceConfigDataType = '';
  /**
   * Output only. The current state for the TPU Node.
   *
   * @var string
   */
  public $state;
  protected $symptomsType = Symptom::class;
  protected $symptomsDataType = 'array';
  /**
   * Tags to apply to the TPU Node. Tags are used to identify valid sources or
   * targets for network firewalls.
   *
   * @var string[]
   */
  public $tags;
  protected $upcomingMaintenanceType = UpcomingMaintenance::class;
  protected $upcomingMaintenanceDataType = '';

  /**
   * The AccleratorConfig for the TPU Node.
   *
   * @param AcceleratorConfig $acceleratorConfig
   */
  public function setAcceleratorConfig(AcceleratorConfig $acceleratorConfig)
  {
    $this->acceleratorConfig = $acceleratorConfig;
  }
  /**
   * @return AcceleratorConfig
   */
  public function getAcceleratorConfig()
  {
    return $this->acceleratorConfig;
  }
  /**
   * Optional. The type of hardware accelerators associated with this node.
   *
   * @param string $acceleratorType
   */
  public function setAcceleratorType($acceleratorType)
  {
    $this->acceleratorType = $acceleratorType;
  }
  /**
   * @return string
   */
  public function getAcceleratorType()
  {
    return $this->acceleratorType;
  }
  /**
   * Output only. The API version that created this Node.
   *
   * Accepted values: API_VERSION_UNSPECIFIED, V1_ALPHA1, V1, V2_ALPHA1, V2
   *
   * @param self::API_VERSION_* $apiVersion
   */
  public function setApiVersion($apiVersion)
  {
    $this->apiVersion = $apiVersion;
  }
  /**
   * @return self::API_VERSION_*
   */
  public function getApiVersion()
  {
    return $this->apiVersion;
  }
  /**
   * Optional. Boot disk configuration.
   *
   * @param BootDiskConfig $bootDiskConfig
   */
  public function setBootDiskConfig(BootDiskConfig $bootDiskConfig)
  {
    $this->bootDiskConfig = $bootDiskConfig;
  }
  /**
   * @return BootDiskConfig
   */
  public function getBootDiskConfig()
  {
    return $this->bootDiskConfig;
  }
  /**
   * The CIDR block that the TPU node will use when selecting an IP address.
   * This CIDR block must be a /29 block; the Compute Engine networks API
   * forbids a smaller block, and using a larger block would be wasteful (a node
   * can only consume one IP address). Errors will occur if the CIDR block has
   * already been used for a currently existing TPU node, the CIDR block
   * conflicts with any subnetworks in the user's provided network, or the
   * provided network is peered with another network that is using that CIDR
   * block.
   *
   * @param string $cidrBlock
   */
  public function setCidrBlock($cidrBlock)
  {
    $this->cidrBlock = $cidrBlock;
  }
  /**
   * @return string
   */
  public function getCidrBlock()
  {
    return $this->cidrBlock;
  }
  /**
   * Output only. The time when the node was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * The additional data disks for the Node.
   *
   * @param AttachedDisk[] $dataDisks
   */
  public function setDataDisks($dataDisks)
  {
    $this->dataDisks = $dataDisks;
  }
  /**
   * @return AttachedDisk[]
   */
  public function getDataDisks()
  {
    return $this->dataDisks;
  }
  /**
   * The user-supplied description of the TPU. Maximum of 512 characters.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * The health status of the TPU node.
   *
   * Accepted values: HEALTH_UNSPECIFIED, HEALTHY, TIMEOUT,
   * UNHEALTHY_TENSORFLOW, UNHEALTHY_MAINTENANCE
   *
   * @param self::HEALTH_* $health
   */
  public function setHealth($health)
  {
    $this->health = $health;
  }
  /**
   * @return self::HEALTH_*
   */
  public function getHealth()
  {
    return $this->health;
  }
  /**
   * Output only. If this field is populated, it contains a description of why
   * the TPU Node is unhealthy.
   *
   * @param string $healthDescription
   */
  public function setHealthDescription($healthDescription)
  {
    $this->healthDescription = $healthDescription;
  }
  /**
   * @return string
   */
  public function getHealthDescription()
  {
    return $this->healthDescription;
  }
  /**
   * Output only. The unique identifier for the TPU Node.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Resource labels to represent user-provided metadata.
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
   * Custom metadata to apply to the TPU Node. Can set startup-script and
   * shutdown-script
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
   * Output only. Whether the Node belongs to a Multislice group.
   *
   * @param bool $multisliceNode
   */
  public function setMultisliceNode($multisliceNode)
  {
    $this->multisliceNode = $multisliceNode;
  }
  /**
   * @return bool
   */
  public function getMultisliceNode()
  {
    return $this->multisliceNode;
  }
  /**
   * Output only. Immutable. The name of the TPU.
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
   * Network configurations for the TPU node. network_config and network_configs
   * are mutually exclusive, you can only specify one of them. If both are
   * specified, an error will be returned.
   *
   * @param NetworkConfig $networkConfig
   */
  public function setNetworkConfig(NetworkConfig $networkConfig)
  {
    $this->networkConfig = $networkConfig;
  }
  /**
   * @return NetworkConfig
   */
  public function getNetworkConfig()
  {
    return $this->networkConfig;
  }
  /**
   * Optional. Repeated network configurations for the TPU node. This field is
   * used to specify multiple networks configs for the TPU node. network_config
   * and network_configs are mutually exclusive, you can only specify one of
   * them. If both are specified, an error will be returned.
   *
   * @param NetworkConfig[] $networkConfigs
   */
  public function setNetworkConfigs($networkConfigs)
  {
    $this->networkConfigs = $networkConfigs;
  }
  /**
   * @return NetworkConfig[]
   */
  public function getNetworkConfigs()
  {
    return $this->networkConfigs;
  }
  /**
   * Output only. The network endpoints where TPU workers can be accessed and
   * sent work. It is recommended that runtime clients of the node reach out to
   * the 0th entry in this map first.
   *
   * @param NetworkEndpoint[] $networkEndpoints
   */
  public function setNetworkEndpoints($networkEndpoints)
  {
    $this->networkEndpoints = $networkEndpoints;
  }
  /**
   * @return NetworkEndpoint[]
   */
  public function getNetworkEndpoints()
  {
    return $this->networkEndpoints;
  }
  /**
   * Output only. The qualified name of the QueuedResource that requested this
   * Node.
   *
   * @param string $queuedResource
   */
  public function setQueuedResource($queuedResource)
  {
    $this->queuedResource = $queuedResource;
  }
  /**
   * @return string
   */
  public function getQueuedResource()
  {
    return $this->queuedResource;
  }
  /**
   * Required. The runtime version running in the Node.
   *
   * @param string $runtimeVersion
   */
  public function setRuntimeVersion($runtimeVersion)
  {
    $this->runtimeVersion = $runtimeVersion;
  }
  /**
   * @return string
   */
  public function getRuntimeVersion()
  {
    return $this->runtimeVersion;
  }
  /**
   * The scheduling options for this node.
   *
   * @param SchedulingConfig $schedulingConfig
   */
  public function setSchedulingConfig(SchedulingConfig $schedulingConfig)
  {
    $this->schedulingConfig = $schedulingConfig;
  }
  /**
   * @return SchedulingConfig
   */
  public function getSchedulingConfig()
  {
    return $this->schedulingConfig;
  }
  /**
   * The Google Cloud Platform Service Account to be used by the TPU node VMs.
   * If None is specified, the default compute service account will be used.
   *
   * @param ServiceAccount $serviceAccount
   */
  public function setServiceAccount(ServiceAccount $serviceAccount)
  {
    $this->serviceAccount = $serviceAccount;
  }
  /**
   * @return ServiceAccount
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
   * Output only. The current state for the TPU Node.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, READY, RESTARTING, REIMAGING,
   * DELETING, REPAIRING, STOPPED, STOPPING, STARTING, PREEMPTED, TERMINATED,
   * HIDING, HIDDEN, UNHIDING, UNKNOWN
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. The Symptoms that have occurred to the TPU Node.
   *
   * @param Symptom[] $symptoms
   */
  public function setSymptoms($symptoms)
  {
    $this->symptoms = $symptoms;
  }
  /**
   * @return Symptom[]
   */
  public function getSymptoms()
  {
    return $this->symptoms;
  }
  /**
   * Tags to apply to the TPU Node. Tags are used to identify valid sources or
   * targets for network firewalls.
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
   * Output only. Upcoming maintenance on this TPU node.
   *
   * @param UpcomingMaintenance $upcomingMaintenance
   */
  public function setUpcomingMaintenance(UpcomingMaintenance $upcomingMaintenance)
  {
    $this->upcomingMaintenance = $upcomingMaintenance;
  }
  /**
   * @return UpcomingMaintenance
   */
  public function getUpcomingMaintenance()
  {
    return $this->upcomingMaintenance;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Node::class, 'Google_Service_TPU_Node');
