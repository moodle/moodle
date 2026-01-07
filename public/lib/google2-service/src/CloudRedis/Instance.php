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

namespace Google\Service\CloudRedis;

class Instance extends \Google\Collection
{
  /**
   * Not set.
   */
  public const CONNECT_MODE_CONNECT_MODE_UNSPECIFIED = 'CONNECT_MODE_UNSPECIFIED';
  /**
   * Connect via direct peering to the Memorystore for Redis hosted service.
   */
  public const CONNECT_MODE_DIRECT_PEERING = 'DIRECT_PEERING';
  /**
   * Connect your Memorystore for Redis instance using Private Service Access.
   * Private services access provides an IP address range for multiple Google
   * Cloud services, including Memorystore.
   */
  public const CONNECT_MODE_PRIVATE_SERVICE_ACCESS = 'PRIVATE_SERVICE_ACCESS';
  /**
   * If not set, Memorystore Redis backend will default to
   * READ_REPLICAS_DISABLED.
   */
  public const READ_REPLICAS_MODE_READ_REPLICAS_MODE_UNSPECIFIED = 'READ_REPLICAS_MODE_UNSPECIFIED';
  /**
   * If disabled, read endpoint will not be provided and the instance cannot
   * scale up or down the number of replicas.
   */
  public const READ_REPLICAS_MODE_READ_REPLICAS_DISABLED = 'READ_REPLICAS_DISABLED';
  /**
   * If enabled, read endpoint will be provided and the instance can scale up
   * and down the number of replicas. Not valid for basic tier.
   */
  public const READ_REPLICAS_MODE_READ_REPLICAS_ENABLED = 'READ_REPLICAS_ENABLED';
  /**
   * Not set.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Redis instance is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * Redis instance has been created and is fully usable.
   */
  public const STATE_READY = 'READY';
  /**
   * Redis instance configuration is being updated. Certain kinds of updates may
   * cause the instance to become unusable while the update is in progress.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * Redis instance is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Redis instance is being repaired and may be unusable.
   */
  public const STATE_REPAIRING = 'REPAIRING';
  /**
   * Maintenance is being performed on this Redis instance.
   */
  public const STATE_MAINTENANCE = 'MAINTENANCE';
  /**
   * Redis instance is importing data (availability may be affected).
   */
  public const STATE_IMPORTING = 'IMPORTING';
  /**
   * Redis instance is failing over (availability may be affected).
   */
  public const STATE_FAILING_OVER = 'FAILING_OVER';
  /**
   * Not set.
   */
  public const TIER_TIER_UNSPECIFIED = 'TIER_UNSPECIFIED';
  /**
   * BASIC tier: standalone instance
   */
  public const TIER_BASIC = 'BASIC';
  /**
   * STANDARD_HA tier: highly available primary/replica instances
   */
  public const TIER_STANDARD_HA = 'STANDARD_HA';
  /**
   * Not set.
   */
  public const TRANSIT_ENCRYPTION_MODE_TRANSIT_ENCRYPTION_MODE_UNSPECIFIED = 'TRANSIT_ENCRYPTION_MODE_UNSPECIFIED';
  /**
   * Client to Server traffic encryption enabled with server authentication.
   */
  public const TRANSIT_ENCRYPTION_MODE_SERVER_AUTHENTICATION = 'SERVER_AUTHENTICATION';
  /**
   * TLS is disabled for the instance.
   */
  public const TRANSIT_ENCRYPTION_MODE_DISABLED = 'DISABLED';
  protected $collection_key = 'suspensionReasons';
  /**
   * Optional. If specified, at least one node will be provisioned in this zone
   * in addition to the zone specified in location_id. Only applicable to
   * standard tier. If provided, it must be a different zone from the one
   * provided in [location_id]. Additional nodes beyond the first 2 will be
   * placed in zones selected by the service.
   *
   * @var string
   */
  public $alternativeLocationId;
  /**
   * Optional. Indicates whether OSS Redis AUTH is enabled for the instance. If
   * set to "true" AUTH is enabled on the instance. Default value is "false"
   * meaning AUTH is disabled.
   *
   * @var bool
   */
  public $authEnabled;
  /**
   * Optional. The full name of the Google Compute Engine
   * [network](https://cloud.google.com/vpc/docs/vpc) to which the instance is
   * connected. If left unspecified, the `default` network will be used.
   *
   * @var string
   */
  public $authorizedNetwork;
  /**
   * Optional. The available maintenance versions that an instance could update
   * to.
   *
   * @var string[]
   */
  public $availableMaintenanceVersions;
  /**
   * Optional. The network connect mode of the Redis instance. If not provided,
   * the connect mode defaults to DIRECT_PEERING.
   *
   * @var string
   */
  public $connectMode;
  /**
   * Output only. The time the instance was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The current zone where the Redis primary node is located. In
   * basic tier, this will always be the same as [location_id]. In standard
   * tier, this can be the zone of any node in the instance.
   *
   * @var string
   */
  public $currentLocationId;
  /**
   * Optional. The KMS key reference that the customer provides when trying to
   * create the instance.
   *
   * @var string
   */
  public $customerManagedKey;
  /**
   * An arbitrary and optional user-provided name for the instance.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. Hostname or IP address of the exposed Redis endpoint used by
   * clients to connect to the service.
   *
   * @var string
   */
  public $host;
  /**
   * Resource labels to represent user provided metadata
   *
   * @var string[]
   */
  public $labels;
  /**
   * Optional. The zone where the instance will be provisioned. If not provided,
   * the service will choose a zone from the specified region for the instance.
   * For standard tier, additional nodes will be added across multiple zones for
   * protection against zonal failures. If specified, at least one node will be
   * provisioned in this zone.
   *
   * @var string
   */
  public $locationId;
  protected $maintenancePolicyType = MaintenancePolicy::class;
  protected $maintenancePolicyDataType = '';
  protected $maintenanceScheduleType = MaintenanceSchedule::class;
  protected $maintenanceScheduleDataType = '';
  /**
   * Optional. The self service update maintenance version. The version is date
   * based such as "20210712_00_00".
   *
   * @var string
   */
  public $maintenanceVersion;
  /**
   * Required. Redis memory size in GiB.
   *
   * @var int
   */
  public $memorySizeGb;
  /**
   * Required. Unique name of the resource in this scope including project and
   * location using the form:
   * `projects/{project_id}/locations/{location_id}/instances/{instance_id}`
   * Note: Redis instances are managed and addressed at regional level so
   * location_id here refers to a GCP region; however, users may choose which
   * specific zone (or collection of zones for cross-zone instances) an instance
   * should be provisioned in. Refer to location_id and alternative_location_id
   * fields for more details.
   *
   * @var string
   */
  public $name;
  protected $nodesType = NodeInfo::class;
  protected $nodesDataType = 'array';
  protected $persistenceConfigType = PersistenceConfig::class;
  protected $persistenceConfigDataType = '';
  /**
   * Output only. Cloud IAM identity used by import / export operations to
   * transfer data to/from Cloud Storage. Format is "serviceAccount:". The value
   * may change over time for a given instance so should be checked before each
   * import/export operation.
   *
   * @var string
   */
  public $persistenceIamIdentity;
  /**
   * Output only. The port number of the exposed Redis endpoint.
   *
   * @var int
   */
  public $port;
  /**
   * Output only. Hostname or IP address of the exposed readonly Redis endpoint.
   * Standard tier only. Targets all healthy replica nodes in instance.
   * Replication is asynchronous and replica nodes will exhibit some lag behind
   * the primary. Write requests must target 'host'.
   *
   * @var string
   */
  public $readEndpoint;
  /**
   * Output only. The port number of the exposed readonly redis endpoint.
   * Standard tier only. Write requests should target 'port'.
   *
   * @var int
   */
  public $readEndpointPort;
  /**
   * Optional. Read replicas mode for the instance. Defaults to
   * READ_REPLICAS_DISABLED.
   *
   * @var string
   */
  public $readReplicasMode;
  /**
   * Optional. Redis configuration parameters, according to
   * http://redis.io/topics/config. Currently, the only supported parameters
   * are: Redis version 3.2 and newer: * maxmemory-policy * notify-keyspace-
   * events Redis version 4.0 and newer: * activedefrag * lfu-decay-time * lfu-
   * log-factor * maxmemory-gb Redis version 5.0 and newer: * stream-node-max-
   * bytes * stream-node-max-entries
   *
   * @var string[]
   */
  public $redisConfigs;
  /**
   * Optional. The version of Redis software. If not provided, the default
   * version will be used. Currently, the supported values are: * `REDIS_3_2`
   * for Redis 3.2 compatibility * `REDIS_4_0` for Redis 4.0 compatibility *
   * `REDIS_5_0` for Redis 5.0 compatibility * `REDIS_6_X` for Redis 6.x
   * compatibility * `REDIS_7_0` for Redis 7.0 compatibility (default) *
   * `REDIS_7_2` for Redis 7.2 compatibility
   *
   * @var string
   */
  public $redisVersion;
  /**
   * Optional. The number of replica nodes. The valid range for the Standard
   * Tier with read replicas enabled is [1-5] and defaults to 2. If read
   * replicas are not enabled for a Standard Tier instance, the only valid value
   * is 1 and the default is 1. The valid value for basic tier is 0 and the
   * default is also 0.
   *
   * @var int
   */
  public $replicaCount;
  /**
   * Optional. For DIRECT_PEERING mode, the CIDR range of internal addresses
   * that are reserved for this instance. Range must be unique and non-
   * overlapping with existing subnets in an authorized network. For
   * PRIVATE_SERVICE_ACCESS mode, the name of one allocated IP address ranges
   * associated with this private service access connection. If not provided,
   * the service will choose an unused /29 block, for example, 10.0.0.0/29 or
   * 192.168.0.0/29. For READ_REPLICAS_ENABLED the default block size is /28.
   *
   * @var string
   */
  public $reservedIpRange;
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
   * Optional. Additional IP range for node placement. Required when enabling
   * read replicas on an existing instance. For DIRECT_PEERING mode value must
   * be a CIDR range of size /28, or "auto". For PRIVATE_SERVICE_ACCESS mode
   * value must be the name of an allocated address range associated with the
   * private service access connection, or "auto".
   *
   * @var string
   */
  public $secondaryIpRange;
  protected $serverCaCertsType = TlsCertificate::class;
  protected $serverCaCertsDataType = 'array';
  /**
   * Output only. The current state of this instance.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Additional information about the current status of this
   * instance, if available.
   *
   * @var string
   */
  public $statusMessage;
  /**
   * Optional. reasons that causes instance in "SUSPENDED" state.
   *
   * @var string[]
   */
  public $suspensionReasons;
  /**
   * Optional. Input only. Immutable. Tag keys/values directly bound to this
   * resource. For example: "123/environment": "production", "123/costCenter":
   * "marketing"
   *
   * @var string[]
   */
  public $tags;
  /**
   * Required. The service tier of the instance.
   *
   * @var string
   */
  public $tier;
  /**
   * Optional. The TLS mode of the Redis instance. If not provided, TLS is
   * disabled for the instance.
   *
   * @var string
   */
  public $transitEncryptionMode;

