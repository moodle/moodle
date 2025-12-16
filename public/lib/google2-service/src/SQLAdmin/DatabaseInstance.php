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

namespace Google\Service\SQLAdmin;

class DatabaseInstance extends \Google\Collection
{
  /**
   * This is an unknown backend type for instance.
   */
  public const BACKEND_TYPE_SQL_BACKEND_TYPE_UNSPECIFIED = 'SQL_BACKEND_TYPE_UNSPECIFIED';
  /**
   * V1 speckle instance.
   *
   * @deprecated
   */
  public const BACKEND_TYPE_FIRST_GEN = 'FIRST_GEN';
  /**
   * V2 speckle instance.
   */
  public const BACKEND_TYPE_SECOND_GEN = 'SECOND_GEN';
  /**
   * On premises instance.
   */
  public const BACKEND_TYPE_EXTERNAL = 'EXTERNAL';
  /**
   * This is an unknown database version.
   */
  public const DATABASE_VERSION_SQL_DATABASE_VERSION_UNSPECIFIED = 'SQL_DATABASE_VERSION_UNSPECIFIED';
  /**
   * The database version is MySQL 5.1.
   *
   * @deprecated
   */
  public const DATABASE_VERSION_MYSQL_5_1 = 'MYSQL_5_1';
  /**
   * The database version is MySQL 5.5.
   *
   * @deprecated
   */
  public const DATABASE_VERSION_MYSQL_5_5 = 'MYSQL_5_5';
  /**
   * The database version is MySQL 5.6.
   */
  public const DATABASE_VERSION_MYSQL_5_6 = 'MYSQL_5_6';
  /**
   * The database version is MySQL 5.7.
   */
  public const DATABASE_VERSION_MYSQL_5_7 = 'MYSQL_5_7';
  /**
   * The database version is MySQL 8.
   */
  public const DATABASE_VERSION_MYSQL_8_0 = 'MYSQL_8_0';
  /**
   * The database major version is MySQL 8.0 and the minor version is 18.
   */
  public const DATABASE_VERSION_MYSQL_8_0_18 = 'MYSQL_8_0_18';
  /**
   * The database major version is MySQL 8.0 and the minor version is 26.
   */
  public const DATABASE_VERSION_MYSQL_8_0_26 = 'MYSQL_8_0_26';
  /**
   * The database major version is MySQL 8.0 and the minor version is 27.
   */
  public const DATABASE_VERSION_MYSQL_8_0_27 = 'MYSQL_8_0_27';
  /**
   * The database major version is MySQL 8.0 and the minor version is 28.
   */
  public const DATABASE_VERSION_MYSQL_8_0_28 = 'MYSQL_8_0_28';
  /**
   * The database major version is MySQL 8.0 and the minor version is 29.
   *
   * @deprecated
   */
  public const DATABASE_VERSION_MYSQL_8_0_29 = 'MYSQL_8_0_29';
  /**
   * The database major version is MySQL 8.0 and the minor version is 30.
   */
  public const DATABASE_VERSION_MYSQL_8_0_30 = 'MYSQL_8_0_30';
  /**
   * The database major version is MySQL 8.0 and the minor version is 31.
   */
  public const DATABASE_VERSION_MYSQL_8_0_31 = 'MYSQL_8_0_31';
  /**
   * The database major version is MySQL 8.0 and the minor version is 32.
   */
  public const DATABASE_VERSION_MYSQL_8_0_32 = 'MYSQL_8_0_32';
  /**
   * The database major version is MySQL 8.0 and the minor version is 33.
   */
  public const DATABASE_VERSION_MYSQL_8_0_33 = 'MYSQL_8_0_33';
  /**
   * The database major version is MySQL 8.0 and the minor version is 34.
   */
  public const DATABASE_VERSION_MYSQL_8_0_34 = 'MYSQL_8_0_34';
  /**
   * The database major version is MySQL 8.0 and the minor version is 35.
   */
  public const DATABASE_VERSION_MYSQL_8_0_35 = 'MYSQL_8_0_35';
  /**
   * The database major version is MySQL 8.0 and the minor version is 36.
   */
  public const DATABASE_VERSION_MYSQL_8_0_36 = 'MYSQL_8_0_36';
  /**
   * The database major version is MySQL 8.0 and the minor version is 37.
   */
  public const DATABASE_VERSION_MYSQL_8_0_37 = 'MYSQL_8_0_37';
  /**
   * The database major version is MySQL 8.0 and the minor version is 39.
   */
  public const DATABASE_VERSION_MYSQL_8_0_39 = 'MYSQL_8_0_39';
  /**
   * The database major version is MySQL 8.0 and the minor version is 40.
   */
  public const DATABASE_VERSION_MYSQL_8_0_40 = 'MYSQL_8_0_40';
  /**
   * The database major version is MySQL 8.0 and the minor version is 41.
   */
  public const DATABASE_VERSION_MYSQL_8_0_41 = 'MYSQL_8_0_41';
  /**
   * The database major version is MySQL 8.0 and the minor version is 42.
   */
  public const DATABASE_VERSION_MYSQL_8_0_42 = 'MYSQL_8_0_42';
  /**
   * The database major version is MySQL 8.0 and the minor version is 43.
   */
  public const DATABASE_VERSION_MYSQL_8_0_43 = 'MYSQL_8_0_43';
  /**
   * The database major version is MySQL 8.0 and the minor version is 44.
   */
  public const DATABASE_VERSION_MYSQL_8_0_44 = 'MYSQL_8_0_44';
  /**
   * The database major version is MySQL 8.0 and the minor version is 45.
   */
  public const DATABASE_VERSION_MYSQL_8_0_45 = 'MYSQL_8_0_45';
  /**
   * The database major version is MySQL 8.0 and the minor version is 46.
   */
  public const DATABASE_VERSION_MYSQL_8_0_46 = 'MYSQL_8_0_46';
  /**
   * The database version is MySQL 8.4.
   */
  public const DATABASE_VERSION_MYSQL_8_4 = 'MYSQL_8_4';
  /**
   * The database version is SQL Server 2017 Standard.
   */
  public const DATABASE_VERSION_SQLSERVER_2017_STANDARD = 'SQLSERVER_2017_STANDARD';
  /**
   * The database version is SQL Server 2017 Enterprise.
   */
  public const DATABASE_VERSION_SQLSERVER_2017_ENTERPRISE = 'SQLSERVER_2017_ENTERPRISE';
  /**
   * The database version is SQL Server 2017 Express.
   */
  public const DATABASE_VERSION_SQLSERVER_2017_EXPRESS = 'SQLSERVER_2017_EXPRESS';
  /**
   * The database version is SQL Server 2017 Web.
   */
  public const DATABASE_VERSION_SQLSERVER_2017_WEB = 'SQLSERVER_2017_WEB';
  /**
   * The database version is PostgreSQL 9.6.
   */
  public const DATABASE_VERSION_POSTGRES_9_6 = 'POSTGRES_9_6';
  /**
   * The database version is PostgreSQL 10.
   */
  public const DATABASE_VERSION_POSTGRES_10 = 'POSTGRES_10';
  /**
   * The database version is PostgreSQL 11.
   */
  public const DATABASE_VERSION_POSTGRES_11 = 'POSTGRES_11';
  /**
   * The database version is PostgreSQL 12.
   */
  public const DATABASE_VERSION_POSTGRES_12 = 'POSTGRES_12';
  /**
   * The database version is PostgreSQL 13.
   */
  public const DATABASE_VERSION_POSTGRES_13 = 'POSTGRES_13';
  /**
   * The database version is PostgreSQL 14.
   */
  public const DATABASE_VERSION_POSTGRES_14 = 'POSTGRES_14';
  /**
   * The database version is PostgreSQL 15.
   */
  public const DATABASE_VERSION_POSTGRES_15 = 'POSTGRES_15';
  /**
   * The database version is PostgreSQL 16.
   */
  public const DATABASE_VERSION_POSTGRES_16 = 'POSTGRES_16';
  /**
   * The database version is PostgreSQL 17.
   */
  public const DATABASE_VERSION_POSTGRES_17 = 'POSTGRES_17';
  /**
   * The database version is PostgreSQL 18.
   */
  public const DATABASE_VERSION_POSTGRES_18 = 'POSTGRES_18';
  /**
   * The database version is SQL Server 2019 Standard.
   */
  public const DATABASE_VERSION_SQLSERVER_2019_STANDARD = 'SQLSERVER_2019_STANDARD';
  /**
   * The database version is SQL Server 2019 Enterprise.
   */
  public const DATABASE_VERSION_SQLSERVER_2019_ENTERPRISE = 'SQLSERVER_2019_ENTERPRISE';
  /**
   * The database version is SQL Server 2019 Express.
   */
  public const DATABASE_VERSION_SQLSERVER_2019_EXPRESS = 'SQLSERVER_2019_EXPRESS';
  /**
   * The database version is SQL Server 2019 Web.
   */
  public const DATABASE_VERSION_SQLSERVER_2019_WEB = 'SQLSERVER_2019_WEB';
  /**
   * The database version is SQL Server 2022 Standard.
   */
  public const DATABASE_VERSION_SQLSERVER_2022_STANDARD = 'SQLSERVER_2022_STANDARD';
  /**
   * The database version is SQL Server 2022 Enterprise.
   */
  public const DATABASE_VERSION_SQLSERVER_2022_ENTERPRISE = 'SQLSERVER_2022_ENTERPRISE';
  /**
   * The database version is SQL Server 2022 Express.
   */
  public const DATABASE_VERSION_SQLSERVER_2022_EXPRESS = 'SQLSERVER_2022_EXPRESS';
  /**
   * The database version is SQL Server 2022 Web.
   */
  public const DATABASE_VERSION_SQLSERVER_2022_WEB = 'SQLSERVER_2022_WEB';
  /**
   * This is an unknown Cloud SQL instance type.
   */
  public const INSTANCE_TYPE_SQL_INSTANCE_TYPE_UNSPECIFIED = 'SQL_INSTANCE_TYPE_UNSPECIFIED';
  /**
   * A regular Cloud SQL instance that is not replicating from a primary
   * instance.
   */
  public const INSTANCE_TYPE_CLOUD_SQL_INSTANCE = 'CLOUD_SQL_INSTANCE';
  /**
   * An instance running on the customer's premises that is not managed by Cloud
   * SQL.
   */
  public const INSTANCE_TYPE_ON_PREMISES_INSTANCE = 'ON_PREMISES_INSTANCE';
  /**
   * A Cloud SQL instance acting as a read-replica.
   */
  public const INSTANCE_TYPE_READ_REPLICA_INSTANCE = 'READ_REPLICA_INSTANCE';
  /**
   * A Cloud SQL read pool.
   */
  public const INSTANCE_TYPE_READ_POOL_INSTANCE = 'READ_POOL_INSTANCE';
  public const SQL_NETWORK_ARCHITECTURE_SQL_NETWORK_ARCHITECTURE_UNSPECIFIED = 'SQL_NETWORK_ARCHITECTURE_UNSPECIFIED';
  /**
   * The instance uses the new network architecture.
   */
  public const SQL_NETWORK_ARCHITECTURE_NEW_NETWORK_ARCHITECTURE = 'NEW_NETWORK_ARCHITECTURE';
  /**
   * The instance uses the old network architecture.
   */
  public const SQL_NETWORK_ARCHITECTURE_OLD_NETWORK_ARCHITECTURE = 'OLD_NETWORK_ARCHITECTURE';
  /**
   * The state of the instance is unknown.
   */
  public const STATE_SQL_INSTANCE_STATE_UNSPECIFIED = 'SQL_INSTANCE_STATE_UNSPECIFIED';
  /**
   * The instance is running, or has been stopped by owner.
   */
  public const STATE_RUNNABLE = 'RUNNABLE';
  /**
   * The instance is not available, for example due to problems with billing.
   */
  public const STATE_SUSPENDED = 'SUSPENDED';
  /**
   * The instance is being deleted.
   */
  public const STATE_PENDING_DELETE = 'PENDING_DELETE';
  /**
   * The instance is being created.
   */
  public const STATE_PENDING_CREATE = 'PENDING_CREATE';
  /**
   * The instance is down for maintenance.
   */
  public const STATE_MAINTENANCE = 'MAINTENANCE';
  /**
   * The creation of the instance failed or a fatal error occurred during
   * maintenance.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Deprecated
   *
   * @deprecated
   */
  public const STATE_ONLINE_MAINTENANCE = 'ONLINE_MAINTENANCE';
  /**
   * (Applicable to read pool nodes only.) The read pool node needs to be
   * repaired. The database might be unavailable.
   */
  public const STATE_REPAIRING = 'REPAIRING';
  protected $collection_key = 'upgradableDatabaseVersions';
  /**
   * Output only. List all maintenance versions applicable on the instance
   *
   * @var string[]
   */
  public $availableMaintenanceVersions;
  /**
   * The backend type. `SECOND_GEN`: Cloud SQL database instance. `EXTERNAL`: A
   * database server that is not managed by Google. This property is read-only;
   * use the `tier` property in the `settings` object to determine the database
   * type.
   *
   * @var string
   */
  public $backendType;
  /**
   * Connection name of the Cloud SQL instance used in connection strings.
   *
   * @var string
   */
  public $connectionName;
  /**
   * Output only. The time when the instance was created in [RFC
   * 3339](https://tools.ietf.org/html/rfc3339) format, for example
   * `2012-11-15T16:19:00.094Z`.
   *
   * @var string
   */
  public $createTime;
  /**
   * The current disk usage of the instance in bytes. This property has been
   * deprecated. Use the "cloudsql.googleapis.com/database/disk/bytes_used"
   * metric in Cloud Monitoring API instead. Please see [this
   * announcement](https://groups.google.com/d/msg/google-cloud-sql-
   * announce/I_7-F9EBhT0/BtvFtdFeAgAJ) for details.
   *
   * @deprecated
   * @var string
   */
  public $currentDiskSize;
  /**
   * Output only. Stores the current database version running on the instance
   * including minor version such as `MYSQL_8_0_18`.
   *
   * @var string
   */
  public $databaseInstalledVersion;
  /**
   * The database engine type and version. The `databaseVersion` field cannot be
   * changed after instance creation.
   *
   * @var string
   */
  public $databaseVersion;
  protected $diskEncryptionConfigurationType = DiskEncryptionConfiguration::class;
  protected $diskEncryptionConfigurationDataType = '';
  protected $diskEncryptionStatusType = DiskEncryptionStatus::class;
  protected $diskEncryptionStatusDataType = '';
  /**
   * Output only. The dns name of the instance.
   *
   * @var string
   */
  public $dnsName;
  protected $dnsNamesType = DnsNameMapping::class;
  protected $dnsNamesDataType = 'array';
  /**
   * This field is deprecated and will be removed from a future version of the
   * API. Use the `settings.settingsVersion` field instead.
   *
   * @var string
   */
  public $etag;
  protected $failoverReplicaType = DatabaseInstanceFailoverReplica::class;
  protected $failoverReplicaDataType = '';
  /**
   * The Compute Engine zone that the instance is currently serving from. This
   * value could be different from the zone that was specified when the instance
   * was created if the instance has failed over to its secondary zone. WARNING:
   * Changing this might restart the instance.
   *
   * @var string
   */
  public $gceZone;
  protected $geminiConfigType = GeminiInstanceConfig::class;
  protected $geminiConfigDataType = '';
  /**
   * Input only. Determines whether an in-place major version upgrade of
   * replicas happens when an in-place major version upgrade of a primary
   * instance is initiated.
   *
   * @var bool
   */
  public $includeReplicasForMajorVersionUpgrade;
  /**
   * The instance type.
   *
   * @var string
   */
  public $instanceType;
  protected $ipAddressesType = IpMapping::class;
  protected $ipAddressesDataType = 'array';
  /**
   * The IPv6 address assigned to the instance. (Deprecated) This property was
   * applicable only to First Generation instances.
   *
   * @deprecated
   * @var string
   */
  public $ipv6Address;
  /**
   * This is always `sql#instance`.
   *
   * @var string
   */
  public $kind;
  /**
   * The current software version on the instance.
   *
   * @var string
   */
  public $maintenanceVersion;
  /**
   * The name of the instance which will act as primary in the replication
   * setup.
   *
   * @var string
   */
  public $masterInstanceName;
  /**
   * The maximum disk size of the instance in bytes.
   *
   * @deprecated
   * @var string
   */
  public $maxDiskSize;
  /**
   * Name of the Cloud SQL instance. This does not include the project ID.
   *
   * @var string
   */
  public $name;
  /**
   * The number of read pool nodes in a read pool.
   *
   * @var int
   */
  public $nodeCount;
  protected $nodesType = PoolNodeConfig::class;
  protected $nodesDataType = 'array';
  protected $onPremisesConfigurationType = OnPremisesConfiguration::class;
  protected $onPremisesConfigurationDataType = '';
  protected $outOfDiskReportType = SqlOutOfDiskReport::class;
  protected $outOfDiskReportDataType = '';
  /**
   * Output only. DEPRECATED: please use write_endpoint instead.
   *
   * @deprecated
   * @var string
   */
  public $primaryDnsName;
  /**
   * The project ID of the project containing the Cloud SQL instance. The Google
   * apps domain is prefixed if applicable.
   *
   * @var string
   */
  public $project;
  /**
   * Output only. The link to service attachment of PSC instance.
   *
   * @var string
   */
  public $pscServiceAttachmentLink;
  /**
   * The geographical region of the Cloud SQL instance. It can be one of the
   * [regions](https://cloud.google.com/sql/docs/mysql/locations#location-r)
   * where Cloud SQL operates: For example, `asia-east1`, `europe-west1`, and
   * `us-central1`. The default value is `us-central1`.
   *
   * @var string
   */
  public $region;
  protected $replicaConfigurationType = ReplicaConfiguration::class;
  protected $replicaConfigurationDataType = '';
  /**
   * The replicas of the instance.
   *
   * @var string[]
   */
  public $replicaNames;
  protected $replicationClusterType = ReplicationCluster::class;
  protected $replicationClusterDataType = '';
  /**
   * Initial root password. Use only on creation. You must set root passwords
   * before you can connect to PostgreSQL instances.
   *
   * @var string
   */
  public $rootPassword;
  /**
   * Output only. This status indicates whether the instance satisfies PZI. The
   * status is reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzi;
  /**
   * This status indicates whether the instance satisfies PZS. The status is
   * reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzs;
  protected $scheduledMaintenanceType = SqlScheduledMaintenance::class;
  protected $scheduledMaintenanceDataType = '';
  /**
   * The Compute Engine zone that the failover instance is currently serving
   * from for a regional instance. This value could be different from the zone
   * that was specified when the instance was created if the instance has failed
   * over to its secondary/failover zone.
   *
   * @var string
   */
  public $secondaryGceZone;
  /**
   * The URI of this resource.
   *
   * @var string
   */
  public $selfLink;
  protected $serverCaCertType = SslCert::class;
  protected $serverCaCertDataType = '';
  /**
   * The service account email address assigned to the instance.\This property
   * is read-only.
   *
   * @var string
   */
  public $serviceAccountEmailAddress;
  protected $settingsType = Settings::class;
  protected $settingsDataType = '';
  /**
   * @var string
   */
  public $sqlNetworkArchitecture;
  /**
   * The current serving state of the Cloud SQL instance.
   *
   * @var string
   */
  public $state;
  /**
   * If the instance state is SUSPENDED, the reason for the suspension.
   *
   * @var string[]
   */
  public $suspensionReason;
  /**
   * Input only. Whether Cloud SQL is enabled to switch storing point-in-time
   * recovery log files from a data disk to Cloud Storage.
   *
   * @var bool
   */
  public $switchTransactionLogsToCloudStorageEnabled;
  /**
   * Optional. Input only. Immutable. Tag keys and tag values that are bound to
   * this instance. You must represent each item in the map as: `"" : ""`. For
   * example, a single resource can have the following tags: ```
   * "123/environment": "production", "123/costCenter": "marketing", ``` For
   * more information on tag creation and management, see
   * https://cloud.google.com/resource-manager/docs/tags/tags-overview.
   *
   * @var string[]
   */
  public $tags;
  protected $upgradableDatabaseVersionsType = AvailableDatabaseVersion::class;
  protected $upgradableDatabaseVersionsDataType = 'array';
  /**
   * Output only. The dns name of the primary instance in a replication group.
   *
   * @var string
   */
  public $writeEndpoint;

