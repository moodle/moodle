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

namespace Google\Service\DatabaseMigrationService;

class CloudSqlSettings extends \Google\Model
{
  /**
   * unspecified policy.
   */
  public const ACTIVATION_POLICY_SQL_ACTIVATION_POLICY_UNSPECIFIED = 'SQL_ACTIVATION_POLICY_UNSPECIFIED';
  /**
   * The instance is always up and running.
   */
  public const ACTIVATION_POLICY_ALWAYS = 'ALWAYS';
  /**
   * The instance should never spin up.
   */
  public const ACTIVATION_POLICY_NEVER = 'NEVER';
  /**
   * This is an unknown Availability type.
   */
  public const AVAILABILITY_TYPE_SQL_AVAILABILITY_TYPE_UNSPECIFIED = 'SQL_AVAILABILITY_TYPE_UNSPECIFIED';
  /**
   * Zonal availablility instance.
   */
  public const AVAILABILITY_TYPE_ZONAL = 'ZONAL';
  /**
   * Regional availability instance.
   */
  public const AVAILABILITY_TYPE_REGIONAL = 'REGIONAL';
  /**
   * Unspecified.
   */
  public const DATA_DISK_TYPE_SQL_DATA_DISK_TYPE_UNSPECIFIED = 'SQL_DATA_DISK_TYPE_UNSPECIFIED';
  /**
   * SSD disk.
   */
  public const DATA_DISK_TYPE_PD_SSD = 'PD_SSD';
  /**
   * HDD disk.
   */
  public const DATA_DISK_TYPE_PD_HDD = 'PD_HDD';
  /**
   * A Hyperdisk Balanced data disk.
   */
  public const DATA_DISK_TYPE_HYPERDISK_BALANCED = 'HYPERDISK_BALANCED';
  /**
   * Unspecified version.
   */
  public const DATABASE_VERSION_SQL_DATABASE_VERSION_UNSPECIFIED = 'SQL_DATABASE_VERSION_UNSPECIFIED';
  /**
   * MySQL 5.6.
   */
  public const DATABASE_VERSION_MYSQL_5_6 = 'MYSQL_5_6';
  /**
   * MySQL 5.7.
   */
  public const DATABASE_VERSION_MYSQL_5_7 = 'MYSQL_5_7';
  /**
   * MySQL 8.0.
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
   * MySQL 8.4.
   */
  public const DATABASE_VERSION_MYSQL_8_4 = 'MYSQL_8_4';
  /**
   * PostgreSQL 9.6.
   */
  public const DATABASE_VERSION_POSTGRES_9_6 = 'POSTGRES_9_6';
  /**
   * PostgreSQL 11.
   */
  public const DATABASE_VERSION_POSTGRES_11 = 'POSTGRES_11';
  /**
   * PostgreSQL 10.
   */
  public const DATABASE_VERSION_POSTGRES_10 = 'POSTGRES_10';
  /**
   * PostgreSQL 12.
   */
  public const DATABASE_VERSION_POSTGRES_12 = 'POSTGRES_12';
  /**
   * PostgreSQL 13.
   */
  public const DATABASE_VERSION_POSTGRES_13 = 'POSTGRES_13';
  /**
   * PostgreSQL 14.
   */
  public const DATABASE_VERSION_POSTGRES_14 = 'POSTGRES_14';
  /**
   * PostgreSQL 15.
   */
  public const DATABASE_VERSION_POSTGRES_15 = 'POSTGRES_15';
  /**
   * PostgreSQL 16.
   */
  public const DATABASE_VERSION_POSTGRES_16 = 'POSTGRES_16';
  /**
   * The instance did not specify the edition.
   */
  public const EDITION_EDITION_UNSPECIFIED = 'EDITION_UNSPECIFIED';
  /**
   * The instance is an enterprise edition.
   */
  public const EDITION_ENTERPRISE = 'ENTERPRISE';
  /**
   * The instance is an enterprise plus edition.
   */
  public const EDITION_ENTERPRISE_PLUS = 'ENTERPRISE_PLUS';
  /**
   * The activation policy specifies when the instance is activated; it is
   * applicable only when the instance state is 'RUNNABLE'. Valid values:
   * 'ALWAYS': The instance is on, and remains so even in the absence of
   * connection requests. `NEVER`: The instance is off; it is not activated,
   * even if a connection request arrives.
   *
   * @var string
   */
  public $activationPolicy;
  /**
   * [default: ON] If you enable this setting, Cloud SQL checks your available
   * storage every 30 seconds. If the available storage falls below a threshold
   * size, Cloud SQL automatically adds additional storage capacity. If the
   * available storage repeatedly falls below the threshold size, Cloud SQL
   * continues to add storage until it reaches the maximum of 30 TB.
   *
   * @var bool
   */
  public $autoStorageIncrease;
  /**
   * Optional. Availability type. Potential values: * `ZONAL`: The instance
   * serves data from only one zone. Outages in that zone affect data
   * availability. * `REGIONAL`: The instance can serve data from more than one
   * zone in a region (it is highly available).
   *
   * @var string
   */
  public $availabilityType;
  /**
   * The KMS key name used for the csql instance.
   *
   * @var string
   */
  public $cmekKeyName;
  /**
   * The Cloud SQL default instance level collation.
   *
   * @var string
   */
  public $collation;
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
   * The storage capacity available to the database, in GB. The minimum (and
   * default) size is 10GB.
   *
   * @var string
   */
  public $dataDiskSizeGb;
  /**
   * The type of storage: `PD_SSD` (default) or `PD_HDD` or
   * `HYPERDISK_BALANCED`.
   *
   * @var string
   */
  public $dataDiskType;
  /**
   * The database flags passed to the Cloud SQL instance at startup. An object
   * containing a list of "key": value pairs. Example: { "name": "wrench",
   * "mass": "1.3kg", "count": "3" }.
   *
   * @var string[]
   */
  public $databaseFlags;
  /**
   * The database engine type and version. Deprecated. Use database_version_name
   * instead.
   *
   * @var string
   */
  public $databaseVersion;
  /**
   * Optional. The database engine type and version name.
   *
   * @var string
   */
  public $databaseVersionName;
  /**
   * Optional. The edition of the given Cloud SQL instance.
   *
   * @var string
   */
  public $edition;
  protected $ipConfigType = SqlIpConfig::class;
  protected $ipConfigDataType = '';
  /**
   * Input only. Initial root password.
   *
   * @var string
   */
  public $rootPassword;
  /**
   * Output only. Indicates If this connection profile root password is stored.
   *
   * @var bool
   */
  public $rootPasswordSet;
  /**
   * Optional. The Google Cloud Platform zone where the failover Cloud SQL
   * database instance is located. Used when the Cloud SQL database availability
   * type is REGIONAL (i.e. multiple zones / highly available).
   *
   * @var string
   */
  public $secondaryZone;
  /**
   * The Database Migration Service source connection profile ID, in the format:
   * `projects/my_project_name/locations/us-
   * central1/connectionProfiles/connection_profile_ID`
   *
   * @var string
   */
  public $sourceId;
  /**
   * The maximum size to which storage capacity can be automatically increased.
   * The default value is 0, which specifies that there is no limit.
   *
   * @var string
   */
  public $storageAutoResizeLimit;
  /**
   * The tier (or machine type) for this instance, for example:
   * `db-n1-standard-1` (MySQL instances) or `db-custom-1-3840` (PostgreSQL
   * instances). For more information, see [Cloud SQL Instance
   * Settings](https://cloud.google.com/sql/docs/mysql/instance-settings).
   *
   * @var string
   */
  public $tier;
  /**
   * The resource labels for a Cloud SQL instance to use to annotate any related
   * underlying resources such as Compute Engine VMs. An object containing a
   * list of "key": "value" pairs. Example: `{ "name": "wrench", "mass": "18kg",
   * "count": "3" }`.
   *
   * @var string[]
   */
  public $userLabels;
  /**
   * The Google Cloud Platform zone where your Cloud SQL database instance is
   * located.
   *
   * @var string
   */
  public $zone;