  /**
   * Optional. If specified, at least one node will be provisioned in this zone
   * in addition to the zone specified in location_id. Only applicable to
   * standard tier. If provided, it must be a different zone from the one
   * provided in [location_id]. Additional nodes beyond the first 2 will be
   * placed in zones selected by the service.
   *
   * @param string $alternativeLocationId
   */
  public function setAlternativeLocationId($alternativeLocationId)
  {
    $this->alternativeLocationId = $alternativeLocationId;
  }
  /**
   * @return string
   */
  public function getAlternativeLocationId()
  {
    return $this->alternativeLocationId;
  }
  /**
   * Optional. Indicates whether OSS Redis AUTH is enabled for the instance. If
   * set to "true" AUTH is enabled on the instance. Default value is "false"
   * meaning AUTH is disabled.
   *
   * @param bool $authEnabled
   */
  public function setAuthEnabled($authEnabled)
  {
    $this->authEnabled = $authEnabled;
  }
  /**
   * @return bool
   */
  public function getAuthEnabled()
  {
    return $this->authEnabled;
  }
  /**
   * Optional. The full name of the Google Compute Engine
   * [network](https://cloud.google.com/vpc/docs/vpc) to which the instance is
   * connected. If left unspecified, the `default` network will be used.
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
   * Optional. The available maintenance versions that an instance could update
   * to.
   *
   * @param string[] $availableMaintenanceVersions
   */
  public function setAvailableMaintenanceVersions($availableMaintenanceVersions)
  {
    $this->availableMaintenanceVersions = $availableMaintenanceVersions;
  }
  /**
   * @return string[]
   */
  public function getAvailableMaintenanceVersions()
  {
    return $this->availableMaintenanceVersions;
  }
  /**
   * Optional. The network connect mode of the Redis instance. If not provided,
   * the connect mode defaults to DIRECT_PEERING.
   *
   * Accepted values: CONNECT_MODE_UNSPECIFIED, DIRECT_PEERING,
   * PRIVATE_SERVICE_ACCESS
   *
   * @param self::CONNECT_MODE_* $connectMode
   */
  public function setConnectMode($connectMode)
  {
    $this->connectMode = $connectMode;
  }
  /**
   * @return self::CONNECT_MODE_*
   */
  public function getConnectMode()
  {
    return $this->connectMode;
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
   * Output only. The current zone where the Redis primary node is located. In
   * basic tier, this will always be the same as [location_id]. In standard
   * tier, this can be the zone of any node in the instance.
   *
   * @param string $currentLocationId
   */
  public function setCurrentLocationId($currentLocationId)
  {
    $this->currentLocationId = $currentLocationId;
  }
  /**
   * @return string
   */
  public function getCurrentLocationId()
  {
    return $this->currentLocationId;
  }
  /**
   * Optional. The KMS key reference that the customer provides when trying to
   * create the instance.
   *
   * @param string $customerManagedKey
   */
  public function setCustomerManagedKey($customerManagedKey)
  {
    $this->customerManagedKey = $customerManagedKey;
  }
  /**
   * @return string
   */
  public function getCustomerManagedKey()
  {
    return $this->customerManagedKey;
  }
  /**
   * An arbitrary and optional user-provided name for the instance.
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
   * Output only. Hostname or IP address of the exposed Redis endpoint used by
   * clients to connect to the service.
   *
   * @param string $host
   */
  public function setHost($host)
  {
    $this->host = $host;
  }
  /**
   * @return string
   */
  public function getHost()
  {
    return $this->host;
  }
  /**
   * Resource labels to represent user provided metadata
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
   * Optional. The zone where the instance will be provisioned. If not provided,
   * the service will choose a zone from the specified region for the instance.
   * For standard tier, additional nodes will be added across multiple zones for
   * protection against zonal failures. If specified, at least one node will be
   * provisioned in this zone.
   *
   * @param string $locationId
   */
  public function setLocationId($locationId)
  {
    $this->locationId = $locationId;
  }
  /**
   * @return string
   */
  public function getLocationId()
  {
    return $this->locationId;
  }
  /**
   * Optional. The maintenance policy for the instance. If not provided,
   * maintenance events can be performed at any time.
   *
   * @param MaintenancePolicy $maintenancePolicy
   */
  public function setMaintenancePolicy(MaintenancePolicy $maintenancePolicy)
  {
    $this->maintenancePolicy = $maintenancePolicy;
  }
  /**
   * @return MaintenancePolicy
   */
  public function getMaintenancePolicy()
  {
    return $this->maintenancePolicy;
  }
  /**
   * Output only. Date and time of upcoming maintenance events which have been
   * scheduled.
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
   * Optional. The self service update maintenance version. The version is date
   * based such as "20210712_00_00".
   *
   * @param string $maintenanceVersion
   */
  public function setMaintenanceVersion($maintenanceVersion)
  {
    $this->maintenanceVersion = $maintenanceVersion;
  }
  /**
   * @return string
   */
  public function getMaintenanceVersion()
  {
    return $this->maintenanceVersion;
  }
  /**
   * Required. Redis memory size in GiB.
   *
   * @param int $memorySizeGb
   */
  public function setMemorySizeGb($memorySizeGb)
  {
    $this->memorySizeGb = $memorySizeGb;
  }
  /**
   * @return int
   */
  public function getMemorySizeGb()
  {
    return $this->memorySizeGb;
  }
  /**
   * Required. Unique name of the resource in this scope including project and
   * location using the form:
   * `projects/{project_id}/locations/{location_id}/instances/{instance_id}`
   * Note: Redis instances are managed and addressed at regional level so
   * location_id here refers to a GCP region; however, users may choose which
   * specific zone (or collection of zones for cross-zone instances) an instance
   * should be provisioned in. Refer to location_id and alternative_location_id
   * fields for more details.
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
   * Output only. Info per node.
   *
   * @param NodeInfo[] $nodes
   */
  public function setNodes($nodes)
  {
    $this->nodes = $nodes;
  }
  /**
   * @return NodeInfo[]
   */
  public function getNodes()
  {
    return $this->nodes;
  }
  /**
   * Optional. Persistence configuration parameters
   *
   * @param PersistenceConfig $persistenceConfig
   */
  public function setPersistenceConfig(PersistenceConfig $persistenceConfig)
  {
    $this->persistenceConfig = $persistenceConfig;
  }
  /**
   * @return PersistenceConfig
   */
  public function getPersistenceConfig()
  {
    return $this->persistenceConfig;
  }
  /**
   * Output only. Cloud IAM identity used by import / export operations to
   * transfer data to/from Cloud Storage. Format is "serviceAccount:". The value
   * may change over time for a given instance so should be checked before each
   * import/export operation.
   *
   * @param string $persistenceIamIdentity
   */
  public function setPersistenceIamIdentity($persistenceIamIdentity)
  {
    $this->persistenceIamIdentity = $persistenceIamIdentity;
  }
  /**
   * @return string
   */
  public function getPersistenceIamIdentity()
  {
    return $this->persistenceIamIdentity;
  }
  /**
   * Output only. The port number of the exposed Redis endpoint.
   *
   * @param int $port
   */
  public function setPort($port)
  {
    $this->port = $port;
  }
  /**
   * @return int
   */
  public function getPort()
  {
    return $this->port;
  }
  /**
   * Output only. Hostname or IP address of the exposed readonly Redis endpoint.
   * Standard tier only. Targets all healthy replica nodes in instance.
   * Replication is asynchronous and replica nodes will exhibit some lag behind
   * the primary. Write requests must target 'host'.
   *
   * @param string $readEndpoint
   */
  public function setReadEndpoint($readEndpoint)
  {
    $this->readEndpoint = $readEndpoint;
  }
  /**
   * @return string
   */
  public function getReadEndpoint()
  {
    return $this->readEndpoint;
  }
  /**
   * Output only. The port number of the exposed readonly redis endpoint.
   * Standard tier only. Write requests should target 'port'.
   *
   * @param int $readEndpointPort
   */
  public function setReadEndpointPort($readEndpointPort)
  {
    $this->readEndpointPort = $readEndpointPort;
  }
  /**
   * @return int
   */
  public function getReadEndpointPort()
  {
    return $this->readEndpointPort;
  }
  /**
   * Optional. Read replicas mode for the instance. Defaults to
   * READ_REPLICAS_DISABLED.
   *
   * Accepted values: READ_REPLICAS_MODE_UNSPECIFIED, READ_REPLICAS_DISABLED,
   * READ_REPLICAS_ENABLED
   *
   * @param self::READ_REPLICAS_MODE_* $readReplicasMode
   */
  public function setReadReplicasMode($readReplicasMode)
  {
    $this->readReplicasMode = $readReplicasMode;
  }
  /**
   * @return self::READ_REPLICAS_MODE_*
   */
  public function getReadReplicasMode()
  {
    return $this->readReplicasMode;
  }
  /**
   * Optional. Redis configuration parameters, according to
   * http://redis.io/topics/config. Currently, the only supported parameters
   * are: Redis version 3.2 and newer: * maxmemory-policy * notify-keyspace-
   * events Redis version 4.0 and newer: * activedefrag * lfu-decay-time * lfu-
   * log-factor * maxmemory-gb Redis version 5.0 and newer: * stream-node-max-
   * bytes * stream-node-max-entries
   *
   * @param string[] $redisConfigs
   */
  public function setRedisConfigs($redisConfigs)
  {
    $this->redisConfigs = $redisConfigs;
  }
  /**
   * @return string[]
   */
  public function getRedisConfigs()
  {
    return $this->redisConfigs;
  }
  /**
   * Optional. The version of Redis software. If not provided, the default
   * version will be used. Currently, the supported values are: * `REDIS_3_2`
   * for Redis 3.2 compatibility * `REDIS_4_0` for Redis 4.0 compatibility *
   * `REDIS_5_0` for Redis 5.0 compatibility * `REDIS_6_X` for Redis 6.x
   * compatibility * `REDIS_7_0` for Redis 7.0 compatibility (default) *
   * `REDIS_7_2` for Redis 7.2 compatibility
   *
   * @param string $redisVersion
   */
  public function setRedisVersion($redisVersion)
  {
    $this->redisVersion = $redisVersion;
  }
  /**
   * @return string
   */
  public function getRedisVersion()
  {
    return $this->redisVersion;
  }
  /**
   * Optional. The number of replica nodes. The valid range for the Standard
   * Tier with read replicas enabled is [1-5] and defaults to 2. If read
   * replicas are not enabled for a Standard Tier instance, the only valid value
   * is 1 and the default is 1. The valid value for basic tier is 0 and the
   * default is also 0.
   *
   * @param int $replicaCount
   */
  public function setReplicaCount($replicaCount)
  {
    $this->replicaCount = $replicaCount;
  }
  /**
   * @return int
   */
  public function getReplicaCount()
  {
    return $this->replicaCount;
  }
  /**
   * Optional. For DIRECT_PEERING mode, the CIDR range of internal addresses
   * that are reserved for this instance. Range must be unique and non-
   * overlapping with existing subnets in an authorized network. For
   * PRIVATE_SERVICE_ACCESS mode, the name of one allocated IP address ranges
   * associated with this private service access connection. If not provided,
   * the service will choose an unused /29 block, for example, 10.0.0.0/29 or
   * 192.168.0.0/29. For READ_REPLICAS_ENABLED the default block size is /28.
   *
   * @param string $reservedIpRange
   */
  public function setReservedIpRange($reservedIpRange)
  {
    $this->reservedIpRange = $reservedIpRange;
  }
  /**
   * @return string
   */
  public function getReservedIpRange()
  {
    return $this->reservedIpRange;
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
   * Optional. Additional IP range for node placement. Required when enabling
   * read replicas on an existing instance. For DIRECT_PEERING mode value must
   * be a CIDR range of size /28, or "auto". For PRIVATE_SERVICE_ACCESS mode
   * value must be the name of an allocated address range associated with the
   * private service access connection, or "auto".
   *
   * @param string $secondaryIpRange
   */
  public function setSecondaryIpRange($secondaryIpRange)
  {
    $this->secondaryIpRange = $secondaryIpRange;
  }
  /**
   * @return string
   */
  public function getSecondaryIpRange()
  {
    return $this->secondaryIpRange;
  }
  /**
   * Output only. List of server CA certificates for the instance.
   *
   * @param TlsCertificate[] $serverCaCerts
   */
  public function setServerCaCerts($serverCaCerts)
  {
    $this->serverCaCerts = $serverCaCerts;
  }
  /**
   * @return TlsCertificate[]
   */
  public function getServerCaCerts()
  {
    return $this->serverCaCerts;
  }
  /**
   * Output only. The current state of this instance.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, READY, UPDATING, DELETING,
   * REPAIRING, MAINTENANCE, IMPORTING, FAILING_OVER
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
   * Output only. Additional information about the current status of this
   * instance, if available.
   *
   * @param string $statusMessage
   */
  public function setStatusMessage($statusMessage)
  {
    $this->statusMessage = $statusMessage;
  }
  /**
   * @return string
   */
  public function getStatusMessage()
  {
    return $this->statusMessage;
  }
  /**
   * Optional. reasons that causes instance in "SUSPENDED" state.
   *
   * @param string[] $suspensionReasons
   */
  public function setSuspensionReasons($suspensionReasons)
  {
    $this->suspensionReasons = $suspensionReasons;
  }
  /**
   * @return string[]
   */
  public function getSuspensionReasons()
  {
    return $this->suspensionReasons;
  }
  /**
   * Optional. Input only. Immutable. Tag keys/values directly bound to this
   * resource. For example: "123/environment": "production", "123/costCenter":
   * "marketing"
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
   * Required. The service tier of the instance.
   *
   * Accepted values: TIER_UNSPECIFIED, BASIC, STANDARD_HA
   *
   * @param self::TIER_* $tier
   */
  public function setTier($tier)
  {
    $this->tier = $tier;
  }
  /**
   * @return self::TIER_*
   */
  public function getTier()
  {
    return $this->tier;
  }
  /**
   * Optional. The TLS mode of the Redis instance. If not provided, TLS is
   * disabled for the instance.
   *
   * Accepted values: TRANSIT_ENCRYPTION_MODE_UNSPECIFIED,
   * SERVER_AUTHENTICATION, DISABLED
   *
   * @param self::TRANSIT_ENCRYPTION_MODE_* $transitEncryptionMode
   */
  public function setTransitEncryptionMode($transitEncryptionMode)
  {
    $this->transitEncryptionMode = $transitEncryptionMode;
  }
  /**
   * @return self::TRANSIT_ENCRYPTION_MODE_*
   */
  public function getTransitEncryptionMode()
  {
    return $this->transitEncryptionMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Instance::class, 'Google_Service_CloudRedis_Instance');
