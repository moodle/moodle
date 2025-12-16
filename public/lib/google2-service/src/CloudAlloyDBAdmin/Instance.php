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

namespace Google\Service\CloudAlloyDBAdmin;

class Instance extends \Google\Collection
{
  /**
   * The policy is not specified.
   */
  public const ACTIVATION_POLICY_ACTIVATION_POLICY_UNSPECIFIED = 'ACTIVATION_POLICY_UNSPECIFIED';
  /**
   * The instance is running.
   */
  public const ACTIVATION_POLICY_ALWAYS = 'ALWAYS';
  /**
   * The instance is not running.
   */
  public const ACTIVATION_POLICY_NEVER = 'NEVER';
  /**
   * This is an unknown Availability type.
   */
  public const AVAILABILITY_TYPE_AVAILABILITY_TYPE_UNSPECIFIED = 'AVAILABILITY_TYPE_UNSPECIFIED';
  /**
   * Zonal available instance.
   */
  public const AVAILABILITY_TYPE_ZONAL = 'ZONAL';
  /**
   * Regional (or Highly) available instance.
   */
  public const AVAILABILITY_TYPE_REGIONAL = 'REGIONAL';
  /**
   * The type of the instance is unknown.
   */
  public const INSTANCE_TYPE_INSTANCE_TYPE_UNSPECIFIED = 'INSTANCE_TYPE_UNSPECIFIED';
  /**
   * PRIMARY instances support read and write operations.
   */
  public const INSTANCE_TYPE_PRIMARY = 'PRIMARY';
  /**
   * READ POOL instances support read operations only. Each read pool instance
   * consists of one or more homogeneous nodes. * Read pool of size 1 can only
   * have zonal availability. * Read pools with node count of 2 or more can have
   * regional availability (nodes are present in 2 or more zones in a region).
   */
  public const INSTANCE_TYPE_READ_POOL = 'READ_POOL';
  /**
   * SECONDARY instances support read operations only. SECONDARY instance is a
   * cross-region read replica
   */
  public const INSTANCE_TYPE_SECONDARY = 'SECONDARY';
  /**
   * The state of the instance is unknown.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The instance is active and running.
   */
  public const STATE_READY = 'READY';
  /**
   * The instance is stopped. Instance name and IP resources are preserved.
   */
  public const STATE_STOPPED = 'STOPPED';
  /**
   * The instance is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The instance is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The instance is down for maintenance.
   */
  public const STATE_MAINTENANCE = 'MAINTENANCE';
  /**
   * The creation of the instance failed or a fatal error occurred during an
   * operation on the instance. Note: Instances in this state would tried to be
   * auto-repaired. And Customers should be able to restart, update or delete
   * these instances.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The instance has been configured to sync data from some other source.
   */
  public const STATE_BOOTSTRAPPING = 'BOOTSTRAPPING';
  /**
   * The instance is being promoted.
   */
  public const STATE_PROMOTING = 'PROMOTING';
  protected $collection_key = 'outboundPublicIpAddresses';
  /**
   * Optional. Specifies whether an instance needs to spin up. Once the instance
   * is active, the activation policy can be updated to the `NEVER` to stop the
   * instance. Likewise, the activation policy can be updated to `ALWAYS` to
   * start the instance. There are restrictions around when an instance
   * can/cannot be activated (for example, a read pool instance should be
   * stopped before stopping primary etc.). Please refer to the API
   * documentation for more details.
   *
   * @var string
   */
  public $activationPolicy;
  /**
   * Annotations to allow client tools to store small amount of arbitrary data.
   * This is distinct from labels. https://google.aip.dev/128
   *
   * @var string[]
   */
  public $annotations;
  /**
   * Availability type of an Instance. If empty, defaults to REGIONAL for
   * primary instances. For read pools, availability_type is always UNSPECIFIED.
   * Instances in the read pools are evenly distributed across available zones
   * within the region (i.e. read pools with more than one node will have a node
   * in at least two zones).
   *
   * @var string
   */
  public $availabilityType;
  protected $clientConnectionConfigType = ClientConnectionConfig::class;
  protected $clientConnectionConfigDataType = '';
  protected $connectionPoolConfigType = ConnectionPoolConfig::class;
  protected $connectionPoolConfigDataType = '';
  /**
   * Output only. Create time stamp
   *
   * @var string
   */
  public $createTime;
  /**
   * Database flags. Set at the instance level. They are copied from the primary
   * instance on secondary instance creation. Flags that have restrictions
   * default to the value at primary instance on read instances during creation.
   * Read instances can set new flags or override existing flags that are
   * relevant for reads, for example, for enabling columnar cache on a read
   * instance. Flags set on read instance might or might not be present on the
   * primary instance. This is a list of "key": "value" pairs. "key": The name
   * of the flag. These flags are passed at instance setup time, so include both
   * server options and system variables for Postgres. Flags are specified with
   * underscores, not hyphens. "value": The value of the flag. Booleans are set
   * to **on** for true and **off** for false. This field must be omitted if the
   * flag doesn't take a value.
   *
   * @var string[]
   */
  public $databaseFlags;
  /**
   * Output only. Delete time stamp
   *
   * @var string
   */
  public $deleteTime;
  /**
   * User-settable and human-readable display name for the Instance.
   *
   * @var string
   */
  public $displayName;
  /**
   * For Resource freshness validation (https://google.aip.dev/154)
   *
   * @var string
   */
  public $etag;
  /**
   * The Compute Engine zone that the instance should serve from, per
   * https://cloud.google.com/compute/docs/regions-zones This can ONLY be
   * specified for ZONAL instances. If present for a REGIONAL instance, an error
   * will be thrown. If this is absent for a ZONAL instance, instance is created
   * in a random zone with available capacity.
   *
   * @var string
   */
  public $gceZone;
  /**
   * Required. The type of the instance. Specified at creation time.
   *
   * @var string
   */
  public $instanceType;
  /**
   * Output only. The IP address for the Instance. This is the connection
   * endpoint for an end-user application.
   *
   * @var string
   */
  public $ipAddress;
  /**
   * Labels as key value pairs
   *
   * @var string[]
   */
  public $labels;
  protected $machineConfigType = MachineConfig::class;
  protected $machineConfigDataType = '';
  /**
   * Output only. Maintenance version of the instance, for example:
   * POSTGRES_15.2025_07_15.04_00. Output only. Update this field via the parent
   * cluster's maintenance_version field(s).
   *
   * @var string
   */
  public $maintenanceVersionName;
  /**
   * Output only. The name of the instance resource with the format: * projects/
   * {project}/locations/{region}/clusters/{cluster_id}/instances/{instance_id}
   * where the cluster and instance ID segments should satisfy the regex
   * expression `[a-z]([a-z0-9-]{0,61}[a-z0-9])?`, e.g. 1-63 characters of
   * lowercase letters, numbers, and dashes, starting with a letter, and ending
   * with a letter or number. For more details see https://google.aip.dev/122.
   * The prefix of the instance resource name is the name of the parent
   * resource: * projects/{project}/locations/{region}/clusters/{cluster_id}
   *
   * @var string
   */
  public $name;
  protected $networkConfigType = InstanceNetworkConfig::class;
  protected $networkConfigDataType = '';
  protected $nodesType = Node::class;
  protected $nodesDataType = 'array';
  protected $observabilityConfigType = ObservabilityInstanceConfig::class;
  protected $observabilityConfigDataType = '';
  /**
   * Output only. All outbound public IP addresses configured for the instance.
   *
   * @var string[]
   */
  public $outboundPublicIpAddresses;
  protected $pscInstanceConfigType = PscInstanceConfig::class;
  protected $pscInstanceConfigDataType = '';
  /**
   * Output only. The public IP addresses for the Instance. This is available
   * ONLY when enable_public_ip is set. This is the connection endpoint for an
   * end-user application.
   *
   * @var string
   */
  public $publicIpAddress;
  protected $queryInsightsConfigType = QueryInsightsInstanceConfig::class;
  protected $queryInsightsConfigDataType = '';
  protected $readPoolConfigType = ReadPoolConfig::class;
  protected $readPoolConfigDataType = '';
  /**
   * Output only. Reconciling (https://google.aip.dev/128#reconciliation). Set
   * to true if the current state of Instance does not match the user's intended
   * state, and the service is actively updating the resource to reconcile them.
   * This can happen due to user-triggered updates or system actions like
   * failover or maintenance.
   *
   * @var bool
   */
  public $reconciling;
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzs;
  /**
   * Output only. The current serving state of the instance.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The system-generated UID of the resource. The UID is assigned
   * when the resource is created, and it is retained until it is deleted.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. Update time stamp
   *
   * @var string
   */
  public $updateTime;
  protected $writableNodeType = Node::class;
  protected $writableNodeDataType = '';

