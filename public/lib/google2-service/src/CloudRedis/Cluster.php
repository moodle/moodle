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

class Cluster extends \Google\Collection
{
  /**
   * Not set.
   */
  public const AUTHORIZATION_MODE_AUTH_MODE_UNSPECIFIED = 'AUTH_MODE_UNSPECIFIED';
  /**
   * IAM basic authorization mode
   */
  public const AUTHORIZATION_MODE_AUTH_MODE_IAM_AUTH = 'AUTH_MODE_IAM_AUTH';
  /**
   * Authorization disabled mode
   */
  public const AUTHORIZATION_MODE_AUTH_MODE_DISABLED = 'AUTH_MODE_DISABLED';
  /**
   * Node type unspecified
   */
  public const NODE_TYPE_NODE_TYPE_UNSPECIFIED = 'NODE_TYPE_UNSPECIFIED';
  /**
   * Redis shared core nano node_type.
   */
  public const NODE_TYPE_REDIS_SHARED_CORE_NANO = 'REDIS_SHARED_CORE_NANO';
  /**
   * Redis highmem medium node_type.
   */
  public const NODE_TYPE_REDIS_HIGHMEM_MEDIUM = 'REDIS_HIGHMEM_MEDIUM';
  /**
   * Redis highmem xlarge node_type.
   */
  public const NODE_TYPE_REDIS_HIGHMEM_XLARGE = 'REDIS_HIGHMEM_XLARGE';
  /**
   * Redis standard small node_type.
   */
  public const NODE_TYPE_REDIS_STANDARD_SMALL = 'REDIS_STANDARD_SMALL';
  /**
   * Not set.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Redis cluster is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * Redis cluster has been created and is fully usable.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * Redis cluster configuration is being updated.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * Redis cluster is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * In-transit encryption not set.
   */
  public const TRANSIT_ENCRYPTION_MODE_TRANSIT_ENCRYPTION_MODE_UNSPECIFIED = 'TRANSIT_ENCRYPTION_MODE_UNSPECIFIED';
  /**
   * In-transit encryption disabled.
   */
  public const TRANSIT_ENCRYPTION_MODE_TRANSIT_ENCRYPTION_MODE_DISABLED = 'TRANSIT_ENCRYPTION_MODE_DISABLED';
  /**
   * Use server managed encryption for in-transit encryption.
   */
  public const TRANSIT_ENCRYPTION_MODE_TRANSIT_ENCRYPTION_MODE_SERVER_AUTHENTICATION = 'TRANSIT_ENCRYPTION_MODE_SERVER_AUTHENTICATION';
  protected $collection_key = 'pscServiceAttachments';
  /**
   * Optional. Immutable. Deprecated, do not use.
   *
   * @deprecated
   * @var bool
   */
  public $allowFewerZonesDeployment;
  /**
   * Optional. If true, cluster endpoints that are created and registered by
   * customers can be deleted asynchronously. That is, such a cluster endpoint
   * can be de-registered before the forwarding rules in the cluster endpoint
   * are deleted.
   *
   * @var bool
   */
  public $asyncClusterEndpointsDeletionEnabled;
  /**
   * Optional. The authorization mode of the Redis cluster. If not provided,
   * auth feature is disabled for the cluster.
   *
   * @var string
   */
  public $authorizationMode;
  protected $automatedBackupConfigType = AutomatedBackupConfig::class;
  protected $automatedBackupConfigDataType = '';
  /**
   * Output only. This field is used to determine the available maintenance
   * versions for the self service update.
   *
   * @var string[]
   */
  public $availableMaintenanceVersions;
  /**
   * Optional. Output only. The backup collection full resource name. Example:
   * projects/{project}/locations/{location}/backupCollections/{collection}
   *
   * @var string
   */
  public $backupCollection;
  protected $clusterEndpointsType = ClusterEndpoint::class;
  protected $clusterEndpointsDataType = 'array';
  /**
   * Output only. The timestamp associated with the cluster creation request.
   *
   * @var string
   */
  public $createTime;
  protected $crossClusterReplicationConfigType = CrossClusterReplicationConfig::class;
  protected $crossClusterReplicationConfigDataType = '';
  /**
   * Optional. The delete operation will fail when the value is set to true.
   *
   * @var bool
   */
  public $deletionProtectionEnabled;
  protected $discoveryEndpointsType = DiscoveryEndpoint::class;
  protected $discoveryEndpointsDataType = 'array';
  /**
   * Output only. This field represents the actual maintenance version of the
   * cluster.
   *
   * @var string
   */
  public $effectiveMaintenanceVersion;
  protected $encryptionInfoType = EncryptionInfo::class;
  protected $encryptionInfoDataType = '';
  protected $gcsSourceType = GcsBackupSource::class;
  protected $gcsSourceDataType = '';
  /**
   * Optional. The KMS key used to encrypt the at-rest data of the cluster.
   *
   * @var string
   */
  public $kmsKey;
  /**
   * Optional. Labels to represent user-provided metadata.
   *
   * @var string[]
   */
  public $labels;
  protected $maintenancePolicyType = ClusterMaintenancePolicy::class;
  protected $maintenancePolicyDataType = '';
  protected $maintenanceScheduleType = ClusterMaintenanceSchedule::class;
  protected $maintenanceScheduleDataType = '';
  /**
   * Optional. This field can be used to trigger self service update to indicate
   * the desired maintenance version. The input to this field can be determined
   * by the available_maintenance_versions field.
   *
   * @var string
   */
  public $maintenanceVersion;
  protected $managedBackupSourceType = ManagedBackupSource::class;
  protected $managedBackupSourceDataType = '';
  /**
   * Required. Identifier. Unique name of the resource in this scope including
   * project and location using the form:
   * `projects/{project_id}/locations/{location_id}/clusters/{cluster_id}`
   *
   * @var string
   */
  public $name;
  /**
   * Optional. The type of a redis node in the cluster. NodeType determines the
   * underlying machine-type of a redis node.
   *
   * @var string
   */
  public $nodeType;
  /**
   * Optional. Input only. Ondemand maintenance for the cluster. This field can
   * be used to trigger ondemand critical update on the cluster.
   *
   * @deprecated
   * @var bool
   */
  public $ondemandMaintenance;
  protected $persistenceConfigType = ClusterPersistenceConfig::class;
  protected $persistenceConfigDataType = '';
  /**
   * Output only. Precise value of redis memory size in GB for the entire
   * cluster.
   *
   * @var 
   */
  public $preciseSizeGb;
  protected $pscConfigsType = PscConfig::class;
  protected $pscConfigsDataType = 'array';
  protected $pscConnectionsType = PscConnection::class;
  protected $pscConnectionsDataType = 'array';
  protected $pscServiceAttachmentsType = PscServiceAttachment::class;
  protected $pscServiceAttachmentsDataType = 'array';
  /**
   * Optional. Key/Value pairs of customer overrides for mutable Redis Configs
   *
   * @var string[]
   */
  public $redisConfigs;
  /**
   * Optional. The number of replica nodes per shard.
   *
   * @var int
   */
  public $replicaCount;
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
   * Optional. Number of shards for the Redis cluster.
   *
   * @var int
   */
  public $shardCount;
  /**
   * Optional. Input only. Simulate a maintenance event.
   *
   * @var bool
   */
  public $simulateMaintenanceEvent;
  /**
   * Output only. Redis memory size in GB for the entire cluster rounded up to
   * the next integer.
   *
   * @var int
   */
  public $sizeGb;
  /**
   * Output only. The current state of this cluster. Can be CREATING, READY,
   * UPDATING, DELETING and SUSPENDED
   *
   * @var string
   */
  public $state;
  protected $stateInfoType = StateInfo::class;
  protected $stateInfoDataType = '';
  /**
   * Optional. The in-transit encryption for the Redis cluster. If not provided,
   * encryption is disabled for the cluster.
   *
   * @var string
   */
  public $transitEncryptionMode;
  /**
   * Output only. System assigned, unique identifier for the cluster.
   *
   * @var string
   */
  public $uid;
  protected $zoneDistributionConfigType = ZoneDistributionConfig::class;
  protected $zoneDistributionConfigDataType = '';