  /**
   * Output only. List all maintenance versions applicable on the instance
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
   * The backend type. `SECOND_GEN`: Cloud SQL database instance. `EXTERNAL`: A
   * database server that is not managed by Google. This property is read-only;
   * use the `tier` property in the `settings` object to determine the database
   * type.
   *
   * Accepted values: SQL_BACKEND_TYPE_UNSPECIFIED, FIRST_GEN, SECOND_GEN,
   * EXTERNAL
   *
   * @param self::BACKEND_TYPE_* $backendType
   */
  public function setBackendType($backendType)
  {
    $this->backendType = $backendType;
  }
  /**
   * @return self::BACKEND_TYPE_*
   */
  public function getBackendType()
  {
    return $this->backendType;
  }
  /**
   * Connection name of the Cloud SQL instance used in connection strings.
   *
   * @param string $connectionName
   */
  public function setConnectionName($connectionName)
  {
    $this->connectionName = $connectionName;
  }
  /**
   * @return string
   */
  public function getConnectionName()
  {
    return $this->connectionName;
  }
  /**
   * Output only. The time when the instance was created in [RFC
   * 3339](https://tools.ietf.org/html/rfc3339) format, for example
   * `2012-11-15T16:19:00.094Z`.
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
   * The current disk usage of the instance in bytes. This property has been
   * deprecated. Use the "cloudsql.googleapis.com/database/disk/bytes_used"
   * metric in Cloud Monitoring API instead. Please see [this
   * announcement](https://groups.google.com/d/msg/google-cloud-sql-
   * announce/I_7-F9EBhT0/BtvFtdFeAgAJ) for details.
   *
   * @deprecated
   * @param string $currentDiskSize
   */
  public function setCurrentDiskSize($currentDiskSize)
  {
    $this->currentDiskSize = $currentDiskSize;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getCurrentDiskSize()
  {
    return $this->currentDiskSize;
  }
  /**
   * Output only. Stores the current database version running on the instance
   * including minor version such as `MYSQL_8_0_18`.
   *
   * @param string $databaseInstalledVersion
   */
  public function setDatabaseInstalledVersion($databaseInstalledVersion)
  {
    $this->databaseInstalledVersion = $databaseInstalledVersion;
  }
  /**
   * @return string
   */
  public function getDatabaseInstalledVersion()
  {
    return $this->databaseInstalledVersion;
  }
  /**
   * The database engine type and version. The `databaseVersion` field cannot be
   * changed after instance creation.
   *
   * Accepted values: SQL_DATABASE_VERSION_UNSPECIFIED, MYSQL_5_1, MYSQL_5_5,
   * MYSQL_5_6, MYSQL_5_7, MYSQL_8_0, MYSQL_8_0_18, MYSQL_8_0_26, MYSQL_8_0_27,
   * MYSQL_8_0_28, MYSQL_8_0_29, MYSQL_8_0_30, MYSQL_8_0_31, MYSQL_8_0_32,
   * MYSQL_8_0_33, MYSQL_8_0_34, MYSQL_8_0_35, MYSQL_8_0_36, MYSQL_8_0_37,
   * MYSQL_8_0_39, MYSQL_8_0_40, MYSQL_8_0_41, MYSQL_8_0_42, MYSQL_8_0_43,
   * MYSQL_8_0_44, MYSQL_8_0_45, MYSQL_8_0_46, MYSQL_8_4,
   * SQLSERVER_2017_STANDARD, SQLSERVER_2017_ENTERPRISE, SQLSERVER_2017_EXPRESS,
   * SQLSERVER_2017_WEB, POSTGRES_9_6, POSTGRES_10, POSTGRES_11, POSTGRES_12,
   * POSTGRES_13, POSTGRES_14, POSTGRES_15, POSTGRES_16, POSTGRES_17,
   * POSTGRES_18, SQLSERVER_2019_STANDARD, SQLSERVER_2019_ENTERPRISE,
   * SQLSERVER_2019_EXPRESS, SQLSERVER_2019_WEB, SQLSERVER_2022_STANDARD,
   * SQLSERVER_2022_ENTERPRISE, SQLSERVER_2022_EXPRESS, SQLSERVER_2022_WEB
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
   * Disk encryption configuration specific to an instance.
   *
   * @param DiskEncryptionConfiguration $diskEncryptionConfiguration
   */
  public function setDiskEncryptionConfiguration(DiskEncryptionConfiguration $diskEncryptionConfiguration)
  {
    $this->diskEncryptionConfiguration = $diskEncryptionConfiguration;
  }
  /**
   * @return DiskEncryptionConfiguration
   */
  public function getDiskEncryptionConfiguration()
  {
    return $this->diskEncryptionConfiguration;
  }
  /**
   * Disk encryption status specific to an instance.
   *
   * @param DiskEncryptionStatus $diskEncryptionStatus
   */
  public function setDiskEncryptionStatus(DiskEncryptionStatus $diskEncryptionStatus)
  {
    $this->diskEncryptionStatus = $diskEncryptionStatus;
  }
  /**
   * @return DiskEncryptionStatus
   */
  public function getDiskEncryptionStatus()
  {
    return $this->diskEncryptionStatus;
  }
  /**
   * Output only. The dns name of the instance.
   *
   * @param string $dnsName
   */
  public function setDnsName($dnsName)
  {
    $this->dnsName = $dnsName;
  }
  /**
   * @return string
   */
  public function getDnsName()
  {
    return $this->dnsName;
  }
  /**
   * Output only. The list of DNS names used by this instance.
   *
   * @param DnsNameMapping[] $dnsNames
   */
  public function setDnsNames($dnsNames)
  {
    $this->dnsNames = $dnsNames;
  }
  /**
   * @return DnsNameMapping[]
   */
  public function getDnsNames()
  {
    return $this->dnsNames;
  }
  /**
   * This field is deprecated and will be removed from a future version of the
   * API. Use the `settings.settingsVersion` field instead.
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
   * The name and status of the failover replica.
   *
   * @param DatabaseInstanceFailoverReplica $failoverReplica
   */
  public function setFailoverReplica(DatabaseInstanceFailoverReplica $failoverReplica)
  {
    $this->failoverReplica = $failoverReplica;
  }
  /**
   * @return DatabaseInstanceFailoverReplica
   */
  public function getFailoverReplica()
  {
    return $this->failoverReplica;
  }
  /**
   * The Compute Engine zone that the instance is currently serving from. This
   * value could be different from the zone that was specified when the instance
   * was created if the instance has failed over to its secondary zone. WARNING:
   * Changing this might restart the instance.
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
   * Gemini instance configuration.
   *
   * @param GeminiInstanceConfig $geminiConfig
   */
  public function setGeminiConfig(GeminiInstanceConfig $geminiConfig)
  {
    $this->geminiConfig = $geminiConfig;
  }
  /**
   * @return GeminiInstanceConfig
   */
  public function getGeminiConfig()
  {
    return $this->geminiConfig;
  }
  /**
   * Input only. Determines whether an in-place major version upgrade of
   * replicas happens when an in-place major version upgrade of a primary
   * instance is initiated.
   *
   * @param bool $includeReplicasForMajorVersionUpgrade
   */
  public function setIncludeReplicasForMajorVersionUpgrade($includeReplicasForMajorVersionUpgrade)
  {
    $this->includeReplicasForMajorVersionUpgrade = $includeReplicasForMajorVersionUpgrade;
  }
  /**
   * @return bool
   */
  public function getIncludeReplicasForMajorVersionUpgrade()
  {
    return $this->includeReplicasForMajorVersionUpgrade;
  }
  /**
   * The instance type.
   *
   * Accepted values: SQL_INSTANCE_TYPE_UNSPECIFIED, CLOUD_SQL_INSTANCE,
   * ON_PREMISES_INSTANCE, READ_REPLICA_INSTANCE, READ_POOL_INSTANCE
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
   * The assigned IP addresses for the instance.
   *
   * @param IpMapping[] $ipAddresses
   */
  public function setIpAddresses($ipAddresses)
  {
    $this->ipAddresses = $ipAddresses;
  }
  /**
   * @return IpMapping[]
   */
  public function getIpAddresses()
  {
    return $this->ipAddresses;
  }
  /**
   * The IPv6 address assigned to the instance. (Deprecated) This property was
   * applicable only to First Generation instances.
   *
   * @deprecated
   * @param string $ipv6Address
   */
  public function setIpv6Address($ipv6Address)
  {
    $this->ipv6Address = $ipv6Address;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getIpv6Address()
  {
    return $this->ipv6Address;
  }
  /**
   * This is always `sql#instance`.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The current software version on the instance.
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
   * The name of the instance which will act as primary in the replication
   * setup.
   *
   * @param string $masterInstanceName
   */
  public function setMasterInstanceName($masterInstanceName)
  {
    $this->masterInstanceName = $masterInstanceName;
  }
  /**
   * @return string
   */
  public function getMasterInstanceName()
  {
    return $this->masterInstanceName;
  }
  /**
   * The maximum disk size of the instance in bytes.
   *
   * @deprecated
   * @param string $maxDiskSize
   */
  public function setMaxDiskSize($maxDiskSize)
  {
    $this->maxDiskSize = $maxDiskSize;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getMaxDiskSize()
  {
    return $this->maxDiskSize;
  }
  /**
   * Name of the Cloud SQL instance. This does not include the project ID.
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
   * The number of read pool nodes in a read pool.
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
   * Output only. Entries containing information about each read pool node of
   * the read pool.
   *
   * @param PoolNodeConfig[] $nodes
   */
  public function setNodes($nodes)
  {
    $this->nodes = $nodes;
  }
  /**
   * @return PoolNodeConfig[]
   */
  public function getNodes()
  {
    return $this->nodes;
  }
  /**
   * Configuration specific to on-premises instances.
   *
   * @param OnPremisesConfiguration $onPremisesConfiguration
   */
  public function setOnPremisesConfiguration(OnPremisesConfiguration $onPremisesConfiguration)
  {
    $this->onPremisesConfiguration = $onPremisesConfiguration;
  }
  /**
   * @return OnPremisesConfiguration
   */
  public function getOnPremisesConfiguration()
  {
    return $this->onPremisesConfiguration;
  }
  /**
   * This field represents the report generated by the proactive database
   * wellness job for OutOfDisk issues. * Writers: * the proactive database
   * wellness job for OOD. * Readers: * the proactive database wellness job
   *
   * @param SqlOutOfDiskReport $outOfDiskReport
   */
  public function setOutOfDiskReport(SqlOutOfDiskReport $outOfDiskReport)
  {
    $this->outOfDiskReport = $outOfDiskReport;
  }
  /**
   * @return SqlOutOfDiskReport
   */
  public function getOutOfDiskReport()
  {
    return $this->outOfDiskReport;
  }
  /**
   * Output only. DEPRECATED: please use write_endpoint instead.
   *
   * @deprecated
   * @param string $primaryDnsName
   */
  public function setPrimaryDnsName($primaryDnsName)
  {
    $this->primaryDnsName = $primaryDnsName;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getPrimaryDnsName()
  {
    return $this->primaryDnsName;
  }
  /**
   * The project ID of the project containing the Cloud SQL instance. The Google
   * apps domain is prefixed if applicable.
   *
   * @param string $project
   */
  public function setProject($project)
  {
    $this->project = $project;
  }
  /**
   * @return string
   */
  public function getProject()
  {
    return $this->project;
  }
  /**
   * Output only. The link to service attachment of PSC instance.
   *
   * @param string $pscServiceAttachmentLink
   */
  public function setPscServiceAttachmentLink($pscServiceAttachmentLink)
  {
    $this->pscServiceAttachmentLink = $pscServiceAttachmentLink;
  }
  /**
   * @return string
   */
  public function getPscServiceAttachmentLink()
  {
    return $this->pscServiceAttachmentLink;
  }
  /**
   * The geographical region of the Cloud SQL instance. It can be one of the
   * [regions](https://cloud.google.com/sql/docs/mysql/locations#location-r)
   * where Cloud SQL operates: For example, `asia-east1`, `europe-west1`, and
   * `us-central1`. The default value is `us-central1`.
   *
   * @param string $region
   */
  public function setRegion($region)
  {
    $this->region = $region;
  }
  /**
   * @return string
   */
  public function getRegion()
  {
    return $this->region;
  }
  /**
   * Configuration specific to failover replicas and read replicas.
   *
   * @param ReplicaConfiguration $replicaConfiguration
   */
  public function setReplicaConfiguration(ReplicaConfiguration $replicaConfiguration)
  {
    $this->replicaConfiguration = $replicaConfiguration;
  }
  /**
   * @return ReplicaConfiguration
   */
  public function getReplicaConfiguration()
  {
    return $this->replicaConfiguration;
  }
  /**
   * The replicas of the instance.
   *
   * @param string[] $replicaNames
   */
  public function setReplicaNames($replicaNames)
  {
    $this->replicaNames = $replicaNames;
  }
  /**
   * @return string[]
   */
  public function getReplicaNames()
  {
    return $this->replicaNames;
  }
  /**
   * Optional. A primary instance and disaster recovery (DR) replica pair. A DR
   * replica is a cross-region replica that you designate for failover in the
   * event that the primary instance experiences regional failure. Applicable to
   * MySQL and PostgreSQL.
   *
   * @param ReplicationCluster $replicationCluster
   */
  public function setReplicationCluster(ReplicationCluster $replicationCluster)
  {
    $this->replicationCluster = $replicationCluster;
  }
  /**
   * @return ReplicationCluster
   */
  public function getReplicationCluster()
  {
    return $this->replicationCluster;
  }
  /**
   * Initial root password. Use only on creation. You must set root passwords
   * before you can connect to PostgreSQL instances.
   *
   * @param string $rootPassword
   */
  public function setRootPassword($rootPassword)
  {
    $this->rootPassword = $rootPassword;
  }
  /**
   * @return string
   */
  public function getRootPassword()
  {
    return $this->rootPassword;
  }
  /**
   * Output only. This status indicates whether the instance satisfies PZI. The
   * status is reserved for future use.
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
   * This status indicates whether the instance satisfies PZS. The status is
   * reserved for future use.
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
   * The start time of any upcoming scheduled maintenance for this instance.
   *
   * @param SqlScheduledMaintenance $scheduledMaintenance
   */
  public function setScheduledMaintenance(SqlScheduledMaintenance $scheduledMaintenance)
  {
    $this->scheduledMaintenance = $scheduledMaintenance;
  }
  /**
   * @return SqlScheduledMaintenance
   */
  public function getScheduledMaintenance()
  {
    return $this->scheduledMaintenance;
  }
  /**
   * The Compute Engine zone that the failover instance is currently serving
   * from for a regional instance. This value could be different from the zone
   * that was specified when the instance was created if the instance has failed
   * over to its secondary/failover zone.
   *
   * @param string $secondaryGceZone
   */
  public function setSecondaryGceZone($secondaryGceZone)
  {
    $this->secondaryGceZone = $secondaryGceZone;
  }
  /**
   * @return string
   */
  public function getSecondaryGceZone()
  {
    return $this->secondaryGceZone;
  }
  /**
   * The URI of this resource.
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
   * SSL configuration.
   *
   * @param SslCert $serverCaCert
   */
  public function setServerCaCert(SslCert $serverCaCert)
  {
    $this->serverCaCert = $serverCaCert;
  }
  /**
   * @return SslCert
   */
  public function getServerCaCert()
  {
    return $this->serverCaCert;
  }
  /**
   * The service account email address assigned to the instance.\This property
   * is read-only.
   *
   * @param string $serviceAccountEmailAddress
   */
  public function setServiceAccountEmailAddress($serviceAccountEmailAddress)
  {
    $this->serviceAccountEmailAddress = $serviceAccountEmailAddress;
  }
  /**
   * @return string
   */
  public function getServiceAccountEmailAddress()
  {
    return $this->serviceAccountEmailAddress;
  }
  /**
   * The user settings.
   *
   * @param Settings $settings
   */
  public function setSettings(Settings $settings)
  {
    $this->settings = $settings;
  }
  /**
   * @return Settings
   */
  public function getSettings()
  {
    return $this->settings;
  }
  /**
   * @param self::SQL_NETWORK_ARCHITECTURE_* $sqlNetworkArchitecture
   */
  public function setSqlNetworkArchitecture($sqlNetworkArchitecture)
  {
    $this->sqlNetworkArchitecture = $sqlNetworkArchitecture;
  }
  /**
   * @return self::SQL_NETWORK_ARCHITECTURE_*
   */
  public function getSqlNetworkArchitecture()
  {
    return $this->sqlNetworkArchitecture;
  }
  /**
   * The current serving state of the Cloud SQL instance.
   *
   * Accepted values: SQL_INSTANCE_STATE_UNSPECIFIED, RUNNABLE, SUSPENDED,
   * PENDING_DELETE, PENDING_CREATE, MAINTENANCE, FAILED, ONLINE_MAINTENANCE,
   * REPAIRING
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
   * If the instance state is SUSPENDED, the reason for the suspension.
   *
   * @param string[] $suspensionReason
   */
  public function setSuspensionReason($suspensionReason)
  {
    $this->suspensionReason = $suspensionReason;
  }
  /**
   * @return string[]
   */
  public function getSuspensionReason()
  {
    return $this->suspensionReason;
  }
  /**
   * Input only. Whether Cloud SQL is enabled to switch storing point-in-time
   * recovery log files from a data disk to Cloud Storage.
   *
   * @param bool $switchTransactionLogsToCloudStorageEnabled
   */
  public function setSwitchTransactionLogsToCloudStorageEnabled($switchTransactionLogsToCloudStorageEnabled)
  {
    $this->switchTransactionLogsToCloudStorageEnabled = $switchTransactionLogsToCloudStorageEnabled;
  }
  /**
   * @return bool
   */
  public function getSwitchTransactionLogsToCloudStorageEnabled()
  {
    return $this->switchTransactionLogsToCloudStorageEnabled;
  }
  /**
   * Optional. Input only. Immutable. Tag keys and tag values that are bound to
   * this instance. You must represent each item in the map as: `"" : ""`. For
   * example, a single resource can have the following tags: ```
   * "123/environment": "production", "123/costCenter": "marketing", ``` For
   * more information on tag creation and management, see
   * https://cloud.google.com/resource-manager/docs/tags/tags-overview.
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
   * Output only. All database versions that are available for upgrade.
   *
   * @param AvailableDatabaseVersion[] $upgradableDatabaseVersions
   */
  public function setUpgradableDatabaseVersions($upgradableDatabaseVersions)
  {
    $this->upgradableDatabaseVersions = $upgradableDatabaseVersions;
  }
  /**
   * @return AvailableDatabaseVersion[]
   */
  public function getUpgradableDatabaseVersions()
  {
    return $this->upgradableDatabaseVersions;
  }
  /**
   * Output only. The dns name of the primary instance in a replication group.
   *
   * @param string $writeEndpoint
   */
  public function setWriteEndpoint($writeEndpoint)
  {
    $this->writeEndpoint = $writeEndpoint;
  }
  /**
   * @return string
   */
  public function getWriteEndpoint()
  {
    return $this->writeEndpoint;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DatabaseInstance::class, 'Google_Service_SQLAdmin_DatabaseInstance');