  /**
   * Optional. Specifies whether an instance needs to spin up. Once the instance
   * is active, the activation policy can be updated to the `NEVER` to stop the
   * instance. Likewise, the activation policy can be updated to `ALWAYS` to
   * start the instance. There are restrictions around when an instance
   * can/cannot be activated (for example, a read pool instance should be
   * stopped before stopping primary etc.). Please refer to the API
   * documentation for more details.
   *
   * Accepted values: ACTIVATION_POLICY_UNSPECIFIED, ALWAYS, NEVER
   *
   * @param self::ACTIVATION_POLICY_* $activationPolicy
   */
  public function setActivationPolicy($activationPolicy)
  {
    $this->activationPolicy = $activationPolicy;
  }
  /**
   * @return self::ACTIVATION_POLICY_*
   */
  public function getActivationPolicy()
  {
    return $this->activationPolicy;
  }
  /**
   * Annotations to allow client tools to store small amount of arbitrary data.
   * This is distinct from labels. https://google.aip.dev/128
   *
   * @param string[] $annotations
   */
  public function setAnnotations($annotations)
  {
    $this->annotations = $annotations;
  }
  /**
   * @return string[]
   */
  public function getAnnotations()
  {
    return $this->annotations;
  }
  /**
   * Availability type of an Instance. If empty, defaults to REGIONAL for
   * primary instances. For read pools, availability_type is always UNSPECIFIED.
   * Instances in the read pools are evenly distributed across available zones
   * within the region (i.e. read pools with more than one node will have a node
   * in at least two zones).
   *
   * Accepted values: AVAILABILITY_TYPE_UNSPECIFIED, ZONAL, REGIONAL
   *
   * @param self::AVAILABILITY_TYPE_* $availabilityType
   */
  public function setAvailabilityType($availabilityType)
  {
    $this->availabilityType = $availabilityType;
  }
  /**
   * @return self::AVAILABILITY_TYPE_*
   */
  public function getAvailabilityType()
  {
    return $this->availabilityType;
  }
  /**
   * Optional. Client connection specific configurations
   *
   * @param ClientConnectionConfig $clientConnectionConfig
   */
  public function setClientConnectionConfig(ClientConnectionConfig $clientConnectionConfig)
  {
    $this->clientConnectionConfig = $clientConnectionConfig;
  }
  /**
   * @return ClientConnectionConfig
   */
  public function getClientConnectionConfig()
  {
    return $this->clientConnectionConfig;
  }
  /**
   * Optional. The configuration for Managed Connection Pool (MCP).
   *
   * @param ConnectionPoolConfig $connectionPoolConfig
   */
  public function setConnectionPoolConfig(ConnectionPoolConfig $connectionPoolConfig)
  {
    $this->connectionPoolConfig = $connectionPoolConfig;
  }
  /**
   * @return ConnectionPoolConfig
   */
  public function getConnectionPoolConfig()
  {
    return $this->connectionPoolConfig;
  }
  /**
   * Output only. Create time stamp
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
   * Database flags. Set at the instance level. They are copied from the primary
   * instance on secondary instance creation. Flags that have restrictions
   * default to the value at primary instance on read instances during creation.
   * Read instances can set new flags or override existing flags that are
   * relevant for reads, for example, for enabling columnar cache on a read
   * instance. Flags set on read instance might or might not be present on the
   * primary instance. This is a list of "key": "value" pairs. "key": The name
   * of the flag. These flags are passed at instance setup time, so include both
   * server options and system variables for Postgres. Flags are specified with
   * underscores, not hyphens. "value": The value of the flag. Booleans are set
   * to **on** for true and **off** for false. This field must be omitted if the
   * flag doesn't take a value.
   *
   * @param string[] $databaseFlags
   */
  public function setDatabaseFlags($databaseFlags)
  {
    $this->databaseFlags = $databaseFlags;
  }
  /**
   * @return string[]
   */
  public function getDatabaseFlags()
  {
    return $this->databaseFlags;
  }
  /**
   * Output only. Delete time stamp
   *
   * @param string $deleteTime
   */
  public function setDeleteTime($deleteTime)
  {
    $this->deleteTime = $deleteTime;
  }
  /**
   * @return string
   */
  public function getDeleteTime()
  {
    return $this->deleteTime;
  }
  /**
   * User-settable and human-readable display name for the Instance.
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
   * For Resource freshness validation (https://google.aip.dev/154)
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
   * The Compute Engine zone that the instance should serve from, per
   * https://cloud.google.com/compute/docs/regions-zones This can ONLY be
   * specified for ZONAL instances. If present for a REGIONAL instance, an error
   * will be thrown. If this is absent for a ZONAL instance, instance is created
   * in a random zone with available capacity.
   *
   * @param string $gceZone
   */
  public function setGceZone($gceZone)
  {
    $this->gceZone = $gceZone;
  }
  /**
   * @return string
   */
  public function getGceZone()
  {
    return $this->gceZone;
  }
  /**
   * Required. The type of the instance. Specified at creation time.
   *
   * Accepted values: INSTANCE_TYPE_UNSPECIFIED, PRIMARY, READ_POOL, SECONDARY
   *
   * @param self::INSTANCE_TYPE_* $instanceType
   */
  public function setInstanceType($instanceType)
  {
    $this->instanceType = $instanceType;
  }
  /**
   * @return self::INSTANCE_TYPE_*
   */
  public function getInstanceType()
  {
    return $this->instanceType;
  }
  /**
   * Output only. The IP address for the Instance. This is the connection
   * endpoint for an end-user application.
   *
   * @param string $ipAddress
   */
  public function setIpAddress($ipAddress)
  {
    $this->ipAddress = $ipAddress;
  }
  /**
   * @return string
   */
  public function getIpAddress()
  {
    return $this->ipAddress;
  }
  /**
   * Labels as key value pairs
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
   * Configurations for the machines that host the underlying database engine.
   *
   * @param MachineConfig $machineConfig
   */
  public function setMachineConfig(MachineConfig $machineConfig)
  {
    $this->machineConfig = $machineConfig;
  }
  /**
   * @return MachineConfig
   */
  public function getMachineConfig()
  {
    return $this->machineConfig;
  }
  /**
   * Output only. Maintenance version of the instance, for example:
   * POSTGRES_15.2025_07_15.04_00. Output only. Update this field via the parent
   * cluster's maintenance_version field(s).
   *
   * @param string $maintenanceVersionName
   */
  public function setMaintenanceVersionName($maintenanceVersionName)
  {
    $this->maintenanceVersionName = $maintenanceVersionName;
  }
  /**
   * @return string
   */
  public function getMaintenanceVersionName()
  {
    return $this->maintenanceVersionName;
  }
  /**
   * Output only. The name of the instance resource with the format: * projects/
   * {project}/locations/{region}/clusters/{cluster_id}/instances/{instance_id}
   * where the cluster and instance ID segments should satisfy the regex
   * expression `[a-z]([a-z0-9-]{0,61}[a-z0-9])?`, e.g. 1-63 characters of
   * lowercase letters, numbers, and dashes, starting with a letter, and ending
   * with a letter or number. For more details see https://google.aip.dev/122.
   * The prefix of the instance resource name is the name of the parent
   * resource: * projects/{project}/locations/{region}/clusters/{cluster_id}
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
   * Optional. Instance-level network configuration.
   *
   * @param InstanceNetworkConfig $networkConfig
   */
  public function setNetworkConfig(InstanceNetworkConfig $networkConfig)
  {
    $this->networkConfig = $networkConfig;
  }
  /**
   * @return InstanceNetworkConfig
   */
  public function getNetworkConfig()
  {
    return $this->networkConfig;
  }
  /**
   * Output only. List of available read-only VMs in this instance, including
   * the standby for a PRIMARY instance.
   *
   * @param Node[] $nodes
   */
  public function setNodes($nodes)
  {
    $this->nodes = $nodes;
  }
  /**
   * @return Node[]
   */
  public function getNodes()
  {
    return $this->nodes;
  }
  /**
   * Configuration for observability.
   *
   * @param ObservabilityInstanceConfig $observabilityConfig
   */
  public function setObservabilityConfig(ObservabilityInstanceConfig $observabilityConfig)
  {
    $this->observabilityConfig = $observabilityConfig;
  }
  /**
   * @return ObservabilityInstanceConfig
   */
  public function getObservabilityConfig()
  {
    return $this->observabilityConfig;
  }
  /**
   * Output only. All outbound public IP addresses configured for the instance.
   *
   * @param string[] $outboundPublicIpAddresses
   */
  public function setOutboundPublicIpAddresses($outboundPublicIpAddresses)
  {
    $this->outboundPublicIpAddresses = $outboundPublicIpAddresses;
  }
  /**
   * @return string[]
   */
  public function getOutboundPublicIpAddresses()
  {
    return $this->outboundPublicIpAddresses;
  }
  /**
   * Optional. The configuration for Private Service Connect (PSC) for the
   * instance.
   *
   * @param PscInstanceConfig $pscInstanceConfig
   */
  public function setPscInstanceConfig(PscInstanceConfig $pscInstanceConfig)
  {
    $this->pscInstanceConfig = $pscInstanceConfig;
  }
  /**
   * @return PscInstanceConfig
   */
  public function getPscInstanceConfig()
  {
    return $this->pscInstanceConfig;
  }
  /**
   * Output only. The public IP addresses for the Instance. This is available
   * ONLY when enable_public_ip is set. This is the connection endpoint for an
   * end-user application.
   *
   * @param string $publicIpAddress
   */
  public function setPublicIpAddress($publicIpAddress)
  {
    $this->publicIpAddress = $publicIpAddress;
  }
  /**
   * @return string
   */
  public function getPublicIpAddress()
  {
    return $this->publicIpAddress;
  }
  /**
   * Configuration for query insights.
   *
   * @param QueryInsightsInstanceConfig $queryInsightsConfig
   */
  public function setQueryInsightsConfig(QueryInsightsInstanceConfig $queryInsightsConfig)
  {
    $this->queryInsightsConfig = $queryInsightsConfig;
  }
  /**
   * @return QueryInsightsInstanceConfig
   */
  public function getQueryInsightsConfig()
  {
    return $this->queryInsightsConfig;
  }
  /**
   * Read pool instance configuration. This is required if the value of
   * instanceType is READ_POOL.
   *
   * @param ReadPoolConfig $readPoolConfig
   */
  public function setReadPoolConfig(ReadPoolConfig $readPoolConfig)
  {
    $this->readPoolConfig = $readPoolConfig;
  }
  /**
   * @return ReadPoolConfig
   */
  public function getReadPoolConfig()
  {
    return $this->readPoolConfig;
  }
  /**
   * Output only. Reconciling (https://google.aip.dev/128#reconciliation). Set
   * to true if the current state of Instance does not match the user's intended
   * state, and the service is actively updating the resource to reconcile them.
   * This can happen due to user-triggered updates or system actions like
   * failover or maintenance.
   *
   * @param bool $reconciling
   */
  public function setReconciling($reconciling)
  {
    $this->reconciling = $reconciling;
  }
  /**
   * @return bool
   */
  public function getReconciling()
  {
    return $this->reconciling;
  }
  /**
   * Output only. Reserved for future use.
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
   * Output only. The current serving state of the instance.
   *
   * Accepted values: STATE_UNSPECIFIED, READY, STOPPED, CREATING, DELETING,
   * MAINTENANCE, FAILED, BOOTSTRAPPING, PROMOTING
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
   * Output only. The system-generated UID of the resource. The UID is assigned
   * when the resource is created, and it is retained until it is deleted.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
  /**
   * Output only. Update time stamp
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
   * Output only. This is set for the read-write VM of the PRIMARY instance
   * only.
   *
   * @param Node $writableNode
   */
  public function setWritableNode(Node $writableNode)
  {
    $this->writableNode = $writableNode;
  }
  /**
   * @return Node
   */
  public function getWritableNode()
  {
    return $this->writableNode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Instance::class, 'Google_Service_CloudAlloyDBAdmin_Instance');