  /**
   * Optional. Immutable. Deprecated, do not use.
   *
   * @deprecated
   * @param bool $allowFewerZonesDeployment
   */
  public function setAllowFewerZonesDeployment($allowFewerZonesDeployment)
  {
    $this->allowFewerZonesDeployment = $allowFewerZonesDeployment;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getAllowFewerZonesDeployment()
  {
    return $this->allowFewerZonesDeployment;
  }
  /**
   * Optional. If true, cluster endpoints that are created and registered by
   * customers can be deleted asynchronously. That is, such a cluster endpoint
   * can be de-registered before the forwarding rules in the cluster endpoint
   * are deleted.
   *
   * @param bool $asyncClusterEndpointsDeletionEnabled
   */
  public function setAsyncClusterEndpointsDeletionEnabled($asyncClusterEndpointsDeletionEnabled)
  {
    $this->asyncClusterEndpointsDeletionEnabled = $asyncClusterEndpointsDeletionEnabled;
  }
  /**
   * @return bool
   */
  public function getAsyncClusterEndpointsDeletionEnabled()
  {
    return $this->asyncClusterEndpointsDeletionEnabled;
  }
  /**
   * Optional. The authorization mode of the Redis cluster. If not provided,
   * auth feature is disabled for the cluster.
   *
   * Accepted values: AUTH_MODE_UNSPECIFIED, AUTH_MODE_IAM_AUTH,
   * AUTH_MODE_DISABLED
   *
   * @param self::AUTHORIZATION_MODE_* $authorizationMode
   */
  public function setAuthorizationMode($authorizationMode)
  {
    $this->authorizationMode = $authorizationMode;
  }
  /**
   * @return self::AUTHORIZATION_MODE_*
   */
  public function getAuthorizationMode()
  {
    return $this->authorizationMode;
  }
  /**
   * Optional. The automated backup config for the cluster.
   *
   * @param AutomatedBackupConfig $automatedBackupConfig
   */
  public function setAutomatedBackupConfig(AutomatedBackupConfig $automatedBackupConfig)
  {
    $this->automatedBackupConfig = $automatedBackupConfig;
  }
  /**
   * @return AutomatedBackupConfig
   */
  public function getAutomatedBackupConfig()
  {
    return $this->automatedBackupConfig;
  }
  /**
   * Output only. This field is used to determine the available maintenance
   * versions for the self service update.
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
   * Optional. Output only. The backup collection full resource name. Example:
   * projects/{project}/locations/{location}/backupCollections/{collection}
   *
   * @param string $backupCollection
   */
  public function setBackupCollection($backupCollection)
  {
    $this->backupCollection = $backupCollection;
  }
  /**
   * @return string
   */
  public function getBackupCollection()
  {
    return $this->backupCollection;
  }
  /**
   * Optional. A list of cluster endpoints.
   *
   * @param ClusterEndpoint[] $clusterEndpoints
   */
  public function setClusterEndpoints($clusterEndpoints)
  {
    $this->clusterEndpoints = $clusterEndpoints;
  }
  /**
   * @return ClusterEndpoint[]
   */
  public function getClusterEndpoints()
  {
    return $this->clusterEndpoints;
  }
  /**
   * Output only. The timestamp associated with the cluster creation request.
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
   * Optional. Cross cluster replication config.
   *
   * @param CrossClusterReplicationConfig $crossClusterReplicationConfig
   */
  public function setCrossClusterReplicationConfig(CrossClusterReplicationConfig $crossClusterReplicationConfig)
  {
    $this->crossClusterReplicationConfig = $crossClusterReplicationConfig;
  }
  /**
   * @return CrossClusterReplicationConfig
   */
  public function getCrossClusterReplicationConfig()
  {
    return $this->crossClusterReplicationConfig;
  }
  /**
   * Optional. The delete operation will fail when the value is set to true.
   *
   * @param bool $deletionProtectionEnabled
   */
  public function setDeletionProtectionEnabled($deletionProtectionEnabled)
  {
    $this->deletionProtectionEnabled = $deletionProtectionEnabled;
  }
  /**
   * @return bool
   */
  public function getDeletionProtectionEnabled()
  {
    return $this->deletionProtectionEnabled;
  }
  /**
   * Output only. Endpoints created on each given network, for Redis clients to
   * connect to the cluster. Currently only one discovery endpoint is supported.
   *
   * @param DiscoveryEndpoint[] $discoveryEndpoints
   */
  public function setDiscoveryEndpoints($discoveryEndpoints)
  {
    $this->discoveryEndpoints = $discoveryEndpoints;
  }
  /**
   * @return DiscoveryEndpoint[]
   */
  public function getDiscoveryEndpoints()
  {
    return $this->discoveryEndpoints;
  }
  /**
   * Output only. This field represents the actual maintenance version of the
   * cluster.
   *
   * @param string $effectiveMaintenanceVersion
   */
  public function setEffectiveMaintenanceVersion($effectiveMaintenanceVersion)
  {
    $this->effectiveMaintenanceVersion = $effectiveMaintenanceVersion;
  }
  /**
   * @return string
   */
  public function getEffectiveMaintenanceVersion()
  {
    return $this->effectiveMaintenanceVersion;
  }
  /**
   * Output only. Encryption information of the data at rest of the cluster.
   *
   * @param EncryptionInfo $encryptionInfo
   */
  public function setEncryptionInfo(EncryptionInfo $encryptionInfo)
  {
    $this->encryptionInfo = $encryptionInfo;
  }
  /**
   * @return EncryptionInfo
   */
  public function getEncryptionInfo()
  {
    return $this->encryptionInfo;
  }
  /**
   * Optional. Backups stored in Cloud Storage buckets. The Cloud Storage
   * buckets need to be the same region as the clusters. Read permission is
   * required to import from the provided Cloud Storage objects.
   *
   * @param GcsBackupSource $gcsSource
   */
  public function setGcsSource(GcsBackupSource $gcsSource)
  {
    $this->gcsSource = $gcsSource;
  }
  /**
   * @return GcsBackupSource
   */
  public function getGcsSource()
  {
    return $this->gcsSource;
  }
  /**
   * Optional. The KMS key used to encrypt the at-rest data of the cluster.
   *
   * @param string $kmsKey
   */
  public function setKmsKey($kmsKey)
  {
    $this->kmsKey = $kmsKey;
  }
  /**
   * @return string
   */
  public function getKmsKey()
  {
    return $this->kmsKey;
  }
  /**
   * Optional. Labels to represent user-provided metadata.
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
   * Optional. ClusterMaintenancePolicy determines when to allow or deny
   * updates.
   *
   * @param ClusterMaintenancePolicy $maintenancePolicy
   */
  public function setMaintenancePolicy(ClusterMaintenancePolicy $maintenancePolicy)
  {
    $this->maintenancePolicy = $maintenancePolicy;
  }
  /**
   * @return ClusterMaintenancePolicy
   */
  public function getMaintenancePolicy()
  {
    return $this->maintenancePolicy;
  }
  /**
   * Output only. ClusterMaintenanceSchedule Output only Published maintenance
   * schedule.
   *
   * @param ClusterMaintenanceSchedule $maintenanceSchedule
   */
  public function setMaintenanceSchedule(ClusterMaintenanceSchedule $maintenanceSchedule)
  {
    $this->maintenanceSchedule = $maintenanceSchedule;
  }
  /**
   * @return ClusterMaintenanceSchedule
   */
  public function getMaintenanceSchedule()
  {
    return $this->maintenanceSchedule;
  }
  /**
   * Optional. This field can be used to trigger self service update to indicate
   * the desired maintenance version. The input to this field can be determined
   * by the available_maintenance_versions field.
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
   * Optional. Backups generated and managed by memorystore service.
   *
   * @param ManagedBackupSource $managedBackupSource
   */
  public function setManagedBackupSource(ManagedBackupSource $managedBackupSource)
  {
    $this->managedBackupSource = $managedBackupSource;
  }
  /**
   * @return ManagedBackupSource
   */
  public function getManagedBackupSource()
  {
    return $this->managedBackupSource;
  }
  /**
   * Required. Identifier. Unique name of the resource in this scope including
   * project and location using the form:
   * `projects/{project_id}/locations/{location_id}/clusters/{cluster_id}`
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
   * Optional. The type of a redis node in the cluster. NodeType determines the
   * underlying machine-type of a redis node.
   *
   * Accepted values: NODE_TYPE_UNSPECIFIED, REDIS_SHARED_CORE_NANO,
   * REDIS_HIGHMEM_MEDIUM, REDIS_HIGHMEM_XLARGE, REDIS_STANDARD_SMALL
   *
   * @param self::NODE_TYPE_* $nodeType
   */
  public function setNodeType($nodeType)
  {
    $this->nodeType = $nodeType;
  }
  /**
   * @return self::NODE_TYPE_*
   */
  public function getNodeType()
  {
    return $this->nodeType;
  }
  /**
   * Optional. Input only. Ondemand maintenance for the cluster. This field can
   * be used to trigger ondemand critical update on the cluster.
   *
   * @deprecated
   * @param bool $ondemandMaintenance
   */
  public function setOndemandMaintenance($ondemandMaintenance)
  {
    $this->ondemandMaintenance = $ondemandMaintenance;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getOndemandMaintenance()
  {
    return $this->ondemandMaintenance;
  }
  /**
   * Optional. Persistence config (RDB, AOF) for the cluster.
   *
   * @param ClusterPersistenceConfig $persistenceConfig
   */
  public function setPersistenceConfig(ClusterPersistenceConfig $persistenceConfig)
  {
    $this->persistenceConfig = $persistenceConfig;
  }
  /**
   * @return ClusterPersistenceConfig
   */
  public function getPersistenceConfig()
  {
    return $this->persistenceConfig;
  }
  public function setPreciseSizeGb($preciseSizeGb)
  {
    $this->preciseSizeGb = $preciseSizeGb;
  }
  public function getPreciseSizeGb()
  {
    return $this->preciseSizeGb;
  }
  /**
   * Optional. Each PscConfig configures the consumer network where IPs will be
   * designated to the cluster for client access through Private Service Connect
   * Automation. Currently, only one PscConfig is supported.
   *
   * @param PscConfig[] $pscConfigs
   */
  public function setPscConfigs($pscConfigs)
  {
    $this->pscConfigs = $pscConfigs;
  }
  /**
   * @return PscConfig[]
   */
  public function getPscConfigs()
  {
    return $this->pscConfigs;
  }
  /**
   * Output only. The list of PSC connections that are auto-created through
   * service connectivity automation.
   *
   * @param PscConnection[] $pscConnections
   */
  public function setPscConnections($pscConnections)
  {
    $this->pscConnections = $pscConnections;
  }
  /**
   * @return PscConnection[]
   */
  public function getPscConnections()
  {
    return $this->pscConnections;
  }
  /**
   * Output only. Service attachment details to configure Psc connections
   *
   * @param PscServiceAttachment[] $pscServiceAttachments
   */
  public function setPscServiceAttachments($pscServiceAttachments)
  {
    $this->pscServiceAttachments = $pscServiceAttachments;
  }
  /**
   * @return PscServiceAttachment[]
   */
  public function getPscServiceAttachments()
  {
    return $this->pscServiceAttachments;
  }
  /**
   * Optional. Key/Value pairs of customer overrides for mutable Redis Configs
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
   * Optional. The number of replica nodes per shard.
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
   * Optional. Number of shards for the Redis cluster.
   *
   * @param int $shardCount
   */
  public function setShardCount($shardCount)
  {
    $this->shardCount = $shardCount;
  }
  /**
   * @return int
   */
  public function getShardCount()
  {
    return $this->shardCount;
  }
  /**
   * Optional. Input only. Simulate a maintenance event.
   *
   * @param bool $simulateMaintenanceEvent
   */
  public function setSimulateMaintenanceEvent($simulateMaintenanceEvent)
  {
    $this->simulateMaintenanceEvent = $simulateMaintenanceEvent;
  }
  /**
   * @return bool
   */
  public function getSimulateMaintenanceEvent()
  {
    return $this->simulateMaintenanceEvent;
  }
  /**
   * Output only. Redis memory size in GB for the entire cluster rounded up to
   * the next integer.
   *
   * @param int $sizeGb
   */
  public function setSizeGb($sizeGb)
  {
    $this->sizeGb = $sizeGb;
  }
  /**
   * @return int
   */
  public function getSizeGb()
  {
    return $this->sizeGb;
  }
  /**
   * Output only. The current state of this cluster. Can be CREATING, READY,
   * UPDATING, DELETING and SUSPENDED
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, ACTIVE, UPDATING, DELETING
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
   * Output only. Additional information about the current state of the cluster.
   *
   * @param StateInfo $stateInfo
   */
  public function setStateInfo(StateInfo $stateInfo)
  {
    $this->stateInfo = $stateInfo;
  }
  /**
   * @return StateInfo
   */
  public function getStateInfo()
  {
    return $this->stateInfo;
  }
  /**
   * Optional. The in-transit encryption for the Redis cluster. If not provided,
   * encryption is disabled for the cluster.
   *
   * Accepted values: TRANSIT_ENCRYPTION_MODE_UNSPECIFIED,
   * TRANSIT_ENCRYPTION_MODE_DISABLED,
   * TRANSIT_ENCRYPTION_MODE_SERVER_AUTHENTICATION
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
  /**
   * Output only. System assigned, unique identifier for the cluster.
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
   * Optional. This config will be used to determine how the customer wants us
   * to distribute cluster resources within the region.
   *
   * @param ZoneDistributionConfig $zoneDistributionConfig
   */
  public function setZoneDistributionConfig(ZoneDistributionConfig $zoneDistributionConfig)
  {
    $this->zoneDistributionConfig = $zoneDistributionConfig;
  }
  /**
   * @return ZoneDistributionConfig
   */
  public function getZoneDistributionConfig()
  {
    return $this->zoneDistributionConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Cluster::class, 'Google_Service_CloudRedis_Cluster');
