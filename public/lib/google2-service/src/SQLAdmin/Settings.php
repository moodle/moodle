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

class Settings extends \Google\Collection
{
  /**
   * Unknown activation plan.
   */
  public const ACTIVATION_POLICY_SQL_ACTIVATION_POLICY_UNSPECIFIED = 'SQL_ACTIVATION_POLICY_UNSPECIFIED';
  /**
   * The instance is always up and running.
   */
  public const ACTIVATION_POLICY_ALWAYS = 'ALWAYS';
  /**
   * The instance never starts.
   */
  public const ACTIVATION_POLICY_NEVER = 'NEVER';
  /**
   * The instance starts upon receiving requests.
   *
   * @deprecated
   */
  public const ACTIVATION_POLICY_ON_DEMAND = 'ON_DEMAND';
  /**
   * This is an unknown Availability type.
   */
  public const AVAILABILITY_TYPE_SQL_AVAILABILITY_TYPE_UNSPECIFIED = 'SQL_AVAILABILITY_TYPE_UNSPECIFIED';
  /**
   * Zonal available instance.
   */
  public const AVAILABILITY_TYPE_ZONAL = 'ZONAL';
  /**
   * Regional available instance.
   */
  public const AVAILABILITY_TYPE_REGIONAL = 'REGIONAL';
  /**
   * The requirement for Cloud SQL connectors is unknown.
   */
  public const CONNECTOR_ENFORCEMENT_CONNECTOR_ENFORCEMENT_UNSPECIFIED = 'CONNECTOR_ENFORCEMENT_UNSPECIFIED';
  /**
   * Do not require Cloud SQL connectors.
   */
  public const CONNECTOR_ENFORCEMENT_NOT_REQUIRED = 'NOT_REQUIRED';
  /**
   * Require all connections to use Cloud SQL connectors, including the Cloud
   * SQL Auth Proxy and Cloud SQL Java, Python, and Go connectors. Note: This
   * disables all existing authorized networks.
   */
  public const CONNECTOR_ENFORCEMENT_REQUIRED = 'REQUIRED';
  /**
   * Unspecified, effectively the same as `DISALLOW_DATA_API`.
   */
  public const DATA_API_ACCESS_DATA_API_ACCESS_UNSPECIFIED = 'DATA_API_ACCESS_UNSPECIFIED';
  /**
   * Disallow using ExecuteSql API to connect to the instance.
   */
  public const DATA_API_ACCESS_DISALLOW_DATA_API = 'DISALLOW_DATA_API';
  /**
   * Allow using ExecuteSql API to connect to the instance. For private IP
   * instances, this allows authorized users to access the instance from the
   * public internet using ExecuteSql API.
   */
  public const DATA_API_ACCESS_ALLOW_DATA_API = 'ALLOW_DATA_API';
  /**
   * This is an unknown data disk type.
   */
  public const DATA_DISK_TYPE_SQL_DATA_DISK_TYPE_UNSPECIFIED = 'SQL_DATA_DISK_TYPE_UNSPECIFIED';
  /**
   * An SSD data disk.
   */
  public const DATA_DISK_TYPE_PD_SSD = 'PD_SSD';
  /**
   * An HDD data disk.
   */
  public const DATA_DISK_TYPE_PD_HDD = 'PD_HDD';
  /**
   * This field is deprecated and will be removed from a future version of the
   * API.
   *
   * @deprecated
   */
  public const DATA_DISK_TYPE_OBSOLETE_LOCAL_SSD = 'OBSOLETE_LOCAL_SSD';
  /**
   * A Hyperdisk Balanced data disk.
   */
  public const DATA_DISK_TYPE_HYPERDISK_BALANCED = 'HYPERDISK_BALANCED';
  /**
   * The instance did not specify the edition.
   */
  public const EDITION_EDITION_UNSPECIFIED = 'EDITION_UNSPECIFIED';
  /**
   * The instance is an enterprise edition.
   */
  public const EDITION_ENTERPRISE = 'ENTERPRISE';
  /**
   * The instance is an Enterprise Plus edition.
   */
  public const EDITION_ENTERPRISE_PLUS = 'ENTERPRISE_PLUS';
  /**
   * This is an unknown pricing plan for this instance.
   */
  public const PRICING_PLAN_SQL_PRICING_PLAN_UNSPECIFIED = 'SQL_PRICING_PLAN_UNSPECIFIED';
  /**
   * The instance is billed at a monthly flat rate.
   */
  public const PRICING_PLAN_PACKAGE = 'PACKAGE';
  /**
   * The instance is billed per usage.
   */
  public const PRICING_PLAN_PER_USE = 'PER_USE';
  /**
   * This is an unknown replication type for a Cloud SQL instance.
   */
  public const REPLICATION_TYPE_SQL_REPLICATION_TYPE_UNSPECIFIED = 'SQL_REPLICATION_TYPE_UNSPECIFIED';
  /**
   * The synchronous replication mode for First Generation instances. It is the
   * default value.
   */
  public const REPLICATION_TYPE_SYNCHRONOUS = 'SYNCHRONOUS';
  /**
   * The asynchronous replication mode for First Generation instances. It
   * provides a slight performance gain, but if an outage occurs while this
   * option is set to asynchronous, you can lose up to a few seconds of updates
   * to your data.
   */
  public const REPLICATION_TYPE_ASYNCHRONOUS = 'ASYNCHRONOUS';
  protected $collection_key = 'denyMaintenancePeriods';
  /**
   * The activation policy specifies when the instance is activated; it is
   * applicable only when the instance state is RUNNABLE. Valid values: *
   * `ALWAYS`: The instance is on, and remains so even in the absence of
   * connection requests. * `NEVER`: The instance is off; it is not activated,
   * even if a connection request arrives.
   *
   * @var string
   */
  public $activationPolicy;
  protected $activeDirectoryConfigType = SqlActiveDirectoryConfig::class;
  protected $activeDirectoryConfigDataType = '';
  protected $advancedMachineFeaturesType = AdvancedMachineFeatures::class;
  protected $advancedMachineFeaturesDataType = '';
  /**
   * The App Engine app IDs that can access this instance. (Deprecated) Applied
   * to First Generation instances only.
   *
   * @deprecated
   * @var string[]
   */
  public $authorizedGaeApplications;
  /**
   * Optional. Cloud SQL for MySQL auto-upgrade configuration. When this
   * parameter is set to true, auto-upgrade is enabled for MySQL 8.0 minor
   * versions. The MySQL version must be 8.0.35 or higher.
   *
   * @var bool
   */
  public $autoUpgradeEnabled;
  /**
   * Availability type. Potential values: * `ZONAL`: The instance serves data
   * from only one zone. Outages in that zone affect data accessibility. *
   * `REGIONAL`: The instance can serve data from more than one zone in a region
   * (it is highly available)./ For more information, see [Overview of the High
   * Availability Configuration](https://cloud.google.com/sql/docs/mysql/high-
   * availability).
   *
   * @var string
   */
  public $availabilityType;
  protected $backupConfigurationType = BackupConfiguration::class;
  protected $backupConfigurationDataType = '';
  /**
   * The name of server Instance collation.
   *
   * @var string
   */
  public $collation;
  protected $connectionPoolConfigType = ConnectionPoolConfig::class;
  protected $connectionPoolConfigDataType = '';
  /**
   * Specifies if connections must use Cloud SQL connectors. Option values
   * include the following: `NOT_REQUIRED` (Cloud SQL instances can be connected
   * without Cloud SQL Connectors) and `REQUIRED` (Only allow connections that
   * use Cloud SQL Connectors). Note that using REQUIRED disables all existing
   * authorized networks. If this field is not specified when creating a new
   * instance, NOT_REQUIRED is used. If this field is not specified when
   * patching or updating an existing instance, it is left unchanged in the
   * instance.
   *
   * @var string
   */
  public $connectorEnforcement;
  /**
   * Configuration specific to read replica instances. Indicates whether
   * database flags for crash-safe replication are enabled. This property was
   * only applicable to First Generation instances.
   *
   * @deprecated
   * @var bool
   */
  public $crashSafeReplicationEnabled;
  /**
   * This parameter controls whether to allow using ExecuteSql API to connect to
   * the instance. Not allowed by default.
   *
   * @var string
   */
  public $dataApiAccess;
  protected $dataCacheConfigType = DataCacheConfig::class;
  protected $dataCacheConfigDataType = '';
  /**
   * Optional. Provisioned number of I/O operations per second for the data
   * disk. This field is only used for hyperdisk-balanced disk types.
   *
   * @var string
   */
  public $dataDiskProvisionedIops;
  /**
   * Optional. Provisioned throughput measured in MiB per second for the data
   * disk. This field is only used for hyperdisk-balanced disk types.
   *
   * @var string
   */
  public $dataDiskProvisionedThroughput;
  /**
   * The size of data disk, in GB. The data disk size minimum is 10GB.
   *
   * @var string
   */
  public $dataDiskSizeGb;
  /**
   * The type of data disk: `PD_SSD` (default) or `PD_HDD`. Not used for First
   * Generation instances.
   *
   * @var string
   */
  public $dataDiskType;
  protected $databaseFlagsType = DatabaseFlags::class;
  protected $databaseFlagsDataType = 'array';
  /**
   * Configuration specific to read replica instances. Indicates whether
   * replication is enabled or not. WARNING: Changing this restarts the
   * instance.
   *
   * @var bool
   */
  public $databaseReplicationEnabled;
  /**
   * Configuration to protect against accidental instance deletion.
   *
   * @var bool
   */
  public $deletionProtectionEnabled;
  protected $denyMaintenancePeriodsType = DenyMaintenancePeriod::class;
  protected $denyMaintenancePeriodsDataType = 'array';
  /**
   * Optional. The edition of the instance.
   *
   * @var string
   */
  public $edition;
  /**
   * Optional. By default, Cloud SQL instances have schema extraction disabled
   * for Dataplex. When this parameter is set to true, schema extraction for
   * Dataplex on Cloud SQL instances is activated.
   *
   * @var bool
   */
  public $enableDataplexIntegration;
  /**
   * Optional. When this parameter is set to true, Cloud SQL instances can
   * connect to Vertex AI to pass requests for real-time predictions and
   * insights to the AI. The default value is false. This applies only to Cloud
   * SQL for MySQL and Cloud SQL for PostgreSQL instances.
   *
   * @var bool
   */
  public $enableGoogleMlIntegration;
  protected $entraidConfigType = SqlServerEntraIdConfig::class;
  protected $entraidConfigDataType = '';
  protected $finalBackupConfigType = FinalBackupConfig::class;
  protected $finalBackupConfigDataType = '';
  protected $insightsConfigType = InsightsConfig::class;
  protected $insightsConfigDataType = '';
  protected $ipConfigurationType = IpConfiguration::class;
  protected $ipConfigurationDataType = '';
  /**
   * This is always `sql#settings`.
   *
   * @var string
   */
  public $kind;
  protected $locationPreferenceType = LocationPreference::class;
  protected $locationPreferenceDataType = '';
  protected $maintenanceWindowType = MaintenanceWindow::class;
  protected $maintenanceWindowDataType = '';
  protected $passwordValidationPolicyType = PasswordValidationPolicy::class;
  protected $passwordValidationPolicyDataType = '';
  protected $performanceCaptureConfigType = PerformanceCaptureConfig::class;
  protected $performanceCaptureConfigDataType = '';
  /**
   * The pricing plan for this instance. This can be either `PER_USE` or
   * `PACKAGE`. Only `PER_USE` is supported for Second Generation instances.
   *
   * @var string
   */
  public $pricingPlan;
  protected $readPoolAutoScaleConfigType = ReadPoolAutoScaleConfig::class;
  protected $readPoolAutoScaleConfigDataType = '';
  /**
   * Optional. Configuration value for recreation of replica after certain
   * replication lag
   *
   * @var int
   */
  public $replicationLagMaxSeconds;
  /**
   * The type of replication this instance uses. This can be either
   * `ASYNCHRONOUS` or `SYNCHRONOUS`. (Deprecated) This property was only
   * applicable to First Generation instances.
   *
   * @deprecated
   * @var string
   */
  public $replicationType;
  /**
   * Optional. When this parameter is set to true, Cloud SQL retains backups of
   * the instance even after the instance is deleted. The ON_DEMAND backup will
   * be retained until customer deletes the backup or the project. The AUTOMATED
   * backup will be retained based on the backups retention setting.
   *
   * @var bool
   */
  public $retainBackupsOnDelete;
  /**
   * The version of instance settings. This is a required field for update
   * method to make sure concurrent updates are handled properly. During update,
   * use the most recent settingsVersion value for this instance and do not try
   * to update this value.
   *
   * @var string
   */
  public $settingsVersion;
  protected $sqlServerAuditConfigType = SqlServerAuditConfig::class;
  protected $sqlServerAuditConfigDataType = '';
  /**
   * Configuration to increase storage size automatically. The default value is
   * true.
   *
   * @var bool
   */
  public $storageAutoResize;
  /**
   * The maximum size to which storage capacity can be automatically increased.
   * The default value is 0, which specifies that there is no limit.
   *
   * @var string
   */
  public $storageAutoResizeLimit;
  /**
   * The tier (or machine type) for this instance, for example `db-
   * custom-1-3840`. WARNING: Changing this restarts the instance.
   *
   * @var string
   */
  public $tier;
  /**
   * Server timezone, relevant only for Cloud SQL for SQL Server.
   *
   * @var string
   */
  public $timeZone;
  /**
   * User-provided labels, represented as a dictionary where each label is a
   * single key value pair.
   *
   * @var string[]
   */
  public $userLabels;

