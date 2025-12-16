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

namespace Google\Service\OracleDatabase;

class AutonomousDatabaseProperties extends \Google\Collection
{
  /**
   * Default unspecified value.
   */
  public const DATA_SAFE_STATE_DATA_SAFE_STATE_UNSPECIFIED = 'DATA_SAFE_STATE_UNSPECIFIED';
  /**
   * Registering data safe state.
   */
  public const DATA_SAFE_STATE_REGISTERING = 'REGISTERING';
  /**
   * Registered data safe state.
   */
  public const DATA_SAFE_STATE_REGISTERED = 'REGISTERED';
  /**
   * Deregistering data safe state.
   */
  public const DATA_SAFE_STATE_DEREGISTERING = 'DEREGISTERING';
  /**
   * Not registered data safe state.
   */
  public const DATA_SAFE_STATE_NOT_REGISTERED = 'NOT_REGISTERED';
  /**
   * Failed data safe state.
   */
  public const DATA_SAFE_STATE_FAILED = 'FAILED';
  /**
   * Default unspecified value.
   */
  public const DATABASE_MANAGEMENT_STATE_DATABASE_MANAGEMENT_STATE_UNSPECIFIED = 'DATABASE_MANAGEMENT_STATE_UNSPECIFIED';
  /**
   * Enabling Database Management state
   */
  public const DATABASE_MANAGEMENT_STATE_ENABLING = 'ENABLING';
  /**
   * Enabled Database Management state
   */
  public const DATABASE_MANAGEMENT_STATE_ENABLED = 'ENABLED';
  /**
   * Disabling Database Management state
   */
  public const DATABASE_MANAGEMENT_STATE_DISABLING = 'DISABLING';
  /**
   * Not Enabled Database Management state
   */
  public const DATABASE_MANAGEMENT_STATE_NOT_ENABLED = 'NOT_ENABLED';
  /**
   * Failed enabling Database Management state
   */
  public const DATABASE_MANAGEMENT_STATE_FAILED_ENABLING = 'FAILED_ENABLING';
  /**
   * Failed disabling Database Management state
   */
  public const DATABASE_MANAGEMENT_STATE_FAILED_DISABLING = 'FAILED_DISABLING';
  /**
   * Default unspecified value.
   */
  public const DB_EDITION_DATABASE_EDITION_UNSPECIFIED = 'DATABASE_EDITION_UNSPECIFIED';
  /**
   * Standard Database Edition
   */
  public const DB_EDITION_STANDARD_EDITION = 'STANDARD_EDITION';
  /**
   * Enterprise Database Edition
   */
  public const DB_EDITION_ENTERPRISE_EDITION = 'ENTERPRISE_EDITION';
  /**
   * Default unspecified value.
   */
  public const DB_WORKLOAD_DB_WORKLOAD_UNSPECIFIED = 'DB_WORKLOAD_UNSPECIFIED';
  /**
   * Autonomous Transaction Processing database.
   */
  public const DB_WORKLOAD_OLTP = 'OLTP';
  /**
   * Autonomous Data Warehouse database.
   */
  public const DB_WORKLOAD_DW = 'DW';
  /**
   * Autonomous JSON Database.
   */
  public const DB_WORKLOAD_AJD = 'AJD';
  /**
   * Autonomous Database with the Oracle APEX Application Development workload
   * type.
   */
  public const DB_WORKLOAD_APEX = 'APEX';
  /**
   * Unspecified
   */
  public const LICENSE_TYPE_LICENSE_TYPE_UNSPECIFIED = 'LICENSE_TYPE_UNSPECIFIED';
  /**
   * License included part of offer
   */
  public const LICENSE_TYPE_LICENSE_INCLUDED = 'LICENSE_INCLUDED';
  /**
   * Bring your own license
   */
  public const LICENSE_TYPE_BRING_YOUR_OWN_LICENSE = 'BRING_YOUR_OWN_LICENSE';
  /**
   * Default unspecified value.
   */
  public const LOCAL_DISASTER_RECOVERY_TYPE_LOCAL_DISASTER_RECOVERY_TYPE_UNSPECIFIED = 'LOCAL_DISASTER_RECOVERY_TYPE_UNSPECIFIED';
  /**
   * Autonomous Data Guard recovery.
   */
  public const LOCAL_DISASTER_RECOVERY_TYPE_ADG = 'ADG';
  /**
   * Backup based recovery.
   */
  public const LOCAL_DISASTER_RECOVERY_TYPE_BACKUP_BASED = 'BACKUP_BASED';
  /**
   * Default unspecified value.
   */
  public const MAINTENANCE_SCHEDULE_TYPE_MAINTENANCE_SCHEDULE_TYPE_UNSPECIFIED = 'MAINTENANCE_SCHEDULE_TYPE_UNSPECIFIED';
  /**
   * An EARLY maintenance schedule patches the database before the regular
   * scheduled maintenance.
   */
  public const MAINTENANCE_SCHEDULE_TYPE_EARLY = 'EARLY';
  /**
   * A REGULAR maintenance schedule follows the normal maintenance cycle.
   */
  public const MAINTENANCE_SCHEDULE_TYPE_REGULAR = 'REGULAR';
  /**
   * Default unspecified value.
   */
  public const OPEN_MODE_OPEN_MODE_UNSPECIFIED = 'OPEN_MODE_UNSPECIFIED';
  /**
   * Read Only Mode
   */
  public const OPEN_MODE_READ_ONLY = 'READ_ONLY';
  /**
   * Read Write Mode
   */
  public const OPEN_MODE_READ_WRITE = 'READ_WRITE';
  /**
   * Default unspecified value.
   */
  public const OPERATIONS_INSIGHTS_STATE_OPERATIONS_INSIGHTS_STATE_UNSPECIFIED = 'OPERATIONS_INSIGHTS_STATE_UNSPECIFIED';
  /**
   * Enabling status for operation insights.
   */
  public const OPERATIONS_INSIGHTS_STATE_ENABLING = 'ENABLING';
  /**
   * Enabled status for operation insights.
   */
  public const OPERATIONS_INSIGHTS_STATE_ENABLED = 'ENABLED';
  /**
   * Disabling status for operation insights.
   */
  public const OPERATIONS_INSIGHTS_STATE_DISABLING = 'DISABLING';
  /**
   * Not Enabled status for operation insights.
   */
  public const OPERATIONS_INSIGHTS_STATE_NOT_ENABLED = 'NOT_ENABLED';
  /**
   * Failed enabling status for operation insights.
   */
  public const OPERATIONS_INSIGHTS_STATE_FAILED_ENABLING = 'FAILED_ENABLING';
  /**
   * Failed disabling status for operation insights.
   */
  public const OPERATIONS_INSIGHTS_STATE_FAILED_DISABLING = 'FAILED_DISABLING';
  /**
   * Default unspecified value.
   */
  public const PERMISSION_LEVEL_PERMISSION_LEVEL_UNSPECIFIED = 'PERMISSION_LEVEL_UNSPECIFIED';
  /**
   * Restricted mode allows access only by admin users.
   */
  public const PERMISSION_LEVEL_RESTRICTED = 'RESTRICTED';
  /**
   * Normal access.
   */
  public const PERMISSION_LEVEL_UNRESTRICTED = 'UNRESTRICTED';
  /**
   * The default unspecified value.
   */
  public const REFRESHABLE_MODE_REFRESHABLE_MODE_UNSPECIFIED = 'REFRESHABLE_MODE_UNSPECIFIED';
  /**
   * AUTOMATIC indicates that the cloned database is automatically refreshed
   * with data from the source Autonomous Database.
   */
  public const REFRESHABLE_MODE_AUTOMATIC = 'AUTOMATIC';
  /**
   * MANUAL indicates that the cloned database is manually refreshed with data
   * from the source Autonomous Database.
   */
  public const REFRESHABLE_MODE_MANUAL = 'MANUAL';
  /**
   * Default unspecified value.
   */
  public const REFRESHABLE_STATE_REFRESHABLE_STATE_UNSPECIFIED = 'REFRESHABLE_STATE_UNSPECIFIED';
  /**
   * Refreshing
   */
  public const REFRESHABLE_STATE_REFRESHING = 'REFRESHING';
  /**
   * Not refreshed
   */
  public const REFRESHABLE_STATE_NOT_REFRESHING = 'NOT_REFRESHING';
  /**
   * Default unspecified value.
   */
  public const ROLE_ROLE_UNSPECIFIED = 'ROLE_UNSPECIFIED';
  /**
   * Primary role
   */
  public const ROLE_PRIMARY = 'PRIMARY';
  /**
   * Standby role
   */
  public const ROLE_STANDBY = 'STANDBY';
  /**
   * Disabled standby role
   */
  public const ROLE_DISABLED_STANDBY = 'DISABLED_STANDBY';
  /**
   * Backup copy role
   */
  public const ROLE_BACKUP_COPY = 'BACKUP_COPY';
  /**
   * Snapshot standby role
   */
  public const ROLE_SNAPSHOT_STANDBY = 'SNAPSHOT_STANDBY';
  /**
   * Default unspecified value.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Indicates that the Autonomous Database is in provisioning state.
   */
  public const STATE_PROVISIONING = 'PROVISIONING';
  /**
   * Indicates that the Autonomous Database is in available state.
   */
  public const STATE_AVAILABLE = 'AVAILABLE';
  /**
   * Indicates that the Autonomous Database is in stopping state.
   */
  public const STATE_STOPPING = 'STOPPING';
  /**
   * Indicates that the Autonomous Database is in stopped state.
   */
  public const STATE_STOPPED = 'STOPPED';
  /**
   * Indicates that the Autonomous Database is in starting state.
   */
  public const STATE_STARTING = 'STARTING';
  /**
   * Indicates that the Autonomous Database is in terminating state.
   */
  public const STATE_TERMINATING = 'TERMINATING';
  /**
   * Indicates that the Autonomous Database is in terminated state.
   */
  public const STATE_TERMINATED = 'TERMINATED';
  /**
   * Indicates that the Autonomous Database is in unavailable state.
   */
  public const STATE_UNAVAILABLE = 'UNAVAILABLE';
  /**
   * Indicates that the Autonomous Database restore is in progress.
   */
  public const STATE_RESTORE_IN_PROGRESS = 'RESTORE_IN_PROGRESS';
  /**
   * Indicates that the Autonomous Database failed to restore.
   */
  public const STATE_RESTORE_FAILED = 'RESTORE_FAILED';
  /**
   * Indicates that the Autonomous Database backup is in progress.
   */
  public const STATE_BACKUP_IN_PROGRESS = 'BACKUP_IN_PROGRESS';
  /**
   * Indicates that the Autonomous Database scale is in progress.
   */
  public const STATE_SCALE_IN_PROGRESS = 'SCALE_IN_PROGRESS';
  /**
   * Indicates that the Autonomous Database is available but needs attention
   * state.
   */
  public const STATE_AVAILABLE_NEEDS_ATTENTION = 'AVAILABLE_NEEDS_ATTENTION';
  /**
   * Indicates that the Autonomous Database is in updating state.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * Indicates that the Autonomous Database's maintenance is in progress state.
   */
  public const STATE_MAINTENANCE_IN_PROGRESS = 'MAINTENANCE_IN_PROGRESS';
  /**
   * Indicates that the Autonomous Database is in restarting state.
   */
  public const STATE_RESTARTING = 'RESTARTING';
  /**
   * Indicates that the Autonomous Database is in recreating state.
   */
  public const STATE_RECREATING = 'RECREATING';
  /**
   * Indicates that the Autonomous Database's role change is in progress state.
   */
  public const STATE_ROLE_CHANGE_IN_PROGRESS = 'ROLE_CHANGE_IN_PROGRESS';
  /**
   * Indicates that the Autonomous Database is in upgrading state.
   */
  public const STATE_UPGRADING = 'UPGRADING';
  /**
   * Indicates that the Autonomous Database is in inaccessible state.
   */
  public const STATE_INACCESSIBLE = 'INACCESSIBLE';
  /**
   * Indicates that the Autonomous Database is in standby state.
   */
  public const STATE_STANDBY = 'STANDBY';
  protected $collection_key = 'supportedCloneRegions';
  /**
   * Output only. The amount of storage currently being used for user and system
   * data, in terabytes.
   *
   * @var 
   */
  public $actualUsedDataStorageSizeTb;
  /**
   * Output only. The amount of storage currently allocated for the database
   * tables and billed for, rounded up in terabytes.
   *
   * @var 
   */
  public $allocatedStorageSizeTb;
  /**
   * Optional. The list of allowlisted IP addresses for the Autonomous Database.
   *
   * @var string[]
   */
  public $allowlistedIps;
  protected $apexDetailsType = AutonomousDatabaseApex::class;
  protected $apexDetailsDataType = '';
  /**
   * Output only. This field indicates the status of Data Guard and Access
   * control for the Autonomous Database. The field's value is null if Data
   * Guard is disabled or Access Control is disabled. The field's value is TRUE
   * if both Data Guard and Access Control are enabled, and the Autonomous
   * Database is using primary IP access control list (ACL) for standby. The
   * field's value is FALSE if both Data Guard and Access Control are enabled,
   * and the Autonomous Database is using a different IP access control list
   * (ACL) for standby compared to primary.
   *
   * @var bool
   */
  public $arePrimaryAllowlistedIpsUsed;
  /**
   * Output only. The Autonomous Container Database OCID.
   *
   * @var string
   */
  public $autonomousContainerDatabaseId;
  /**
   * Output only. The list of available Oracle Database upgrade versions for an
   * Autonomous Database.
   *
   * @var string[]
   */
  public $availableUpgradeVersions;
  /**
   * Optional. The retention period for the Autonomous Database. This field is
   * specified in days, can range from 1 day to 60 days, and has a default value
   * of 60 days.
   *
   * @var int
   */
  public $backupRetentionPeriodDays;
  /**
   * Optional. The character set for the Autonomous Database. The default is
   * AL32UTF8.
   *
   * @var string
   */
  public $characterSet;
  /**
   * Optional. The number of compute servers for the Autonomous Database.
   *
   * @var float
   */
  public $computeCount;
  protected $connectionStringsType = AutonomousDatabaseConnectionStrings::class;
  protected $connectionStringsDataType = '';
  protected $connectionUrlsType = AutonomousDatabaseConnectionUrls::class;
  protected $connectionUrlsDataType = '';
  /**
   * Optional. The number of CPU cores to be made available to the database.
   *
   * @var int
   */
  public $cpuCoreCount;
  protected $customerContactsType = CustomerContact::class;
  protected $customerContactsDataType = 'array';
  /**
   * Output only. The date and time the Autonomous Data Guard role was changed
   * for the standby Autonomous Database.
   *
   * @var string
   */
  public $dataGuardRoleChangedTime;
  /**
   * Output only. The current state of the Data Safe registration for the
   * Autonomous Database.
   *
   * @var string
   */
  public $dataSafeState;
  /**
   * Optional. The size of the data stored in the database, in gigabytes.
   *
   * @var int
   */
  public $dataStorageSizeGb;
  /**
   * Optional. The size of the data stored in the database, in terabytes.
   *
   * @var int
   */
  public $dataStorageSizeTb;
  /**
   * Output only. The current state of database management for the Autonomous
   * Database.
   *
   * @var string
   */
  public $databaseManagementState;
  /**
   * Optional. The edition of the Autonomous Databases.
   *
   * @var string
   */
  public $dbEdition;
  /**
   * Optional. The Oracle Database version for the Autonomous Database.
   *
   * @var string
   */
  public $dbVersion;
  /**
   * Required. The workload type of the Autonomous Database.
   *
   * @var string
   */
  public $dbWorkload;
  /**
   * Output only. The date and time the Disaster Recovery role was changed for
   * the standby Autonomous Database.
   *
   * @var string
   */
  public $disasterRecoveryRoleChangedTime;
  protected $encryptionKeyType = EncryptionKey::class;
  protected $encryptionKeyDataType = '';
  protected $encryptionKeyHistoryEntriesType = EncryptionKeyHistoryEntry::class;
  protected $encryptionKeyHistoryEntriesDataType = 'array';
  /**
   * Output only. This field indicates the number of seconds of data loss during
   * a Data Guard failover.
   *
   * @var string
   */
  public $failedDataRecoveryDuration;
  /**
   * Optional. This field indicates if auto scaling is enabled for the
   * Autonomous Database CPU core count.
   *
   * @var bool
   */
  public $isAutoScalingEnabled;
  /**
   * Output only. This field indicates whether the Autonomous Database has local
   * (in-region) Data Guard enabled.
   *
   * @var bool
   */
  public $isLocalDataGuardEnabled;
  /**
   * Optional. This field indicates if auto scaling is enabled for the
   * Autonomous Database storage.
   *
   * @var bool
   */
  public $isStorageAutoScalingEnabled;
  /**
   * Required. The license type used for the Autonomous Database.
   *
   * @var string
   */
  public $licenseType;
  /**
   * Output only. The details of the current lifestyle state of the Autonomous
   * Database.
   *
   * @var string
   */
  public $lifecycleDetails;
  /**
   * Output only. This field indicates the maximum data loss limit for an
   * Autonomous Database, in seconds.
   *
   * @var int
   */
  public $localAdgAutoFailoverMaxDataLossLimit;
  /**
   * Output only. This field indicates the local disaster recovery (DR) type of
   * an Autonomous Database.
   *
   * @var string
   */
  public $localDisasterRecoveryType;
  protected $localStandbyDbType = AutonomousDatabaseStandbySummary::class;
  protected $localStandbyDbDataType = '';
  /**
   * Output only. The date and time when maintenance will begin.
   *
   * @var string
   */
  public $maintenanceBeginTime;
  /**
   * Output only. The date and time when maintenance will end.
   *
   * @var string
   */
  public $maintenanceEndTime;
  /**
   * Optional. The maintenance schedule of the Autonomous Database.
   *
   * @var string
   */
  public $maintenanceScheduleType;
  /**
   * Output only. The amount of memory enabled per ECPU, in gigabytes.
   *
   * @var int
   */
  public $memoryPerOracleComputeUnitGbs;
  /**
   * Output only. The memory assigned to in-memory tables in an Autonomous
   * Database.
   *
   * @var int
   */
  public $memoryTableGbs;
  /**
   * Optional. This field specifies if the Autonomous Database requires mTLS
   * connections.
   *
   * @var bool
   */
  public $mtlsConnectionRequired;
  /**
   * Optional. The national character set for the Autonomous Database. The
   * default is AL16UTF16.
   *
   * @var string
   */
  public $nCharacterSet;
  /**
   * Output only. The long term backup schedule of the Autonomous Database.
   *
   * @var string
   */
  public $nextLongTermBackupTime;
  /**
   * Output only. The Oracle Cloud Infrastructure link for the Autonomous
   * Database.
   *
   * @var string
   */
  public $ociUrl;
  /**
   * Output only. OCID of the Autonomous Database. https://docs.oracle.com/en-
   * us/iaas/Content/General/Concepts/identifiers.htm#Oracle
   *
   * @var string
   */
  public $ocid;
  /**
   * Output only. This field indicates the current mode of the Autonomous
   * Database.
   *
   * @var string
   */
  public $openMode;
  /**
   * Output only. This field indicates the state of Operations Insights for the
   * Autonomous Database.
   *
   * @var string
   */
  public $operationsInsightsState;
  /**
   * Output only. The list of OCIDs of standby databases located in Autonomous
   * Data Guard remote regions that are associated with the source database.
   *
   * @var string[]
   */
  public $peerDbIds;
  /**
   * Output only. The permission level of the Autonomous Database.
   *
   * @var string
   */
  public $permissionLevel;
  /**
   * Output only. The private endpoint for the Autonomous Database.
   *
   * @var string
   */
  public $privateEndpoint;
  /**
   * Optional. The private endpoint IP address for the Autonomous Database.
   *
   * @var string
   */
  public $privateEndpointIp;
  /**
   * Optional. The private endpoint label for the Autonomous Database.
   *
   * @var string
   */
  public $privateEndpointLabel;
  /**
   * Output only. The refresh mode of the cloned Autonomous Database.
   *
   * @var string
   */
  public $refreshableMode;
  /**
   * Output only. The refresh State of the clone.
   *
   * @var string
   */
  public $refreshableState;
  /**
   * Output only. The Data Guard role of the Autonomous Database.
   *
   * @var string
   */
  public $role;
  protected $scheduledOperationDetailsType = ScheduledOperationDetails::class;
  protected $scheduledOperationDetailsDataType = 'array';
  /**
   * Optional. The ID of the Oracle Cloud Infrastructure vault secret.
   *
   * @var string
   */
  public $secretId;
  /**
   * Output only. An Oracle-managed Google Cloud service account on which
   * customers can grant roles to access resources in the customer project.
   *
   * @var string
   */
  public $serviceAgentEmail;
  /**
   * Output only. The SQL Web Developer URL for the Autonomous Database.
   *
   * @var string
   */
  public $sqlWebDeveloperUrl;
  /**
   * Output only. The current lifecycle state of the Autonomous Database.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The list of available regions that can be used to create a
   * clone for the Autonomous Database.
   *
   * @var string[]
   */
  public $supportedCloneRegions;
  /**
   * Output only. The storage space used by automatic backups of Autonomous
   * Database, in gigabytes.
   *
   * @var float
   */
  public $totalAutoBackupStorageSizeGbs;
  /**
   * Output only. The storage space used by Autonomous Database, in gigabytes.
   *
   * @var int
   */
  public $usedDataStorageSizeTbs;
  /**
   * Optional. The ID of the Oracle Cloud Infrastructure vault.
   *
   * @var string
   */
  public $vaultId;

