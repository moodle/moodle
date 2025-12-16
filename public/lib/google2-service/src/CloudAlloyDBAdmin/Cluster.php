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

class Cluster extends \Google\Model
{
  /**
   * The type of the cluster is unknown.
   */
  public const CLUSTER_TYPE_CLUSTER_TYPE_UNSPECIFIED = 'CLUSTER_TYPE_UNSPECIFIED';
  /**
   * Primary cluster that support read and write operations.
   */
  public const CLUSTER_TYPE_PRIMARY = 'PRIMARY';
  /**
   * Secondary cluster that is replicating from another region. This only
   * supports read.
   */
  public const CLUSTER_TYPE_SECONDARY = 'SECONDARY';
  /**
   * This is an unknown database version.
   */
  public const DATABASE_VERSION_DATABASE_VERSION_UNSPECIFIED = 'DATABASE_VERSION_UNSPECIFIED';
  /**
   * DEPRECATED - The database version is Postgres 13.
   *
   * @deprecated
   */
  public const DATABASE_VERSION_POSTGRES_13 = 'POSTGRES_13';
  /**
   * The database version is Postgres 14.
   */
  public const DATABASE_VERSION_POSTGRES_14 = 'POSTGRES_14';
  /**
   * The database version is Postgres 15.
   */
  public const DATABASE_VERSION_POSTGRES_15 = 'POSTGRES_15';
  /**
   * The database version is Postgres 16.
   */
  public const DATABASE_VERSION_POSTGRES_16 = 'POSTGRES_16';
  /**
   * The database version is Postgres 17.
   */
  public const DATABASE_VERSION_POSTGRES_17 = 'POSTGRES_17';
  /**
   * The maintenance version selection policy is not specified.
   */
  public const MAINTENANCE_VERSION_SELECTION_POLICY_MAINTENANCE_VERSION_SELECTION_POLICY_UNSPECIFIED = 'MAINTENANCE_VERSION_SELECTION_POLICY_UNSPECIFIED';
  /**
   * Use the latest available maintenance version.
   */
  public const MAINTENANCE_VERSION_SELECTION_POLICY_MAINTENANCE_VERSION_SELECTION_POLICY_LATEST = 'MAINTENANCE_VERSION_SELECTION_POLICY_LATEST';
  /**
   * Use the current default maintenance version.
   */
  public const MAINTENANCE_VERSION_SELECTION_POLICY_MAINTENANCE_VERSION_SELECTION_POLICY_DEFAULT = 'MAINTENANCE_VERSION_SELECTION_POLICY_DEFAULT';
  /**
   * The state of the cluster is unknown.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The cluster is active and running.
   */
  public const STATE_READY = 'READY';
  /**
   * This is unused. Even when all instances in the cluster are stopped, the
   * cluster remains in READY state.
   */
  public const STATE_STOPPED = 'STOPPED';
  /**
   * The cluster is empty and has no associated resources. All instances,
   * associated storage and backups have been deleted.
   */
  public const STATE_EMPTY = 'EMPTY';
  /**
   * The cluster is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The cluster is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The creation of the cluster failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The cluster is bootstrapping with data from some other source. Direct
   * mutations to the cluster (e.g. adding read pool) are not allowed.
   */
  public const STATE_BOOTSTRAPPING = 'BOOTSTRAPPING';
  /**
   * The cluster is under maintenance. AlloyDB regularly performs maintenance
   * and upgrades on customer clusters. Updates on the cluster are not allowed
   * while the cluster is in this state.
   */
  public const STATE_MAINTENANCE = 'MAINTENANCE';
  /**
   * The cluster is being promoted.
   */
  public const STATE_PROMOTING = 'PROMOTING';
  /**
   * This is an unknown subscription type. By default, the subscription type is
   * STANDARD.
   */
  public const SUBSCRIPTION_TYPE_SUBSCRIPTION_TYPE_UNSPECIFIED = 'SUBSCRIPTION_TYPE_UNSPECIFIED';
  /**
   * Standard subscription.
   */
  public const SUBSCRIPTION_TYPE_STANDARD = 'STANDARD';
  /**
   * Trial subscription.
   */
  public const SUBSCRIPTION_TYPE_TRIAL = 'TRIAL';
  /**
   * Annotations to allow client tools to store small amount of arbitrary data.
   * This is distinct from labels. https://google.aip.dev/128
   *
   * @var string[]
   */
  public $annotations;
  protected $automatedBackupPolicyType = AutomatedBackupPolicy::class;
  protected $automatedBackupPolicyDataType = '';
  protected $backupSourceType = BackupSource::class;
  protected $backupSourceDataType = '';
  protected $backupdrBackupSourceType = BackupDrBackupSource::class;
  protected $backupdrBackupSourceDataType = '';
  protected $backupdrInfoType = BackupDrInfo::class;
  protected $backupdrInfoDataType = '';
  protected $cloudsqlBackupRunSourceType = CloudSQLBackupRunSource::class;
  protected $cloudsqlBackupRunSourceDataType = '';
  /**
   * Output only. The type of the cluster. This is an output-only field and it's
   * populated at the Cluster creation time or the Cluster promotion time. The
   * cluster type is determined by which RPC was used to create the cluster
   * (i.e. `CreateCluster` vs. `CreateSecondaryCluster`
   *
   * @var string
   */
  public $clusterType;
  protected $continuousBackupConfigType = ContinuousBackupConfig::class;
  protected $continuousBackupConfigDataType = '';
  protected $continuousBackupInfoType = ContinuousBackupInfo::class;
  protected $continuousBackupInfoDataType = '';
  /**
   * Output only. Create time stamp
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. The database engine major version. This is an optional field and
   * it is populated at the Cluster creation time. If a database version is not
   * supplied at cluster creation time, then a default database version will be
   * used.
   *
   * @var string
   */
  public $databaseVersion;
  protected $dataplexConfigType = DataplexConfig::class;
  protected $dataplexConfigDataType = '';
  /**
   * Output only. Delete time stamp
   *
   * @var string
   */
  public $deleteTime;
  /**
   * User-settable and human-readable display name for the Cluster.
   *
   * @var string
   */
  public $displayName;
  protected $encryptionConfigType = EncryptionConfig::class;
  protected $encryptionConfigDataType = '';
  protected $encryptionInfoType = EncryptionInfo::class;
  protected $encryptionInfoDataType = '';
  /**
   * For Resource freshness validation (https://google.aip.dev/154)
   *
   * @var string
   */
  public $etag;
  protected $initialUserType = UserPassword::class;
  protected $initialUserDataType = '';
  /**
   * Labels as key value pairs
   *
   * @var string[]
   */
  public $labels;
  protected $maintenanceScheduleType = MaintenanceSchedule::class;
  protected $maintenanceScheduleDataType = '';
  protected $maintenanceUpdatePolicyType = MaintenanceUpdatePolicy::class;
  protected $maintenanceUpdatePolicyDataType = '';
  /**
   * Input only. Policy to use to automatically select the maintenance version
   * to which to update the cluster's instances.
   *
   * @var string
   */
  public $maintenanceVersionSelectionPolicy;
  protected $migrationSourceType = MigrationSource::class;
  protected $migrationSourceDataType = '';
  /**
   * Output only. The name of the cluster resource with the format: *
   * projects/{project}/locations/{region}/clusters/{cluster_id} where the
   * cluster ID segment should satisfy the regex expression `[a-z0-9-]+`. For
   * more details see https://google.aip.dev/122. The prefix of the cluster
   * resource name is the name of the parent resource: *
   * projects/{project}/locations/{region}
   *
   * @var string
   */
  public $name;
  /**
   * Required. The resource link for the VPC network in which cluster resources
   * are created and from which they are accessible via Private IP. The network
   * must belong to the same project as the cluster. It is specified in the
   * form: `projects/{project}/global/networks/{network_id}`. This is required
   * to create a cluster. Deprecated, use network_config.network instead.
   *
   * @deprecated
   * @var string
   */
  public $network;
  protected $networkConfigType = NetworkConfig::class;
  protected $networkConfigDataType = '';
  protected $primaryConfigType = PrimaryConfig::class;
  protected $primaryConfigDataType = '';
  protected $pscConfigType = PscConfig::class;
  protected $pscConfigDataType = '';
  /**
   * Output only. Reconciling (https://google.aip.dev/128#reconciliation). Set
   * to true if the current state of Cluster does not match the user's intended
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
  protected $secondaryConfigType = SecondaryConfig::class;
  protected $secondaryConfigDataType = '';
  protected $sslConfigType = SslConfig::class;
  protected $sslConfigDataType = '';
  /**
   * Output only. The current serving state of the cluster.
   *
   * @var string
   */
  public $state;
  /**
   * Optional. Subscription type of the cluster.
   *
   * @var string
   */
  public $subscriptionType;
  /**
   * Optional. Input only. Immutable. Tag keys/values directly bound to this
   * resource. For example: ``` "123/environment": "production",
   * "123/costCenter": "marketing" ```
   *
   * @var string[]
   */
  public $tags;
  protected $trialMetadataType = TrialMetadata::class;
  protected $trialMetadataDataType = '';
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
   * The automated backup policy for this cluster. If no policy is provided then
   * the default policy will be used. If backups are supported for the cluster,
   * the default policy takes one backup a day, has a backup window of 1 hour,
   * and retains backups for 14 days. For more information on the defaults,
   * consult the documentation for the message type.
   *
   * @param AutomatedBackupPolicy $automatedBackupPolicy
   */
  public function setAutomatedBackupPolicy(AutomatedBackupPolicy $automatedBackupPolicy)
  {
    $this->automatedBackupPolicy = $automatedBackupPolicy;
  }
  /**
   * @return AutomatedBackupPolicy
   */
  public function getAutomatedBackupPolicy()
  {
    return $this->automatedBackupPolicy;
  }
  /**
   * Output only. Cluster created from backup.
   *
   * @param BackupSource $backupSource
   */
  public function setBackupSource(BackupSource $backupSource)
  {
    $this->backupSource = $backupSource;
  }
  /**
   * @return BackupSource
   */
  public function getBackupSource()
  {
    return $this->backupSource;
  }
  /**
   * Output only. Cluster created from a BackupDR backup.
   *
   * @param BackupDrBackupSource $backupdrBackupSource
   */
  public function setBackupdrBackupSource(BackupDrBackupSource $backupdrBackupSource)
  {
    $this->backupdrBackupSource = $backupdrBackupSource;
  }
  /**
   * @return BackupDrBackupSource
   */
  public function getBackupdrBackupSource()
  {
    return $this->backupdrBackupSource;
  }
  /**
   * Output only. Output only information about BackupDR protection for this
   * cluster.
   *
   * @param BackupDrInfo $backupdrInfo
   */
  public function setBackupdrInfo(BackupDrInfo $backupdrInfo)
  {
    $this->backupdrInfo = $backupdrInfo;
  }
  /**
   * @return BackupDrInfo
   */
  public function getBackupdrInfo()
  {
    return $this->backupdrInfo;
  }
  /**
   * Output only. Cluster created from CloudSQL snapshot.
   *
   * @param CloudSQLBackupRunSource $cloudsqlBackupRunSource
   */
  public function setCloudsqlBackupRunSource(CloudSQLBackupRunSource $cloudsqlBackupRunSource)
  {
    $this->cloudsqlBackupRunSource = $cloudsqlBackupRunSource;
  }
  /**
   * @return CloudSQLBackupRunSource
   */
  public function getCloudsqlBackupRunSource()
  {
    return $this->cloudsqlBackupRunSource;
  }
  /**
   * Output only. The type of the cluster. This is an output-only field and it's
   * populated at the Cluster creation time or the Cluster promotion time. The
   * cluster type is determined by which RPC was used to create the cluster
   * (i.e. `CreateCluster` vs. `CreateSecondaryCluster`
   *
   * Accepted values: CLUSTER_TYPE_UNSPECIFIED, PRIMARY, SECONDARY
   *
   * @param self::CLUSTER_TYPE_* $clusterType
   */
  public function setClusterType($clusterType)
  {
    $this->clusterType = $clusterType;
  }
  /**
   * @return self::CLUSTER_TYPE_*
   */
  public function getClusterType()
  {
    return $this->clusterType;
  }
  /**
   * Optional. Continuous backup configuration for this cluster.
   *
   * @param ContinuousBackupConfig $continuousBackupConfig
   */
  public function setContinuousBackupConfig(ContinuousBackupConfig $continuousBackupConfig)
  {
    $this->continuousBackupConfig = $continuousBackupConfig;
  }
  /**
   * @return ContinuousBackupConfig
   */
  public function getContinuousBackupConfig()
  {
    return $this->continuousBackupConfig;
  }
  /**
   * Output only. Continuous backup properties for this cluster.
   *
   * @param ContinuousBackupInfo $continuousBackupInfo
   */
  public function setContinuousBackupInfo(ContinuousBackupInfo $continuousBackupInfo)
  {
    $this->continuousBackupInfo = $continuousBackupInfo;
  }
  /**
   * @return ContinuousBackupInfo
   */
  public function getContinuousBackupInfo()
  {
    return $this->continuousBackupInfo;
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
   * Optional. The database engine major version. This is an optional field and
   * it is populated at the Cluster creation time. If a database version is not
   * supplied at cluster creation time, then a default database version will be
   * used.
   *
   * Accepted values: DATABASE_VERSION_UNSPECIFIED, POSTGRES_13, POSTGRES_14,
   * POSTGRES_15, POSTGRES_16, POSTGRES_17
   *
   * @param self::DATABASE_VERSION_* $databaseVersion
   */
  public function setDatabaseVersion($databaseVersion)
  {
    $this->databaseVersion = $databaseVersion;
  }
  /**
   * @return self::DATABASE_VERSION_*
   */
  public function getDatabaseVersion()
  {
    return $this->databaseVersion;
  }
  /**
   * Optional. Configuration for Dataplex integration.
   *
   * @param DataplexConfig $dataplexConfig
   */
  public function setDataplexConfig(DataplexConfig $dataplexConfig)
  {
    $this->dataplexConfig = $dataplexConfig;
  }
  /**
   * @return DataplexConfig
   */
  public function getDataplexConfig()
  {
    return $this->dataplexConfig;
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
   * User-settable and human-readable display name for the Cluster.
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
   * Optional. The encryption config can be specified to encrypt the data disks
   * and other persistent data resources of a cluster with a customer-managed
   * encryption key (CMEK). When this field is not specified, the cluster will
   * then use default encryption scheme to protect the user data.
   *
   * @param EncryptionConfig $encryptionConfig
   */
  public function setEncryptionConfig(EncryptionConfig $encryptionConfig)
  {
    $this->encryptionConfig = $encryptionConfig;
  }
  /**
   * @return EncryptionConfig
   */
  public function getEncryptionConfig()
  {
    return $this->encryptionConfig;
  }
  /**
   * Output only. The encryption information for the cluster.
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
   * Input only. Initial user to setup during cluster creation. Required. If
   * used in `RestoreCluster` this is ignored.
   *
   * @param UserPassword $initialUser
   */
  public function setInitialUser(UserPassword $initialUser)
  {
    $this->initialUser = $initialUser;
  }
  /**
   * @return UserPassword
   */
  public function getInitialUser()
  {
    return $this->initialUser;
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
   * Output only. The maintenance schedule for the cluster, generated for a
   * specific rollout if a maintenance window is set.
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
   * Optional. The maintenance update policy determines when to allow or deny
   * updates.
   *
   * @param MaintenanceUpdatePolicy $maintenanceUpdatePolicy
   */
  public function setMaintenanceUpdatePolicy(MaintenanceUpdatePolicy $maintenanceUpdatePolicy)
  {
    $this->maintenanceUpdatePolicy = $maintenanceUpdatePolicy;
  }
  /**
   * @return MaintenanceUpdatePolicy
   */
  public function getMaintenanceUpdatePolicy()
  {
    return $this->maintenanceUpdatePolicy;
  }
  /**
   * Input only. Policy to use to automatically select the maintenance version
   * to which to update the cluster's instances.
   *
   * Accepted values: MAINTENANCE_VERSION_SELECTION_POLICY_UNSPECIFIED,
   * MAINTENANCE_VERSION_SELECTION_POLICY_LATEST,
   * MAINTENANCE_VERSION_SELECTION_POLICY_DEFAULT
   *
   * @param self::MAINTENANCE_VERSION_SELECTION_POLICY_* $maintenanceVersionSelectionPolicy
   */
  public function setMaintenanceVersionSelectionPolicy($maintenanceVersionSelectionPolicy)
  {
    $this->maintenanceVersionSelectionPolicy = $maintenanceVersionSelectionPolicy;
  }
  /**
   * @return self::MAINTENANCE_VERSION_SELECTION_POLICY_*
   */
  public function getMaintenanceVersionSelectionPolicy()
  {
    return $this->maintenanceVersionSelectionPolicy;
  }
  /**
   * Output only. Cluster created via DMS migration.
   *
   * @param MigrationSource $migrationSource
   */
  public function setMigrationSource(MigrationSource $migrationSource)
  {
    $this->migrationSource = $migrationSource;
  }
  /**
   * @return MigrationSource
   */
  public function getMigrationSource()
  {
    return $this->migrationSource;
  }
  /**
   * Output only. The name of the cluster resource with the format: *
   * projects/{project}/locations/{region}/clusters/{cluster_id} where the
   * cluster ID segment should satisfy the regex expression `[a-z0-9-]+`. For
   * more details see https://google.aip.dev/122. The prefix of the cluster
   * resource name is the name of the parent resource: *
   * projects/{project}/locations/{region}
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
   * Required. The resource link for the VPC network in which cluster resources
   * are created and from which they are accessible via Private IP. The network
   * must belong to the same project as the cluster. It is specified in the
   * form: `projects/{project}/global/networks/{network_id}`. This is required
   * to create a cluster. Deprecated, use network_config.network instead.
   *
   * @deprecated
   * @param string $network
   */
  public function setNetwork($network)
  {
    $this->network = $network;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getNetwork()
  {
    return $this->network;
  }
  /**
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
   * Output only. Cross Region replication config specific to PRIMARY cluster.
   *
   * @param PrimaryConfig $primaryConfig
   */
  public function setPrimaryConfig(PrimaryConfig $primaryConfig)
  {
    $this->primaryConfig = $primaryConfig;
  }
  /**
   * @return PrimaryConfig
   */
  public function getPrimaryConfig()
  {
    return $this->primaryConfig;
  }
  /**
   * Optional. The configuration for Private Service Connect (PSC) for the
   * cluster.
   *
   * @param PscConfig $pscConfig
   */
  public function setPscConfig(PscConfig $pscConfig)
  {
    $this->pscConfig = $pscConfig;
  }
  /**
   * @return PscConfig
   */
  public function getPscConfig()
  {
    return $this->pscConfig;
  }
  /**
   * Output only. Reconciling (https://google.aip.dev/128#reconciliation). Set
   * to true if the current state of Cluster does not match the user's intended
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
   * Cross Region replication config specific to SECONDARY cluster.
   *
   * @param SecondaryConfig $secondaryConfig
   */
  public function setSecondaryConfig(SecondaryConfig $secondaryConfig)
  {
    $this->secondaryConfig = $secondaryConfig;
  }
  /**
   * @return SecondaryConfig
   */
  public function getSecondaryConfig()
  {
    return $this->secondaryConfig;
  }
  /**
   * SSL configuration for this AlloyDB cluster.
   *
   * @deprecated
   * @param SslConfig $sslConfig
   */
  public function setSslConfig(SslConfig $sslConfig)
  {
    $this->sslConfig = $sslConfig;
  }
  /**
   * @deprecated
   * @return SslConfig
   */
  public function getSslConfig()
  {
    return $this->sslConfig;
  }
  /**
   * Output only. The current serving state of the cluster.
   *
   * Accepted values: STATE_UNSPECIFIED, READY, STOPPED, EMPTY, CREATING,
   * DELETING, FAILED, BOOTSTRAPPING, MAINTENANCE, PROMOTING
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
   * Optional. Subscription type of the cluster.
   *
   * Accepted values: SUBSCRIPTION_TYPE_UNSPECIFIED, STANDARD, TRIAL
   *
   * @param self::SUBSCRIPTION_TYPE_* $subscriptionType
   */
  public function setSubscriptionType($subscriptionType)
  {
    $this->subscriptionType = $subscriptionType;
  }
  /**
   * @return self::SUBSCRIPTION_TYPE_*
   */
  public function getSubscriptionType()
  {
    return $this->subscriptionType;
  }
  /**
   * Optional. Input only. Immutable. Tag keys/values directly bound to this
   * resource. For example: ``` "123/environment": "production",
   * "123/costCenter": "marketing" ```
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
   * Output only. Metadata for free trial clusters
   *
   * @param TrialMetadata $trialMetadata
   */
  public function setTrialMetadata(TrialMetadata $trialMetadata)
  {
    $this->trialMetadata = $trialMetadata;
  }
  /**
   * @return TrialMetadata
   */
  public function getTrialMetadata()
  {
    return $this->trialMetadata;
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Cluster::class, 'Google_Service_CloudAlloyDBAdmin_Cluster');
