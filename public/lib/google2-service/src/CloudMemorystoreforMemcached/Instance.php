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

namespace Google\Service\CloudMemorystoreforMemcached;

class Instance extends \Google\Collection
{
  /**
   * Memcache version is not specified by customer
   */
  public const MEMCACHE_VERSION_MEMCACHE_VERSION_UNSPECIFIED = 'MEMCACHE_VERSION_UNSPECIFIED';
  /**
   * Memcached 1.5 version.
   */
  public const MEMCACHE_VERSION_MEMCACHE_1_5 = 'MEMCACHE_1_5';
  /**
   * Memcached 1.6.15 version.
   */
  public const MEMCACHE_VERSION_MEMCACHE_1_6_15 = 'MEMCACHE_1_6_15';
  /**
   * State not set.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Memcached instance is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * Memcached instance has been created and ready to be used.
   */
  public const STATE_READY = 'READY';
  /**
   * Memcached instance is updating configuration such as maintenance policy and
   * schedule.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * Memcached instance is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Memcached instance is going through maintenance, e.g. data plane rollout.
   */
  public const STATE_PERFORMING_MAINTENANCE = 'PERFORMING_MAINTENANCE';
  /**
   * Memcached instance is undergoing memcached engine version upgrade.
   */
  public const STATE_MEMCACHE_VERSION_UPGRADING = 'MEMCACHE_VERSION_UPGRADING';
  protected $collection_key = 'zones';
  /**
   * The full name of the Google Compute Engine
   * [network](/compute/docs/networks-and-firewalls#networks) to which the
   * instance is connected. If left unspecified, the `default` network will be
   * used.
   *
   * @var string
   */
  public $authorizedNetwork;
  /**
   * Output only. The time the instance was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. Endpoint for the Discovery API.
   *
   * @var string
   */
  public $discoveryEndpoint;
  /**
   * User provided name for the instance, which is only used for display
   * purposes. Cannot be more than 80 characters.
   *
   * @var string
   */
  public $displayName;
  protected $instanceMessagesType = InstanceMessage::class;
  protected $instanceMessagesDataType = 'array';
  /**
   * Resource labels to represent user-provided metadata. Refer to cloud
   * documentation on labels for more details.
   * https://cloud.google.com/compute/docs/labeling-resources
   *
   * @var string[]
   */
  public $labels;
  protected $maintenancePolicyType = GoogleCloudMemcacheV1MaintenancePolicy::class;
  protected $maintenancePolicyDataType = '';
  protected $maintenanceScheduleType = MaintenanceSchedule::class;
  protected $maintenanceScheduleDataType = '';
  /**
   * Output only. The full version of memcached server running on this instance.
   * System automatically determines the full memcached version for an instance
   * based on the input MemcacheVersion. The full version format will be
   * "memcached-1.5.16".
   *
   * @var string
   */
  public $memcacheFullVersion;
  protected $memcacheNodesType = Node::class;
  protected $memcacheNodesDataType = 'array';
  /**
   * The major version of Memcached software. If not provided, latest supported
   * version will be used. Currently the latest supported major version is
   * `MEMCACHE_1_5`. The minor version will be automatically determined by our
   * system based on the latest supported minor version.
   *
   * @var string
   */
  public $memcacheVersion;
  /**
   * Required. Unique name of the resource in this scope including project and
   * location using the form:
   * `projects/{project_id}/locations/{location_id}/instances/{instance_id}`
   * Note: Memcached instances are managed and addressed at the regional level
   * so `location_id` here refers to a Google Cloud region; however, users may
   * choose which zones Memcached nodes should be provisioned in within an
   * instance. Refer to zones field for more details.
   *
   * @var string
   */
  public $name;
  protected $nodeConfigType = NodeConfig::class;
  protected $nodeConfigDataType = '';
  /**
   * Required. Number of nodes in the Memcached instance.
   *
   * @var int
   */
  public $nodeCount;
  protected $parametersType = MemcacheParameters::class;
  protected $parametersDataType = '';
  /**
   * Optional. Contains the id of allocated IP address ranges associated with
   * the private service access connection for example, "test-default"
   * associated with IP range 10.0.0.0/29.
   *
   * @var string[]
   */
  public $reservedIpRangeId;
  /**
   * Optional. Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzi;
  /**
   * Optional. Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzs;
  /**
   * Output only. The state of this Memcached instance.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The time the instance was updated.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Zones in which Memcached nodes should be provisioned. Memcached nodes will
   * be equally distributed across these zones. If not provided, the service
   * will by default create nodes in all zones in the region for the instance.
   *
   * @var string[]
   */
  public $zones;