  public function setActualUsedDataStorageSizeTb($actualUsedDataStorageSizeTb)
  {
    $this->actualUsedDataStorageSizeTb = $actualUsedDataStorageSizeTb;
  }
  public function getActualUsedDataStorageSizeTb()
  {
    return $this->actualUsedDataStorageSizeTb;
  }
  public function setAllocatedStorageSizeTb($allocatedStorageSizeTb)
  {
    $this->allocatedStorageSizeTb = $allocatedStorageSizeTb;
  }
  public function getAllocatedStorageSizeTb()
  {
    return $this->allocatedStorageSizeTb;
  }
  /**
   * Optional. The list of allowlisted IP addresses for the Autonomous Database.
   *
   * @param string[] $allowlistedIps
   */
  public function setAllowlistedIps($allowlistedIps)
  {
    $this->allowlistedIps = $allowlistedIps;
  }
  /**
   * @return string[]
   */
  public function getAllowlistedIps()
  {
    return $this->allowlistedIps;
  }
  /**
   * Output only. The details for the Oracle APEX Application Development.
   *
   * @param AutonomousDatabaseApex $apexDetails
   */
  public function setApexDetails(AutonomousDatabaseApex $apexDetails)
  {
    $this->apexDetails = $apexDetails;
  }
  /**
   * @return AutonomousDatabaseApex
   */
  public function getApexDetails()
  {
    return $this->apexDetails;
  }
  /**
   * Output only. This field indicates the status of Data Guard and Access
   * control for the Autonomous Database. The field's value is null if Data
   * Guard is disabled or Access Control is disabled. The field's value is TRUE
   * if both Data Guard and Access Control are enabled, and the Autonomous
   * Database is using primary IP access control list (ACL) for standby. The
   * field's value is FALSE if both Data Guard and Access Control are enabled,
   * and the Autonomous Database is using a different IP access control list
   * (ACL) for standby compared to primary.
   *
   * @param bool $arePrimaryAllowlistedIpsUsed
   */
  public function setArePrimaryAllowlistedIpsUsed($arePrimaryAllowlistedIpsUsed)
  {
    $this->arePrimaryAllowlistedIpsUsed = $arePrimaryAllowlistedIpsUsed;
  }
  /**
   * @return bool
   */
  public function getArePrimaryAllowlistedIpsUsed()
  {
    return $this->arePrimaryAllowlistedIpsUsed;
  }
  /**
   * Output only. The Autonomous Container Database OCID.
   *
   * @param string $autonomousContainerDatabaseId
   */
  public function setAutonomousContainerDatabaseId($autonomousContainerDatabaseId)
  {
    $this->autonomousContainerDatabaseId = $autonomousContainerDatabaseId;
  }
  /**
   * @return string
   */
  public function getAutonomousContainerDatabaseId()
  {
    return $this->autonomousContainerDatabaseId;
  }
  /**
   * Output only. The list of available Oracle Database upgrade versions for an
   * Autonomous Database.
   *
   * @param string[] $availableUpgradeVersions
   */
  public function setAvailableUpgradeVersions($availableUpgradeVersions)
  {
    $this->availableUpgradeVersions = $availableUpgradeVersions;
  }
  /**
   * @return string[]
   */
  public function getAvailableUpgradeVersions()
  {
    return $this->availableUpgradeVersions;
  }
  /**
   * Optional. The retention period for the Autonomous Database. This field is
   * specified in days, can range from 1 day to 60 days, and has a default value
   * of 60 days.
   *
   * @param int $backupRetentionPeriodDays
   */
  public function setBackupRetentionPeriodDays($backupRetentionPeriodDays)
  {
    $this->backupRetentionPeriodDays = $backupRetentionPeriodDays;
  }
  /**
   * @return int
   */
  public function getBackupRetentionPeriodDays()
  {
    return $this->backupRetentionPeriodDays;
  }
  /**
   * Optional. The character set for the Autonomous Database. The default is
   * AL32UTF8.
   *
   * @param string $characterSet
   */
  public function setCharacterSet($characterSet)
  {
    $this->characterSet = $characterSet;
  }
  /**
   * @return string
   */
  public function getCharacterSet()
  {
    return $this->characterSet;
  }
  /**
   * Optional. The number of compute servers for the Autonomous Database.
   *
   * @param float $computeCount
   */
  public function setComputeCount($computeCount)
  {
    $this->computeCount = $computeCount;
  }
  /**
   * @return float
   */
  public function getComputeCount()
  {
    return $this->computeCount;
  }
  /**
   * Output only. The connection strings used to connect to an Autonomous
   * Database.
   *
   * @param AutonomousDatabaseConnectionStrings $connectionStrings
   */
  public function setConnectionStrings(AutonomousDatabaseConnectionStrings $connectionStrings)
  {
    $this->connectionStrings = $connectionStrings;
  }
  /**
   * @return AutonomousDatabaseConnectionStrings
   */
  public function getConnectionStrings()
  {
    return $this->connectionStrings;
  }
  /**
   * Output only. The Oracle Connection URLs for an Autonomous Database.
   *
   * @param AutonomousDatabaseConnectionUrls $connectionUrls
   */
  public function setConnectionUrls(AutonomousDatabaseConnectionUrls $connectionUrls)
  {
    $this->connectionUrls = $connectionUrls;
  }
  /**
   * @return AutonomousDatabaseConnectionUrls
   */
  public function getConnectionUrls()
  {
    return $this->connectionUrls;
  }
  /**
   * Optional. The number of CPU cores to be made available to the database.
   *
   * @param int $cpuCoreCount
   */
  public function setCpuCoreCount($cpuCoreCount)
  {
    $this->cpuCoreCount = $cpuCoreCount;
  }
  /**
   * @return int
   */
  public function getCpuCoreCount()
  {
    return $this->cpuCoreCount;
  }
  /**
   * Optional. The list of customer contacts.
   *
   * @param CustomerContact[] $customerContacts
   */
  public function setCustomerContacts($customerContacts)
  {
    $this->customerContacts = $customerContacts;
  }
  /**
   * @return CustomerContact[]
   */
  public function getCustomerContacts()
  {
    return $this->customerContacts;
  }
  /**
   * Output only. The date and time the Autonomous Data Guard role was changed
   * for the standby Autonomous Database.
   *
   * @param string $dataGuardRoleChangedTime
   */
  public function setDataGuardRoleChangedTime($dataGuardRoleChangedTime)
  {
    $this->dataGuardRoleChangedTime = $dataGuardRoleChangedTime;
  }
  /**
   * @return string
   */
  public function getDataGuardRoleChangedTime()
  {
    return $this->dataGuardRoleChangedTime;
  }
  /**
   * Output only. The current state of the Data Safe registration for the
   * Autonomous Database.
   *
   * Accepted values: DATA_SAFE_STATE_UNSPECIFIED, REGISTERING, REGISTERED,
   * DEREGISTERING, NOT_REGISTERED, FAILED
   *
   * @param self::DATA_SAFE_STATE_* $dataSafeState
   */
  public function setDataSafeState($dataSafeState)
  {
    $this->dataSafeState = $dataSafeState;
  }
  /**
   * @return self::DATA_SAFE_STATE_*
   */
  public function getDataSafeState()
  {
    return $this->dataSafeState;
  }
  /**
   * Optional. The size of the data stored in the database, in gigabytes.
   *
   * @param int $dataStorageSizeGb
   */
  public function setDataStorageSizeGb($dataStorageSizeGb)
  {
    $this->dataStorageSizeGb = $dataStorageSizeGb;
  }
  /**
   * @return int
   */
  public function getDataStorageSizeGb()
  {
    return $this->dataStorageSizeGb;
  }
  /**
   * Optional. The size of the data stored in the database, in terabytes.
   *
   * @param int $dataStorageSizeTb
   */
  public function setDataStorageSizeTb($dataStorageSizeTb)
  {
    $this->dataStorageSizeTb = $dataStorageSizeTb;
  }
  /**
   * @return int
   */
  public function getDataStorageSizeTb()
  {
    return $this->dataStorageSizeTb;
  }
  /**
   * Output only. The current state of database management for the Autonomous
   * Database.
   *
   * Accepted values: DATABASE_MANAGEMENT_STATE_UNSPECIFIED, ENABLING, ENABLED,
   * DISABLING, NOT_ENABLED, FAILED_ENABLING, FAILED_DISABLING
   *
   * @param self::DATABASE_MANAGEMENT_STATE_* $databaseManagementState
   */
  public function setDatabaseManagementState($databaseManagementState)
  {
    $this->databaseManagementState = $databaseManagementState;
  }
  /**
   * @return self::DATABASE_MANAGEMENT_STATE_*
   */
  public function getDatabaseManagementState()
  {
    return $this->databaseManagementState;
  }
  /**
   * Optional. The edition of the Autonomous Databases.
   *
   * Accepted values: DATABASE_EDITION_UNSPECIFIED, STANDARD_EDITION,
   * ENTERPRISE_EDITION
   *
   * @param self::DB_EDITION_* $dbEdition
   */
  public function setDbEdition($dbEdition)
  {
    $this->dbEdition = $dbEdition;
  }
  /**
   * @return self::DB_EDITION_*
   */
  public function getDbEdition()
  {
    return $this->dbEdition;
  }
  /**
   * Optional. The Oracle Database version for the Autonomous Database.
   *
   * @param string $dbVersion
   */
  public function setDbVersion($dbVersion)
  {
    $this->dbVersion = $dbVersion;
  }
  /**
   * @return string
   */
  public function getDbVersion()
  {
    return $this->dbVersion;
  }
  /**
   * Required. The workload type of the Autonomous Database.
   *
   * Accepted values: DB_WORKLOAD_UNSPECIFIED, OLTP, DW, AJD, APEX
   *
   * @param self::DB_WORKLOAD_* $dbWorkload
   */
  public function setDbWorkload($dbWorkload)
  {
    $this->dbWorkload = $dbWorkload;
  }
  /**
   * @return self::DB_WORKLOAD_*
   */
  public function getDbWorkload()
  {
    return $this->dbWorkload;
  }
  /**
   * Output only. The date and time the Disaster Recovery role was changed for
   * the standby Autonomous Database.
   *
   * @param string $disasterRecoveryRoleChangedTime
   */
  public function setDisasterRecoveryRoleChangedTime($disasterRecoveryRoleChangedTime)
  {
    $this->disasterRecoveryRoleChangedTime = $disasterRecoveryRoleChangedTime;
  }
  /**
   * @return string
   */
  public function getDisasterRecoveryRoleChangedTime()
  {
    return $this->disasterRecoveryRoleChangedTime;
  }
  /**
   * Optional. The encryption key used to encrypt the Autonomous Database.
   * Updating this field will add a new entry in the
   * `encryption_key_history_entries` field with the former version.
   *
   * @param EncryptionKey $encryptionKey
   */
  public function setEncryptionKey(EncryptionKey $encryptionKey)
  {
    $this->encryptionKey = $encryptionKey;
  }
  /**
   * @return EncryptionKey
   */
  public function getEncryptionKey()
  {
    return $this->encryptionKey;
  }
  /**
   * Output only. The history of the encryption keys used to encrypt the
   * Autonomous Database.
   *
   * @param EncryptionKeyHistoryEntry[] $encryptionKeyHistoryEntries
   */
  public function setEncryptionKeyHistoryEntries($encryptionKeyHistoryEntries)
  {
    $this->encryptionKeyHistoryEntries = $encryptionKeyHistoryEntries;
  }
  /**
   * @return EncryptionKeyHistoryEntry[]
   */
  public function getEncryptionKeyHistoryEntries()
  {
    return $this->encryptionKeyHistoryEntries;
  }
  /**
   * Output only. This field indicates the number of seconds of data loss during
   * a Data Guard failover.
   *
   * @param string $failedDataRecoveryDuration
   */
  public function setFailedDataRecoveryDuration($failedDataRecoveryDuration)
  {
    $this->failedDataRecoveryDuration = $failedDataRecoveryDuration;
  }
  /**
   * @return string
   */
  public function getFailedDataRecoveryDuration()
  {
    return $this->failedDataRecoveryDuration;
  }
  /**
   * Optional. This field indicates if auto scaling is enabled for the
   * Autonomous Database CPU core count.
   *
   * @param bool $isAutoScalingEnabled
   */
  public function setIsAutoScalingEnabled($isAutoScalingEnabled)
  {
    $this->isAutoScalingEnabled = $isAutoScalingEnabled;
  }
  /**
   * @return bool
   */
  public function getIsAutoScalingEnabled()
  {
    return $this->isAutoScalingEnabled;
  }
  /**
   * Output only. This field indicates whether the Autonomous Database has local
   * (in-region) Data Guard enabled.
   *
   * @param bool $isLocalDataGuardEnabled
   */
  public function setIsLocalDataGuardEnabled($isLocalDataGuardEnabled)
  {
    $this->isLocalDataGuardEnabled = $isLocalDataGuardEnabled;
  }
  /**
   * @return bool
   */
  public function getIsLocalDataGuardEnabled()
  {
    return $this->isLocalDataGuardEnabled;
  }
  /**
   * Optional. This field indicates if auto scaling is enabled for the
   * Autonomous Database storage.
   *
   * @param bool $isStorageAutoScalingEnabled
   */
  public function setIsStorageAutoScalingEnabled($isStorageAutoScalingEnabled)
  {
    $this->isStorageAutoScalingEnabled = $isStorageAutoScalingEnabled;
  }
  /**
   * @return bool
   */
  public function getIsStorageAutoScalingEnabled()
  {
    return $this->isStorageAutoScalingEnabled;
  }
  /**
   * Required. The license type used for the Autonomous Database.
   *
   * Accepted values: LICENSE_TYPE_UNSPECIFIED, LICENSE_INCLUDED,
   * BRING_YOUR_OWN_LICENSE
   *
   * @param self::LICENSE_TYPE_* $licenseType
   */
  public function setLicenseType($licenseType)
  {
    $this->licenseType = $licenseType;
  }
  /**
   * @return self::LICENSE_TYPE_*
   */
  public function getLicenseType()
  {
    return $this->licenseType;
  }
  /**
   * Output only. The details of the current lifestyle state of the Autonomous
   * Database.
   *
   * @param string $lifecycleDetails
   */
  public function setLifecycleDetails($lifecycleDetails)
  {
    $this->lifecycleDetails = $lifecycleDetails;
  }
  /**
   * @return string
   */
  public function getLifecycleDetails()
  {
    return $this->lifecycleDetails;
  }
  /**
   * Output only. This field indicates the maximum data loss limit for an
   * Autonomous Database, in seconds.
   *
   * @param int $localAdgAutoFailoverMaxDataLossLimit
   */
  public function setLocalAdgAutoFailoverMaxDataLossLimit($localAdgAutoFailoverMaxDataLossLimit)
  {
    $this->localAdgAutoFailoverMaxDataLossLimit = $localAdgAutoFailoverMaxDataLossLimit;
  }
  /**
   * @return int
   */
  public function getLocalAdgAutoFailoverMaxDataLossLimit()
  {
    return $this->localAdgAutoFailoverMaxDataLossLimit;
  }
  /**
   * Output only. This field indicates the local disaster recovery (DR) type of
   * an Autonomous Database.
   *
   * Accepted values: LOCAL_DISASTER_RECOVERY_TYPE_UNSPECIFIED, ADG,
   * BACKUP_BASED
   *
   * @param self::LOCAL_DISASTER_RECOVERY_TYPE_* $localDisasterRecoveryType
   */
  public function setLocalDisasterRecoveryType($localDisasterRecoveryType)
  {
    $this->localDisasterRecoveryType = $localDisasterRecoveryType;
  }
  /**
   * @return self::LOCAL_DISASTER_RECOVERY_TYPE_*
   */
  public function getLocalDisasterRecoveryType()
  {
    return $this->localDisasterRecoveryType;
  }
  /**
   * Output only. The details of the Autonomous Data Guard standby database.
   *
   * @param AutonomousDatabaseStandbySummary $localStandbyDb
   */
  public function setLocalStandbyDb(AutonomousDatabaseStandbySummary $localStandbyDb)
  {
    $this->localStandbyDb = $localStandbyDb;
  }
  /**
   * @return AutonomousDatabaseStandbySummary
   */
  public function getLocalStandbyDb()
  {
    return $this->localStandbyDb;
  }
  /**
   * Output only. The date and time when maintenance will begin.
   *
   * @param string $maintenanceBeginTime
   */
  public function setMaintenanceBeginTime($maintenanceBeginTime)
  {
    $this->maintenanceBeginTime = $maintenanceBeginTime;
  }
  /**
   * @return string
   */
  public function getMaintenanceBeginTime()
  {
    return $this->maintenanceBeginTime;
  }
  /**
   * Output only. The date and time when maintenance will end.
   *
   * @param string $maintenanceEndTime
   */
  public function setMaintenanceEndTime($maintenanceEndTime)
  {
    $this->maintenanceEndTime = $maintenanceEndTime;
  }
  /**
   * @return string
   */
  public function getMaintenanceEndTime()
  {
    return $this->maintenanceEndTime;
  }
  /**
   * Optional. The maintenance schedule of the Autonomous Database.
   *
   * Accepted values: MAINTENANCE_SCHEDULE_TYPE_UNSPECIFIED, EARLY, REGULAR
   *
   * @param self::MAINTENANCE_SCHEDULE_TYPE_* $maintenanceScheduleType
   */
  public function setMaintenanceScheduleType($maintenanceScheduleType)
  {
    $this->maintenanceScheduleType = $maintenanceScheduleType;
  }
  /**
   * @return self::MAINTENANCE_SCHEDULE_TYPE_*
   */
  public function getMaintenanceScheduleType()
  {
    return $this->maintenanceScheduleType;
  }
  /**
   * Output only. The amount of memory enabled per ECPU, in gigabytes.
   *
   * @param int $memoryPerOracleComputeUnitGbs
   */
  public function setMemoryPerOracleComputeUnitGbs($memoryPerOracleComputeUnitGbs)
  {
    $this->memoryPerOracleComputeUnitGbs = $memoryPerOracleComputeUnitGbs;
  }
  /**
   * @return int
   */
  public function getMemoryPerOracleComputeUnitGbs()
  {
    return $this->memoryPerOracleComputeUnitGbs;
  }
  /**
   * Output only. The memory assigned to in-memory tables in an Autonomous
   * Database.
   *
   * @param int $memoryTableGbs
   */
  public function setMemoryTableGbs($memoryTableGbs)
  {
    $this->memoryTableGbs = $memoryTableGbs;
  }
  /**
   * @return int
   */
  public function getMemoryTableGbs()
  {
    return $this->memoryTableGbs;
  }
  /**
   * Optional. This field specifies if the Autonomous Database requires mTLS
   * connections.
   *
   * @param bool $mtlsConnectionRequired
   */
  public function setMtlsConnectionRequired($mtlsConnectionRequired)
  {
    $this->mtlsConnectionRequired = $mtlsConnectionRequired;
  }
  /**
   * @return bool
   */
  public function getMtlsConnectionRequired()
  {
    return $this->mtlsConnectionRequired;
  }
  /**
   * Optional. The national character set for the Autonomous Database. The
   * default is AL16UTF16.
   *
   * @param string $nCharacterSet
   */
  public function setNCharacterSet($nCharacterSet)
  {
    $this->nCharacterSet = $nCharacterSet;
  }
  /**
   * @return string
   */
  public function getNCharacterSet()
  {
    return $this->nCharacterSet;
  }
  /**
   * Output only. The long term backup schedule of the Autonomous Database.
   *
   * @param string $nextLongTermBackupTime
   */
  public function setNextLongTermBackupTime($nextLongTermBackupTime)
  {
    $this->nextLongTermBackupTime = $nextLongTermBackupTime;
  }
  /**
   * @return string
   */
  public function getNextLongTermBackupTime()
  {
    return $this->nextLongTermBackupTime;
  }
  /**
   * Output only. The Oracle Cloud Infrastructure link for the Autonomous
   * Database.
   *
   * @param string $ociUrl
   */
  public function setOciUrl($ociUrl)
  {
    $this->ociUrl = $ociUrl;
  }
  /**
   * @return string
   */
  public function getOciUrl()
  {
    return $this->ociUrl;
  }
  /**
   * Output only. OCID of the Autonomous Database. https://docs.oracle.com/en-
   * us/iaas/Content/General/Concepts/identifiers.htm#Oracle
   *
   * @param string $ocid
   */
  public function setOcid($ocid)
  {
    $this->ocid = $ocid;
  }
  /**
   * @return string
   */
  public function getOcid()
  {
    return $this->ocid;
  }
  /**
   * Output only. This field indicates the current mode of the Autonomous
   * Database.
   *
   * Accepted values: OPEN_MODE_UNSPECIFIED, READ_ONLY, READ_WRITE
   *
   * @param self::OPEN_MODE_* $openMode
   */
  public function setOpenMode($openMode)
  {
    $this->openMode = $openMode;
  }
  /**
   * @return self::OPEN_MODE_*
   */
  public function getOpenMode()
  {
    return $this->openMode;
  }
  /**
   * Output only. This field indicates the state of Operations Insights for the
   * Autonomous Database.
   *
   * Accepted values: OPERATIONS_INSIGHTS_STATE_UNSPECIFIED, ENABLING, ENABLED,
   * DISABLING, NOT_ENABLED, FAILED_ENABLING, FAILED_DISABLING
   *
   * @param self::OPERATIONS_INSIGHTS_STATE_* $operationsInsightsState
   */
  public function setOperationsInsightsState($operationsInsightsState)
  {
    $this->operationsInsightsState = $operationsInsightsState;
  }
  /**
   * @return self::OPERATIONS_INSIGHTS_STATE_*
   */
  public function getOperationsInsightsState()
  {
    return $this->operationsInsightsState;
  }
  /**
   * Output only. The list of OCIDs of standby databases located in Autonomous
   * Data Guard remote regions that are associated with the source database.
   *
   * @param string[] $peerDbIds
   */
  public function setPeerDbIds($peerDbIds)
  {
    $this->peerDbIds = $peerDbIds;
  }
  /**
   * @return string[]
   */
  public function getPeerDbIds()
  {
    return $this->peerDbIds;
  }
  /**
   * Output only. The permission level of the Autonomous Database.
   *
   * Accepted values: PERMISSION_LEVEL_UNSPECIFIED, RESTRICTED, UNRESTRICTED
   *
   * @param self::PERMISSION_LEVEL_* $permissionLevel
   */
  public function setPermissionLevel($permissionLevel)
  {
    $this->permissionLevel = $permissionLevel;
  }
  /**
   * @return self::PERMISSION_LEVEL_*
   */
  public function getPermissionLevel()
  {
    return $this->permissionLevel;
  }
  /**
   * Output only. The private endpoint for the Autonomous Database.
   *
   * @param string $privateEndpoint
   */
  public function setPrivateEndpoint($privateEndpoint)
  {
    $this->privateEndpoint = $privateEndpoint;
  }
  /**
   * @return string
   */
  public function getPrivateEndpoint()
  {
    return $this->privateEndpoint;
  }
  /**
   * Optional. The private endpoint IP address for the Autonomous Database.
   *
   * @param string $privateEndpointIp
   */
  public function setPrivateEndpointIp($privateEndpointIp)
  {
    $this->privateEndpointIp = $privateEndpointIp;
  }
  /**
   * @return string
   */
  public function getPrivateEndpointIp()
  {
    return $this->privateEndpointIp;
  }
  /**
   * Optional. The private endpoint label for the Autonomous Database.
   *
   * @param string $privateEndpointLabel
   */
  public function setPrivateEndpointLabel($privateEndpointLabel)
  {
    $this->privateEndpointLabel = $privateEndpointLabel;
  }
  /**
   * @return string
   */
  public function getPrivateEndpointLabel()
  {
    return $this->privateEndpointLabel;
  }
  /**
   * Output only. The refresh mode of the cloned Autonomous Database.
   *
   * Accepted values: REFRESHABLE_MODE_UNSPECIFIED, AUTOMATIC, MANUAL
   *
   * @param self::REFRESHABLE_MODE_* $refreshableMode
   */
  public function setRefreshableMode($refreshableMode)
  {
    $this->refreshableMode = $refreshableMode;
  }
  /**
   * @return self::REFRESHABLE_MODE_*
   */
  public function getRefreshableMode()
  {
    return $this->refreshableMode;
  }
  /**
   * Output only. The refresh State of the clone.
   *
   * Accepted values: REFRESHABLE_STATE_UNSPECIFIED, REFRESHING, NOT_REFRESHING
   *
   * @param self::REFRESHABLE_STATE_* $refreshableState
   */
  public function setRefreshableState($refreshableState)
  {
    $this->refreshableState = $refreshableState;
  }
  /**
   * @return self::REFRESHABLE_STATE_*
   */
  public function getRefreshableState()
  {
    return $this->refreshableState;
  }
  /**
   * Output only. The Data Guard role of the Autonomous Database.
   *
   * Accepted values: ROLE_UNSPECIFIED, PRIMARY, STANDBY, DISABLED_STANDBY,
   * BACKUP_COPY, SNAPSHOT_STANDBY
   *
   * @param self::ROLE_* $role
   */
  public function setRole($role)
  {
    $this->role = $role;
  }
  /**
   * @return self::ROLE_*
   */
  public function getRole()
  {
    return $this->role;
  }
  /**
   * Output only. The list and details of the scheduled operations of the
   * Autonomous Database.
   *
   * @param ScheduledOperationDetails[] $scheduledOperationDetails
   */
  public function setScheduledOperationDetails($scheduledOperationDetails)
  {
    $this->scheduledOperationDetails = $scheduledOperationDetails;
  }
  /**
   * @return ScheduledOperationDetails[]
   */
  public function getScheduledOperationDetails()
  {
    return $this->scheduledOperationDetails;
  }
  /**
   * Optional. The ID of the Oracle Cloud Infrastructure vault secret.
   *
   * @param string $secretId
   */
  public function setSecretId($secretId)
  {
    $this->secretId = $secretId;
  }
  /**
   * @return string
   */
  public function getSecretId()
  {
    return $this->secretId;
  }
  /**
   * Output only. An Oracle-managed Google Cloud service account on which
   * customers can grant roles to access resources in the customer project.
   *
   * @param string $serviceAgentEmail
   */
  public function setServiceAgentEmail($serviceAgentEmail)
  {
    $this->serviceAgentEmail = $serviceAgentEmail;
  }
  /**
   * @return string
   */
  public function getServiceAgentEmail()
  {
    return $this->serviceAgentEmail;
  }
  /**
   * Output only. The SQL Web Developer URL for the Autonomous Database.
   *
   * @param string $sqlWebDeveloperUrl
   */
  public function setSqlWebDeveloperUrl($sqlWebDeveloperUrl)
  {
    $this->sqlWebDeveloperUrl = $sqlWebDeveloperUrl;
  }
  /**
   * @return string
   */
  public function getSqlWebDeveloperUrl()
  {
    return $this->sqlWebDeveloperUrl;
  }
  /**
   * Output only. The current lifecycle state of the Autonomous Database.
   *
   * Accepted values: STATE_UNSPECIFIED, PROVISIONING, AVAILABLE, STOPPING,
   * STOPPED, STARTING, TERMINATING, TERMINATED, UNAVAILABLE,
   * RESTORE_IN_PROGRESS, RESTORE_FAILED, BACKUP_IN_PROGRESS, SCALE_IN_PROGRESS,
   * AVAILABLE_NEEDS_ATTENTION, UPDATING, MAINTENANCE_IN_PROGRESS, RESTARTING,
   * RECREATING, ROLE_CHANGE_IN_PROGRESS, UPGRADING, INACCESSIBLE, STANDBY
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
   * Output only. The list of available regions that can be used to create a
   * clone for the Autonomous Database.
   *
   * @param string[] $supportedCloneRegions
   */
  public function setSupportedCloneRegions($supportedCloneRegions)
  {
    $this->supportedCloneRegions = $supportedCloneRegions;
  }
  /**
   * @return string[]
   */
  public function getSupportedCloneRegions()
  {
    return $this->supportedCloneRegions;
  }
  /**
   * Output only. The storage space used by automatic backups of Autonomous
   * Database, in gigabytes.
   *
   * @param float $totalAutoBackupStorageSizeGbs
   */
  public function setTotalAutoBackupStorageSizeGbs($totalAutoBackupStorageSizeGbs)
  {
    $this->totalAutoBackupStorageSizeGbs = $totalAutoBackupStorageSizeGbs;
  }
  /**
   * @return float
   */
  public function getTotalAutoBackupStorageSizeGbs()
  {
    return $this->totalAutoBackupStorageSizeGbs;
  }
  /**
   * Output only. The storage space used by Autonomous Database, in gigabytes.
   *
   * @param int $usedDataStorageSizeTbs
   */
  public function setUsedDataStorageSizeTbs($usedDataStorageSizeTbs)
  {
    $this->usedDataStorageSizeTbs = $usedDataStorageSizeTbs;
  }
  /**
   * @return int
   */
  public function getUsedDataStorageSizeTbs()
  {
    return $this->usedDataStorageSizeTbs;
  }
  /**
   * Optional. The ID of the Oracle Cloud Infrastructure vault.
   *
   * @param string $vaultId
   */
  public function setVaultId($vaultId)
  {
    $this->vaultId = $vaultId;
  }
  /**
   * @return string
   */
  public function getVaultId()
  {
    return $this->vaultId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AutonomousDatabaseProperties::class, 'Google_Service_OracleDatabase_AutonomousDatabaseProperties');