  /**
   * The activation policy specifies when the instance is activated; it is
   * applicable only when the instance state is RUNNABLE. Valid values: *
   * `ALWAYS`: The instance is on, and remains so even in the absence of
   * connection requests. * `NEVER`: The instance is off; it is not activated,
   * even if a connection request arrives.
   *
   * Accepted values: SQL_ACTIVATION_POLICY_UNSPECIFIED, ALWAYS, NEVER,
   * ON_DEMAND
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
   * Active Directory configuration, relevant only for Cloud SQL for SQL Server.
   *
   * @param SqlActiveDirectoryConfig $activeDirectoryConfig
   */
  public function setActiveDirectoryConfig(SqlActiveDirectoryConfig $activeDirectoryConfig)
  {
    $this->activeDirectoryConfig = $activeDirectoryConfig;
  }
  /**
   * @return SqlActiveDirectoryConfig
   */
  public function getActiveDirectoryConfig()
  {
    return $this->activeDirectoryConfig;
  }
  /**
   * Specifies advanced machine configuration for the instances relevant only
   * for SQL Server.
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
   * The App Engine app IDs that can access this instance. (Deprecated) Applied
   * to First Generation instances only.
   *
   * @deprecated
   * @param string[] $authorizedGaeApplications
   */
  public function setAuthorizedGaeApplications($authorizedGaeApplications)
  {
    $this->authorizedGaeApplications = $authorizedGaeApplications;
  }
  /**
   * @deprecated
   * @return string[]
   */
  public function getAuthorizedGaeApplications()
  {
    return $this->authorizedGaeApplications;
  }
  /**
   * Optional. Cloud SQL for MySQL auto-upgrade configuration. When this
   * parameter is set to true, auto-upgrade is enabled for MySQL 8.0 minor
   * versions. The MySQL version must be 8.0.35 or higher.
   *
   * @param bool $autoUpgradeEnabled
   */
  public function setAutoUpgradeEnabled($autoUpgradeEnabled)
  {
    $this->autoUpgradeEnabled = $autoUpgradeEnabled;
  }
  /**
   * @return bool
   */
  public function getAutoUpgradeEnabled()
  {
    return $this->autoUpgradeEnabled;
  }
  /**
   * Availability type. Potential values: * `ZONAL`: The instance serves data
   * from only one zone. Outages in that zone affect data accessibility. *
   * `REGIONAL`: The instance can serve data from more than one zone in a region
   * (it is highly available)./ For more information, see [Overview of the High
   * Availability Configuration](https://cloud.google.com/sql/docs/mysql/high-
   * availability).
   *
   * Accepted values: SQL_AVAILABILITY_TYPE_UNSPECIFIED, ZONAL, REGIONAL
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
   * The daily backup configuration for the instance.
   *
   * @param BackupConfiguration $backupConfiguration
   */
  public function setBackupConfiguration(BackupConfiguration $backupConfiguration)
  {
    $this->backupConfiguration = $backupConfiguration;
  }
  /**
   * @return BackupConfiguration
   */
  public function getBackupConfiguration()
  {
    return $this->backupConfiguration;
  }
  /**
   * The name of server Instance collation.
   *
   * @param string $collation
   */
  public function setCollation($collation)
  {
    $this->collation = $collation;
  }
  /**
   * @return string
   */
  public function getCollation()
  {
    return $this->collation;
  }
  /**
   * Optional. The managed connection pooling configuration for the instance.
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
   * Specifies if connections must use Cloud SQL connectors. Option values
   * include the following: `NOT_REQUIRED` (Cloud SQL instances can be connected
   * without Cloud SQL Connectors) and `REQUIRED` (Only allow connections that
   * use Cloud SQL Connectors). Note that using REQUIRED disables all existing
   * authorized networks. If this field is not specified when creating a new
   * instance, NOT_REQUIRED is used. If this field is not specified when
   * patching or updating an existing instance, it is left unchanged in the
   * instance.
   *
   * Accepted values: CONNECTOR_ENFORCEMENT_UNSPECIFIED, NOT_REQUIRED, REQUIRED
   *
   * @param self::CONNECTOR_ENFORCEMENT_* $connectorEnforcement
   */
  public function setConnectorEnforcement($connectorEnforcement)
  {
    $this->connectorEnforcement = $connectorEnforcement;
  }
  /**
   * @return self::CONNECTOR_ENFORCEMENT_*
   */
  public function getConnectorEnforcement()
  {
    return $this->connectorEnforcement;
  }
  /**
   * Configuration specific to read replica instances. Indicates whether
   * database flags for crash-safe replication are enabled. This property was
   * only applicable to First Generation instances.
   *
   * @deprecated
   * @param bool $crashSafeReplicationEnabled
   */
  public function setCrashSafeReplicationEnabled($crashSafeReplicationEnabled)
  {
    $this->crashSafeReplicationEnabled = $crashSafeReplicationEnabled;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getCrashSafeReplicationEnabled()
  {
    return $this->crashSafeReplicationEnabled;
  }
  /**
   * This parameter controls whether to allow using ExecuteSql API to connect to
   * the instance. Not allowed by default.
   *
   * Accepted values: DATA_API_ACCESS_UNSPECIFIED, DISALLOW_DATA_API,
   * ALLOW_DATA_API
   *
   * @param self::DATA_API_ACCESS_* $dataApiAccess
   */
  public function setDataApiAccess($dataApiAccess)
  {
    $this->dataApiAccess = $dataApiAccess;
  }
  /**
   * @return self::DATA_API_ACCESS_*
   */
  public function getDataApiAccess()
  {
    return $this->dataApiAccess;
  }
  /**
   * Configuration for data cache.
   *
   * @param DataCacheConfig $dataCacheConfig
   */
  public function setDataCacheConfig(DataCacheConfig $dataCacheConfig)
  {
    $this->dataCacheConfig = $dataCacheConfig;
  }
  /**
   * @return DataCacheConfig
   */
  public function getDataCacheConfig()
  {
    return $this->dataCacheConfig;
  }
  /**
   * Optional. Provisioned number of I/O operations per second for the data
   * disk. This field is only used for hyperdisk-balanced disk types.
   *
   * @param string $dataDiskProvisionedIops
   */
  public function setDataDiskProvisionedIops($dataDiskProvisionedIops)
  {
    $this->dataDiskProvisionedIops = $dataDiskProvisionedIops;
  }
  /**
   * @return string
   */
  public function getDataDiskProvisionedIops()
  {
    return $this->dataDiskProvisionedIops;
  }
  /**
   * Optional. Provisioned throughput measured in MiB per second for the data
   * disk. This field is only used for hyperdisk-balanced disk types.
   *
   * @param string $dataDiskProvisionedThroughput
   */
  public function setDataDiskProvisionedThroughput($dataDiskProvisionedThroughput)
  {
    $this->dataDiskProvisionedThroughput = $dataDiskProvisionedThroughput;
  }
  /**
   * @return string
   */
  public function getDataDiskProvisionedThroughput()
  {
    return $this->dataDiskProvisionedThroughput;
  }
  /**
   * The size of data disk, in GB. The data disk size minimum is 10GB.
   *
   * @param string $dataDiskSizeGb
   */
  public function setDataDiskSizeGb($dataDiskSizeGb)
  {
    $this->dataDiskSizeGb = $dataDiskSizeGb;
  }
  /**
   * @return string
   */
  public function getDataDiskSizeGb()
  {
    return $this->dataDiskSizeGb;
  }
  /**
   * The type of data disk: `PD_SSD` (default) or `PD_HDD`. Not used for First
   * Generation instances.
   *
   * Accepted values: SQL_DATA_DISK_TYPE_UNSPECIFIED, PD_SSD, PD_HDD,
   * OBSOLETE_LOCAL_SSD, HYPERDISK_BALANCED
   *
   * @param self::DATA_DISK_TYPE_* $dataDiskType
   */
  public function setDataDiskType($dataDiskType)
  {
    $this->dataDiskType = $dataDiskType;
  }
  /**
   * @return self::DATA_DISK_TYPE_*
   */
  public function getDataDiskType()
  {
    return $this->dataDiskType;
  }
  /**
   * The database flags passed to the instance at startup.
   *
   * @param DatabaseFlags[] $databaseFlags
   */
  public function setDatabaseFlags($databaseFlags)
  {
    $this->databaseFlags = $databaseFlags;
  }
  /**
   * @return DatabaseFlags[]
   */
  public function getDatabaseFlags()
  {
    return $this->databaseFlags;
  }
  /**
   * Configuration specific to read replica instances. Indicates whether
   * replication is enabled or not. WARNING: Changing this restarts the
   * instance.
   *
   * @param bool $databaseReplicationEnabled
   */
  public function setDatabaseReplicationEnabled($databaseReplicationEnabled)
  {
    $this->databaseReplicationEnabled = $databaseReplicationEnabled;
  }
  /**
   * @return bool
   */
  public function getDatabaseReplicationEnabled()
  {
    return $this->databaseReplicationEnabled;
  }
  /**
   * Configuration to protect against accidental instance deletion.
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
   * Deny maintenance periods
   *
   * @param DenyMaintenancePeriod[] $denyMaintenancePeriods
   */
  public function setDenyMaintenancePeriods($denyMaintenancePeriods)
  {
    $this->denyMaintenancePeriods = $denyMaintenancePeriods;
  }
  /**
   * @return DenyMaintenancePeriod[]
   */
  public function getDenyMaintenancePeriods()
  {
    return $this->denyMaintenancePeriods;
  }
  /**
   * Optional. The edition of the instance.
   *
   * Accepted values: EDITION_UNSPECIFIED, ENTERPRISE, ENTERPRISE_PLUS
   *
   * @param self::EDITION_* $edition
   */
  public function setEdition($edition)
  {
    $this->edition = $edition;
  }
  /**
   * @return self::EDITION_*
   */
  public function getEdition()
  {
    return $this->edition;
  }
  /**
   * Optional. By default, Cloud SQL instances have schema extraction disabled
   * for Dataplex. When this parameter is set to true, schema extraction for
   * Dataplex on Cloud SQL instances is activated.
   *
   * @param bool $enableDataplexIntegration
   */
  public function setEnableDataplexIntegration($enableDataplexIntegration)
  {
    $this->enableDataplexIntegration = $enableDataplexIntegration;
  }
  /**
   * @return bool
   */
  public function getEnableDataplexIntegration()
  {
    return $this->enableDataplexIntegration;
  }
  /**
   * Optional. When this parameter is set to true, Cloud SQL instances can
   * connect to Vertex AI to pass requests for real-time predictions and
   * insights to the AI. The default value is false. This applies only to Cloud
   * SQL for MySQL and Cloud SQL for PostgreSQL instances.
   *
   * @param bool $enableGoogleMlIntegration
   */
  public function setEnableGoogleMlIntegration($enableGoogleMlIntegration)
  {
    $this->enableGoogleMlIntegration = $enableGoogleMlIntegration;
  }
  /**
   * @return bool
   */
  public function getEnableGoogleMlIntegration()
  {
    return $this->enableGoogleMlIntegration;
  }
  /**
   * Optional. The Microsoft Entra ID configuration for the SQL Server instance.
   *
   * @param SqlServerEntraIdConfig $entraidConfig
   */
  public function setEntraidConfig(SqlServerEntraIdConfig $entraidConfig)
  {
    $this->entraidConfig = $entraidConfig;
  }
  /**
   * @return SqlServerEntraIdConfig
   */
  public function getEntraidConfig()
  {
    return $this->entraidConfig;
  }
  /**
   * Optional. The final backup configuration for the instance.
   *
   * @param FinalBackupConfig $finalBackupConfig
   */
  public function setFinalBackupConfig(FinalBackupConfig $finalBackupConfig)
  {
    $this->finalBackupConfig = $finalBackupConfig;
  }
  /**
   * @return FinalBackupConfig
   */
  public function getFinalBackupConfig()
  {
    return $this->finalBackupConfig;
  }
  /**
   * Insights configuration, for now relevant only for Postgres.
   *
   * @param InsightsConfig $insightsConfig
   */
  public function setInsightsConfig(InsightsConfig $insightsConfig)
  {
    $this->insightsConfig = $insightsConfig;
  }
  /**
   * @return InsightsConfig
   */
  public function getInsightsConfig()
  {
    return $this->insightsConfig;
  }
  /**
   * The settings for IP Management. This allows to enable or disable the
   * instance IP and manage which external networks can connect to the instance.
   * The IPv4 address cannot be disabled for Second Generation instances.
   *
   * @param IpConfiguration $ipConfiguration
   */
  public function setIpConfiguration(IpConfiguration $ipConfiguration)
  {
    $this->ipConfiguration = $ipConfiguration;
  }
  /**
   * @return IpConfiguration
   */
  public function getIpConfiguration()
  {
    return $this->ipConfiguration;
  }
  /**
   * This is always `sql#settings`.
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
   * The location preference settings. This allows the instance to be located as
   * near as possible to either an App Engine app or Compute Engine zone for
   * better performance. App Engine co-location was only applicable to First
   * Generation instances.
   *
   * @param LocationPreference $locationPreference
   */
  public function setLocationPreference(LocationPreference $locationPreference)
  {
    $this->locationPreference = $locationPreference;
  }
  /**
   * @return LocationPreference
   */
  public function getLocationPreference()
  {
    return $this->locationPreference;
  }
  /**
   * The maintenance window for this instance. This specifies when the instance
   * can be restarted for maintenance purposes.
   *
   * @param MaintenanceWindow $maintenanceWindow
   */
  public function setMaintenanceWindow(MaintenanceWindow $maintenanceWindow)
  {
    $this->maintenanceWindow = $maintenanceWindow;
  }
  /**
   * @return MaintenanceWindow
   */
  public function getMaintenanceWindow()
  {
    return $this->maintenanceWindow;
  }
  /**
   * The local user password validation policy of the instance.
   *
   * @param PasswordValidationPolicy $passwordValidationPolicy
   */
  public function setPasswordValidationPolicy(PasswordValidationPolicy $passwordValidationPolicy)
  {
    $this->passwordValidationPolicy = $passwordValidationPolicy;
  }
  /**
   * @return PasswordValidationPolicy
   */
  public function getPasswordValidationPolicy()
  {
    return $this->passwordValidationPolicy;
  }
  /**
   * Optional. Configuration for Performance Capture, provides diagnostic
   * metrics during high load situations.
   *
   * @param PerformanceCaptureConfig $performanceCaptureConfig
   */
  public function setPerformanceCaptureConfig(PerformanceCaptureConfig $performanceCaptureConfig)
  {
    $this->performanceCaptureConfig = $performanceCaptureConfig;
  }
  /**
   * @return PerformanceCaptureConfig
   */
  public function getPerformanceCaptureConfig()
  {
    return $this->performanceCaptureConfig;
  }
  /**
   * The pricing plan for this instance. This can be either `PER_USE` or
   * `PACKAGE`. Only `PER_USE` is supported for Second Generation instances.
   *
   * Accepted values: SQL_PRICING_PLAN_UNSPECIFIED, PACKAGE, PER_USE
   *
   * @param self::PRICING_PLAN_* $pricingPlan
   */
  public function setPricingPlan($pricingPlan)
  {
    $this->pricingPlan = $pricingPlan;
  }
  /**
   * @return self::PRICING_PLAN_*
   */
  public function getPricingPlan()
  {
    return $this->pricingPlan;
  }
  /**
   * Optional. The read pool auto-scale configuration for the instance.
   *
   * @param ReadPoolAutoScaleConfig $readPoolAutoScaleConfig
   */
  public function setReadPoolAutoScaleConfig(ReadPoolAutoScaleConfig $readPoolAutoScaleConfig)
  {
    $this->readPoolAutoScaleConfig = $readPoolAutoScaleConfig;
  }
  /**
   * @return ReadPoolAutoScaleConfig
   */
  public function getReadPoolAutoScaleConfig()
  {
    return $this->readPoolAutoScaleConfig;
  }
  /**
   * Optional. Configuration value for recreation of replica after certain
   * replication lag
   *
   * @param int $replicationLagMaxSeconds
   */
  public function setReplicationLagMaxSeconds($replicationLagMaxSeconds)
  {
    $this->replicationLagMaxSeconds = $replicationLagMaxSeconds;
  }
  /**
   * @return int
   */
  public function getReplicationLagMaxSeconds()
  {
    return $this->replicationLagMaxSeconds;
  }
  /**
   * The type of replication this instance uses. This can be either
   * `ASYNCHRONOUS` or `SYNCHRONOUS`. (Deprecated) This property was only
   * applicable to First Generation instances.
   *
   * Accepted values: SQL_REPLICATION_TYPE_UNSPECIFIED, SYNCHRONOUS,
   * ASYNCHRONOUS
   *
   * @deprecated
   * @param self::REPLICATION_TYPE_* $replicationType
   */
  public function setReplicationType($replicationType)
  {
    $this->replicationType = $replicationType;
  }
  /**
   * @deprecated
   * @return self::REPLICATION_TYPE_*
   */
  public function getReplicationType()
  {
    return $this->replicationType;
  }
  /**
   * Optional. When this parameter is set to true, Cloud SQL retains backups of
   * the instance even after the instance is deleted. The ON_DEMAND backup will
   * be retained until customer deletes the backup or the project. The AUTOMATED
   * backup will be retained based on the backups retention setting.
   *
   * @param bool $retainBackupsOnDelete
   */
  public function setRetainBackupsOnDelete($retainBackupsOnDelete)
  {
    $this->retainBackupsOnDelete = $retainBackupsOnDelete;
  }
  /**
   * @return bool
   */
  public function getRetainBackupsOnDelete()
  {
    return $this->retainBackupsOnDelete;
  }
  /**
   * The version of instance settings. This is a required field for update
   * method to make sure concurrent updates are handled properly. During update,
   * use the most recent settingsVersion value for this instance and do not try
   * to update this value.
   *
   * @param string $settingsVersion
   */
  public function setSettingsVersion($settingsVersion)
  {
    $this->settingsVersion = $settingsVersion;
  }
  /**
   * @return string
   */
  public function getSettingsVersion()
  {
    return $this->settingsVersion;
  }
  /**
   * SQL Server specific audit configuration.
   *
   * @param SqlServerAuditConfig $sqlServerAuditConfig
   */
  public function setSqlServerAuditConfig(SqlServerAuditConfig $sqlServerAuditConfig)
  {
    $this->sqlServerAuditConfig = $sqlServerAuditConfig;
  }
  /**
   * @return SqlServerAuditConfig
   */
  public function getSqlServerAuditConfig()
  {
    return $this->sqlServerAuditConfig;
  }
  /**
   * Configuration to increase storage size automatically. The default value is
   * true.
   *
   * @param bool $storageAutoResize
   */
  public function setStorageAutoResize($storageAutoResize)
  {
    $this->storageAutoResize = $storageAutoResize;
  }
  /**
   * @return bool
   */
  public function getStorageAutoResize()
  {
    return $this->storageAutoResize;
  }
  /**
   * The maximum size to which storage capacity can be automatically increased.
   * The default value is 0, which specifies that there is no limit.
   *
   * @param string $storageAutoResizeLimit
   */
  public function setStorageAutoResizeLimit($storageAutoResizeLimit)
  {
    $this->storageAutoResizeLimit = $storageAutoResizeLimit;
  }
  /**
   * @return string
   */
  public function getStorageAutoResizeLimit()
  {
    return $this->storageAutoResizeLimit;
  }
  /**
   * The tier (or machine type) for this instance, for example `db-
   * custom-1-3840`. WARNING: Changing this restarts the instance.
   *
   * @param string $tier
   */
  public function setTier($tier)
  {
    $this->tier = $tier;
  }
  /**
   * @return string
   */
  public function getTier()
  {
    return $this->tier;
  }
  /**
   * Server timezone, relevant only for Cloud SQL for SQL Server.
   *
   * @param string $timeZone
   */
  public function setTimeZone($timeZone)
  {
    $this->timeZone = $timeZone;
  }
  /**
   * @return string
   */
  public function getTimeZone()
  {
    return $this->timeZone;
  }
  /**
   * User-provided labels, represented as a dictionary where each label is a
   * single key value pair.
   *
   * @param string[] $userLabels
   */
  public function setUserLabels($userLabels)
  {
    $this->userLabels = $userLabels;
  }
  /**
   * @return string[]
   */
  public function getUserLabels()
  {
    return $this->userLabels;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Settings::class, 'Google_Service_SQLAdmin_Settings');