  /**
   * The full name of the Google Compute Engine
   * [network](/compute/docs/networks-and-firewalls#networks) to which the
   * instance is connected. If left unspecified, the `default` network will be
   * used.
   *
   * @param string $authorizedNetwork
   */
  public function setAuthorizedNetwork($authorizedNetwork)
  {
    $this->authorizedNetwork = $authorizedNetwork;
  }
  /**
   * @return string
   */
  public function getAuthorizedNetwork()
  {
    return $this->authorizedNetwork;
  }
  /**
   * Output only. The time the instance was created.
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
   * Output only. Endpoint for the Discovery API.
   *
   * @param string $discoveryEndpoint
   */
  public function setDiscoveryEndpoint($discoveryEndpoint)
  {
    $this->discoveryEndpoint = $discoveryEndpoint;
  }
  /**
   * @return string
   */
  public function getDiscoveryEndpoint()
  {
    return $this->discoveryEndpoint;
  }
  /**
   * User provided name for the instance, which is only used for display
   * purposes. Cannot be more than 80 characters.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * List of messages that describe the current state of the Memcached instance.
   *
   * @param InstanceMessage[] $instanceMessages
   */
  public function setInstanceMessages($instanceMessages)
  {
    $this->instanceMessages = $instanceMessages;
  }
  /**
   * @return InstanceMessage[]
   */
  public function getInstanceMessages()
  {
    return $this->instanceMessages;
  }
  /**
   * Resource labels to represent user-provided metadata. Refer to cloud
   * documentation on labels for more details.
   * https://cloud.google.com/compute/docs/labeling-resources
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
   * The maintenance policy for the instance. If not provided, the maintenance
   * event will be performed based on Memorystore internal rollout schedule.
   *
   * @param GoogleCloudMemcacheV1MaintenancePolicy $maintenancePolicy
   */
  public function setMaintenancePolicy(GoogleCloudMemcacheV1MaintenancePolicy $maintenancePolicy)
  {
    $this->maintenancePolicy = $maintenancePolicy;
  }
  /**
   * @return GoogleCloudMemcacheV1MaintenancePolicy
   */
  public function getMaintenancePolicy()
  {
    return $this->maintenancePolicy;
  }
  /**
   * Output only. Published maintenance schedule.
   *
   * @param MaintenanceSchedule $maintenanceSchedule
   */
  public function setMaintenanceSchedule(MaintenanceSchedule $maintenanceSchedule)
  {
    $this->maintenanceSchedule = $maintenanceSchedule;
  }
  /**
   * @return MaintenanceSchedule
   */
  public function getMaintenanceSchedule()
  {
    return $this->maintenanceSchedule;
  }
  /**
   * Output only. The full version of memcached server running on this instance.
   * System automatically determines the full memcached version for an instance
   * based on the input MemcacheVersion. The full version format will be
   * "memcached-1.5.16".
   *
   * @param string $memcacheFullVersion
   */
  public function setMemcacheFullVersion($memcacheFullVersion)
  {
    $this->memcacheFullVersion = $memcacheFullVersion;
  }
  /**
   * @return string
   */
  public function getMemcacheFullVersion()
  {
    return $this->memcacheFullVersion;
  }
  /**
   * Output only. List of Memcached nodes. Refer to Node message for more
   * details.
   *
   * @param Node[] $memcacheNodes
   */
  public function setMemcacheNodes($memcacheNodes)
  {
    $this->memcacheNodes = $memcacheNodes;
  }
  /**
   * @return Node[]
   */
  public function getMemcacheNodes()
  {
    return $this->memcacheNodes;
  }
  /**
   * The major version of Memcached software. If not provided, latest supported
   * version will be used. Currently the latest supported major version is
   * `MEMCACHE_1_5`. The minor version will be automatically determined by our
   * system based on the latest supported minor version.
   *
   * Accepted values: MEMCACHE_VERSION_UNSPECIFIED, MEMCACHE_1_5,
   * MEMCACHE_1_6_15
   *
   * @param self::MEMCACHE_VERSION_* $memcacheVersion
   */
  public function setMemcacheVersion($memcacheVersion)
  {
    $this->memcacheVersion = $memcacheVersion;
  }
  /**
   * @return self::MEMCACHE_VERSION_*
   */
  public function getMemcacheVersion()
  {
    return $this->memcacheVersion;
  }
  /**
   * Required. Unique name of the resource in this scope including project and
   * location using the form:
   * `projects/{project_id}/locations/{location_id}/instances/{instance_id}`
   * Note: Memcached instances are managed and addressed at the regional level
   * so `location_id` here refers to a Google Cloud region; however, users may
   * choose which zones Memcached nodes should be provisioned in within an
   * instance. Refer to zones field for more details.
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
   * Required. Configuration for Memcached nodes.
   *
   * @param NodeConfig $nodeConfig
   */
  public function setNodeConfig(NodeConfig $nodeConfig)
  {
    $this->nodeConfig = $nodeConfig;
  }
  /**
   * @return NodeConfig
   */
  public function getNodeConfig()
  {
    return $this->nodeConfig;
  }
  /**
   * Required. Number of nodes in the Memcached instance.
   *
   * @param int $nodeCount
   */
  public function setNodeCount($nodeCount)
  {
    $this->nodeCount = $nodeCount;
  }
  /**
   * @return int
   */
  public function getNodeCount()
  {
    return $this->nodeCount;
  }
  /**
   * User defined parameters to apply to the memcached process on each node.
   *
   * @param MemcacheParameters $parameters
   */
  public function setParameters(MemcacheParameters $parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return MemcacheParameters
   */
  public function getParameters()
  {
    return $this->parameters;
  }
  /**
   * Optional. Contains the id of allocated IP address ranges associated with
   * the private service access connection for example, "test-default"
   * associated with IP range 10.0.0.0/29.
   *
   * @param string[] $reservedIpRangeId
   */
  public function setReservedIpRangeId($reservedIpRangeId)
  {
    $this->reservedIpRangeId = $reservedIpRangeId;
  }
  /**
   * @return string[]
   */
  public function getReservedIpRangeId()
  {
    return $this->reservedIpRangeId;
  }
  /**
   * Optional. Output only. Reserved for future use.
   *
   * @param bool $satisfiesPzi
   */
  public function setSatisfiesPzi($satisfiesPzi)
  {
    $this->satisfiesPzi = $satisfiesPzi;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzi()
  {
    return $this->satisfiesPzi;
  }
  /**
   * Optional. Output only. Reserved for future use.
   *
   * @param bool $satisfiesPzs
   */
  public function setSatisfiesPzs($satisfiesPzs)
  {
    $this->satisfiesPzs = $satisfiesPzs;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzs()
  {
    return $this->satisfiesPzs;
  }
  /**
   * Output only. The state of this Memcached instance.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, READY, UPDATING, DELETING,
   * PERFORMING_MAINTENANCE, MEMCACHE_VERSION_UPGRADING
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
   * Output only. The time the instance was updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
  /**
   * Zones in which Memcached nodes should be provisioned. Memcached nodes will
   * be equally distributed across these zones. If not provided, the service
   * will by default create nodes in all zones in the region for the instance.
   *
   * @param string[] $zones
   */
  public function setZones($zones)
  {
    $this->zones = $zones;
  }
  /**
   * @return string[]
   */
  public function getZones()
  {
    return $this->zones;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Instance::class, 'Google_Service_CloudMemorystoreforMemcached_Instance');