  /**
   * The activation policy specifies when the instance is activated; it is
   * applicable only when the instance state is 'RUNNABLE'. Valid values:
   * 'ALWAYS': The instance is on, and remains so even in the absence of
   * connection requests. `NEVER`: The instance is off; it is not activated,
   * even if a connection request arrives.
   *
   * Accepted values: SQL_ACTIVATION_POLICY_UNSPECIFIED, ALWAYS, NEVER
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
   * [default: ON] If you enable this setting, Cloud SQL checks your available
   * storage every 30 seconds. If the available storage falls below a threshold
   * size, Cloud SQL automatically adds additional storage capacity. If the
   * available storage repeatedly falls below the threshold size, Cloud SQL
   * continues to add storage until it reaches the maximum of 30 TB.
   *
   * @param bool $autoStorageIncrease
   */
  public function setAutoStorageIncrease($autoStorageIncrease)
  {
    $this->autoStorageIncrease = $autoStorageIncrease;
  }
  /**
   * @return bool
   */
  public function getAutoStorageIncrease()
  {
    return $this->autoStorageIncrease;
  }
  /**
   * Optional. Availability type. Potential values: * `ZONAL`: The instance
   * serves data from only one zone. Outages in that zone affect data
   * availability. * `REGIONAL`: The instance can serve data from more than one
   * zone in a region (it is highly available).
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
   * The KMS key name used for the csql instance.
   *
   * @param string $cmekKeyName
   */
  public function setCmekKeyName($cmekKeyName)
  {
    $this->cmekKeyName = $cmekKeyName;
  }
  /**
   * @return string
   */
  public function getCmekKeyName()
  {
    return $this->cmekKeyName;
  }
  /**
   * The Cloud SQL default instance level collation.
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
   * Optional. Data cache is an optional feature available for Cloud SQL for
   * MySQL Enterprise Plus edition only. For more information on data cache, see
   * [Data cache overview](https://cloud.google.com/sql/help/mysql-data-cache)
   * in Cloud SQL documentation.
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
   * The storage capacity available to the database, in GB. The minimum (and
   * default) size is 10GB.
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
   * The type of storage: `PD_SSD` (default) or `PD_HDD` or
   * `HYPERDISK_BALANCED`.
   *
   * Accepted values: SQL_DATA_DISK_TYPE_UNSPECIFIED, PD_SSD, PD_HDD,
   * HYPERDISK_BALANCED
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
   * The database flags passed to the Cloud SQL instance at startup. An object
   * containing a list of "key": value pairs. Example: { "name": "wrench",
   * "mass": "1.3kg", "count": "3" }.
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
   * The database engine type and version. Deprecated. Use database_version_name
   * instead.
   *
   * Accepted values: SQL_DATABASE_VERSION_UNSPECIFIED, MYSQL_5_6, MYSQL_5_7,
   * MYSQL_8_0, MYSQL_8_0_18, MYSQL_8_0_26, MYSQL_8_0_27, MYSQL_8_0_28,
   * MYSQL_8_0_30, MYSQL_8_0_31, MYSQL_8_0_32, MYSQL_8_0_33, MYSQL_8_0_34,
   * MYSQL_8_0_35, MYSQL_8_0_36, MYSQL_8_0_37, MYSQL_8_4, POSTGRES_9_6,
   * POSTGRES_11, POSTGRES_10, POSTGRES_12, POSTGRES_13, POSTGRES_14,
   * POSTGRES_15, POSTGRES_16
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
   * Optional. The database engine type and version name.
   *
   * @param string $databaseVersionName
   */
  public function setDatabaseVersionName($databaseVersionName)
  {
    $this->databaseVersionName = $databaseVersionName;
  }
  /**
   * @return string
   */
  public function getDatabaseVersionName()
  {
    return $this->databaseVersionName;
  }
  /**
   * Optional. The edition of the given Cloud SQL instance.
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
   * The settings for IP Management. This allows to enable or disable the
   * instance IP and manage which external networks can connect to the instance.
   * The IPv4 address cannot be disabled.
   *
   * @param SqlIpConfig $ipConfig
   */
  public function setIpConfig(SqlIpConfig $ipConfig)
  {
    $this->ipConfig = $ipConfig;
  }
  /**
   * @return SqlIpConfig
   */
  public function getIpConfig()
  {
    return $this->ipConfig;
  }
  /**
   * Input only. Initial root password.
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
   * Output only. Indicates If this connection profile root password is stored.
   *
   * @param bool $rootPasswordSet
   */
  public function setRootPasswordSet($rootPasswordSet)
  {
    $this->rootPasswordSet = $rootPasswordSet;
  }
  /**
   * @return bool
   */
  public function getRootPasswordSet()
  {
    return $this->rootPasswordSet;
  }
  /**
   * Optional. The Google Cloud Platform zone where the failover Cloud SQL
   * database instance is located. Used when the Cloud SQL database availability
   * type is REGIONAL (i.e. multiple zones / highly available).
   *
   * @param string $secondaryZone
   */
  public function setSecondaryZone($secondaryZone)
  {
    $this->secondaryZone = $secondaryZone;
  }
  /**
   * @return string
   */
  public function getSecondaryZone()
  {
    return $this->secondaryZone;
  }
  /**
   * The Database Migration Service source connection profile ID, in the format:
   * `projects/my_project_name/locations/us-
   * central1/connectionProfiles/connection_profile_ID`
   *
   * @param string $sourceId
   */
  public function setSourceId($sourceId)
  {
    $this->sourceId = $sourceId;
  }
  /**
   * @return string
   */
  public function getSourceId()
  {
    return $this->sourceId;
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
   * The tier (or machine type) for this instance, for example:
   * `db-n1-standard-1` (MySQL instances) or `db-custom-1-3840` (PostgreSQL
   * instances). For more information, see [Cloud SQL Instance
   * Settings](https://cloud.google.com/sql/docs/mysql/instance-settings).
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
   * The resource labels for a Cloud SQL instance to use to annotate any related
   * underlying resources such as Compute Engine VMs. An object containing a
   * list of "key": "value" pairs. Example: `{ "name": "wrench", "mass": "18kg",
   * "count": "3" }`.
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
  /**
   * The Google Cloud Platform zone where your Cloud SQL database instance is
   * located.
   *
   * @param string $zone
   */
  public function setZone($zone)
  {
    $this->zone = $zone;
  }
  /**
   * @return string
   */
  public function getZone()
  {
    return $this->zone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudSqlSettings::class, 'Google_Service_DatabaseMigrationService_CloudSqlSettings');
