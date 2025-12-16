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

namespace Google\Service\NetAppFiles;

class Volume extends \Google\Collection
{
  /**
   * The source of the encryption key is not specified.
   */
  public const ENCRYPTION_TYPE_ENCRYPTION_TYPE_UNSPECIFIED = 'ENCRYPTION_TYPE_UNSPECIFIED';
  /**
   * Google managed encryption key.
   */
  public const ENCRYPTION_TYPE_SERVICE_MANAGED = 'SERVICE_MANAGED';
  /**
   * Customer managed encryption key, which is stored in KMS.
   */
  public const ENCRYPTION_TYPE_CLOUD_KMS = 'CLOUD_KMS';
  /**
   * SecurityStyle is unspecified
   */
  public const SECURITY_STYLE_SECURITY_STYLE_UNSPECIFIED = 'SECURITY_STYLE_UNSPECIFIED';
  /**
   * SecurityStyle uses NTFS
   */
  public const SECURITY_STYLE_NTFS = 'NTFS';
  /**
   * SecurityStyle uses UNIX
   */
  public const SECURITY_STYLE_UNIX = 'UNIX';
  /**
   * Unspecified service level.
   */
  public const SERVICE_LEVEL_SERVICE_LEVEL_UNSPECIFIED = 'SERVICE_LEVEL_UNSPECIFIED';
  /**
   * Premium service level.
   */
  public const SERVICE_LEVEL_PREMIUM = 'PREMIUM';
  /**
   * Extreme service level.
   */
  public const SERVICE_LEVEL_EXTREME = 'EXTREME';
  /**
   * Standard service level.
   */
  public const SERVICE_LEVEL_STANDARD = 'STANDARD';
  /**
   * Flex service level.
   */
  public const SERVICE_LEVEL_FLEX = 'FLEX';
  /**
   * Unspecified Volume State
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Volume State is Ready
   */
  public const STATE_READY = 'READY';
  /**
   * Volume State is Creating
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * Volume State is Deleting
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Volume State is Updating
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * Volume State is Restoring
   */
  public const STATE_RESTORING = 'RESTORING';
  /**
   * Volume State is Disabled
   */
  public const STATE_DISABLED = 'DISABLED';
  /**
   * Volume State is Error
   */
  public const STATE_ERROR = 'ERROR';
  /**
   * Volume State is Preparing. Note that this is different from CREATING where
   * CREATING means the volume is being created, while PREPARING means the
   * volume is created and now being prepared for the replication.
   */
  public const STATE_PREPARING = 'PREPARING';
  /**
   * Volume State is Read Only
   */
  public const STATE_READ_ONLY = 'READ_ONLY';
  protected $collection_key = 'smbSettings';
  /**
   * Output only. Specifies the ActiveDirectory name of a SMB volume.
   *
   * @var string
   */
  public $activeDirectory;
  protected $backupConfigType = BackupConfig::class;
  protected $backupConfigDataType = '';
  protected $blockDevicesType = BlockDevice::class;
  protected $blockDevicesDataType = 'array';
  protected $cacheParametersType = CacheParameters::class;
  protected $cacheParametersDataType = '';
  /**
   * Required. Capacity in GIB of the volume
   *
   * @var string
   */
  public $capacityGib;
  /**
   * Output only. Size of the volume cold tier data rounded down to the nearest
   * GiB.
   *
   * @var string
   */
  public $coldTierSizeGib;
  /**
   * Output only. Create time of the volume
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Description of the volume
   *
   * @var string
   */
  public $description;
  /**
   * Output only. Specified the current volume encryption key source.
   *
   * @var string
   */
  public $encryptionType;
  protected $exportPolicyType = ExportPolicy::class;
  protected $exportPolicyDataType = '';
  /**
   * Output only. Indicates whether the volume is part of a replication
   * relationship.
   *
   * @var bool
   */
  public $hasReplication;
  /**
   * Output only. Total hot tier data rounded down to the nearest GiB used by
   * the Volume. This field is only used for flex Service Level
   *
   * @var string
   */
  public $hotTierSizeUsedGib;
  protected $hybridReplicationParametersType = HybridReplicationParameters::class;
  protected $hybridReplicationParametersDataType = '';
  /**
   * Optional. Flag indicating if the volume is a kerberos volume or not, export
   * policy rules control kerberos security modes (krb5, krb5i, krb5p).
   *
   * @var bool
   */
  public $kerberosEnabled;
  /**
   * Output only. Specifies the KMS config to be used for volume encryption.
   *
   * @var string
   */
  public $kmsConfig;
  /**
   * Optional. Labels as key value pairs
   *
   * @var string[]
   */
  public $labels;
  /**
   * Optional. Flag indicating if the volume will be a large capacity volume or
   * a regular volume.
   *
   * @var bool
   */
  public $largeCapacity;
  /**
   * Output only. Flag indicating if the volume is NFS LDAP enabled or not.
   *
   * @var bool
   */
  public $ldapEnabled;
  protected $mountOptionsType = MountOption::class;
  protected $mountOptionsDataType = 'array';
  /**
   * Optional. Flag indicating if the volume will have an IP address per node
   * for volumes supporting multiple IP endpoints. Only the volume with
   * large_capacity will be allowed to have multiple endpoints.
   *
   * @var bool
   */
  public $multipleEndpoints;
  /**
   * Identifier. Name of the volume
   *
   * @var string
   */
  public $name;
  /**
   * Output only. VPC Network name. Format:
   * projects/{project}/global/networks/{network}
   *
   * @var string
   */
  public $network;
  /**
   * Required. Protocols required for the volume
   *
   * @var string[]
   */
  public $protocols;
  /**
   * Output only. This field is not implemented. The values provided in this
   * field are ignored.
   *
   * @var string
   */
  public $psaRange;
  /**
   * Output only. Specifies the replica zone for regional volume.
   *
   * @var string
   */
  public $replicaZone;
  protected $restoreParametersType = RestoreParameters::class;
  protected $restoreParametersDataType = '';
  /**
   * Optional. List of actions that are restricted on this volume.
   *
   * @var string[]
   */
  public $restrictedActions;
  /**
   * Optional. Security Style of the Volume
   *
   * @var string
   */
  public $securityStyle;
  /**
   * Output only. Service level of the volume
   *
   * @var string
   */
  public $serviceLevel;
  /**
   * Required. Share name of the volume
   *
   * @var string
   */
  public $shareName;
  /**
   * Optional. SMB share settings for the volume.
   *
   * @var string[]
   */
  public $smbSettings;
  /**
   * Optional. Snap_reserve specifies percentage of volume storage reserved for
   * snapshot storage. Default is 0 percent.
   *
   * @var 
   */
  public $snapReserve;
  /**
   * Optional. Snapshot_directory if enabled (true) the volume will contain a
   * read-only .snapshot directory which provides access to each of the volume's
   * snapshots.
   *
   * @var bool
   */
  public $snapshotDirectory;
  protected $snapshotPolicyType = SnapshotPolicy::class;
  protected $snapshotPolicyDataType = '';
  /**
   * Output only. State of the volume
   *
   * @var string
   */
  public $state;
  /**
   * Output only. State details of the volume
   *
   * @var string
   */
  public $stateDetails;
  /**
   * Required. StoragePool name of the volume
   *
   * @var string
   */
  public $storagePool;
  /**
   * Optional. Throughput of the volume (in MiB/s)
   *
   * @var 
   */
  public $throughputMibps;
  protected $tieringPolicyType = TieringPolicy::class;
  protected $tieringPolicyDataType = '';
  /**
   * Optional. Default unix style permission (e.g. 777) the mount point will be
   * created with. Applicable for NFS protocol types only.
   *
   * @var string
   */
  public $unixPermissions;
  /**
   * Output only. Used capacity in GIB of the volume. This is computed
   * periodically and it does not represent the realtime usage.
   *
   * @var string
   */
  public $usedGib;
  /**
   * Output only. Specifies the active zone for regional volume.
   *
   * @var string
   */
  public $zone;

  /**
   * Output only. Specifies the ActiveDirectory name of a SMB volume.
   *
   * @param string $activeDirectory
   */
  public function setActiveDirectory($activeDirectory)
  {
    $this->activeDirectory = $activeDirectory;
  }
  /**
   * @return string
   */
  public function getActiveDirectory()
  {
    return $this->activeDirectory;
  }
  /**
   * BackupConfig of the volume.
   *
   * @param BackupConfig $backupConfig
   */
  public function setBackupConfig(BackupConfig $backupConfig)
  {
    $this->backupConfig = $backupConfig;
  }
  /**
   * @return BackupConfig
   */
  public function getBackupConfig()
  {
    return $this->backupConfig;
  }
  /**
   * Optional. Block devices for the volume. Currently, only one block device is
   * permitted per Volume.
   *
   * @param BlockDevice[] $blockDevices
   */
  public function setBlockDevices($blockDevices)
  {
    $this->blockDevices = $blockDevices;
  }
  /**
   * @return BlockDevice[]
   */
  public function getBlockDevices()
  {
    return $this->blockDevices;
  }
  /**
   * Optional. Cache parameters for the volume.
   *
   * @param CacheParameters $cacheParameters
   */
  public function setCacheParameters(CacheParameters $cacheParameters)
  {
    $this->cacheParameters = $cacheParameters;
  }
  /**
   * @return CacheParameters
   */
  public function getCacheParameters()
  {
    return $this->cacheParameters;
  }
  /**
   * Required. Capacity in GIB of the volume
   *
   * @param string $capacityGib
   */
  public function setCapacityGib($capacityGib)
  {
    $this->capacityGib = $capacityGib;
  }
  /**
   * @return string
   */
  public function getCapacityGib()
  {
    return $this->capacityGib;
  }
  /**
   * Output only. Size of the volume cold tier data rounded down to the nearest
   * GiB.
   *
   * @param string $coldTierSizeGib
   */
  public function setColdTierSizeGib($coldTierSizeGib)
  {
    $this->coldTierSizeGib = $coldTierSizeGib;
  }
  /**
   * @return string
   */
  public function getColdTierSizeGib()
  {
    return $this->coldTierSizeGib;
  }
  /**
   * Output only. Create time of the volume
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
   * Optional. Description of the volume
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
   * Output only. Specified the current volume encryption key source.
   *
   * Accepted values: ENCRYPTION_TYPE_UNSPECIFIED, SERVICE_MANAGED, CLOUD_KMS
   *
   * @param self::ENCRYPTION_TYPE_* $encryptionType
   */
  public function setEncryptionType($encryptionType)
  {
    $this->encryptionType = $encryptionType;
  }
  /**
   * @return self::ENCRYPTION_TYPE_*
   */
  public function getEncryptionType()
  {
    return $this->encryptionType;
  }
  /**
   * Optional. Export policy of the volume
   *
   * @param ExportPolicy $exportPolicy
   */
  public function setExportPolicy(ExportPolicy $exportPolicy)
  {
    $this->exportPolicy = $exportPolicy;
  }
  /**
   * @return ExportPolicy
   */
  public function getExportPolicy()
  {
    return $this->exportPolicy;
  }
  /**
   * Output only. Indicates whether the volume is part of a replication
   * relationship.
   *
   * @param bool $hasReplication
   */
  public function setHasReplication($hasReplication)
  {
    $this->hasReplication = $hasReplication;
  }
  /**
   * @return bool
   */
  public function getHasReplication()
  {
    return $this->hasReplication;
  }
  /**
   * Output only. Total hot tier data rounded down to the nearest GiB used by
   * the Volume. This field is only used for flex Service Level
   *
   * @param string $hotTierSizeUsedGib
   */
  public function setHotTierSizeUsedGib($hotTierSizeUsedGib)
  {
    $this->hotTierSizeUsedGib = $hotTierSizeUsedGib;
  }
  /**
   * @return string
   */
  public function getHotTierSizeUsedGib()
  {
    return $this->hotTierSizeUsedGib;
  }
  /**
   * Optional. The Hybrid Replication parameters for the volume.
   *
   * @param HybridReplicationParameters $hybridReplicationParameters
   */
  public function setHybridReplicationParameters(HybridReplicationParameters $hybridReplicationParameters)
  {
    $this->hybridReplicationParameters = $hybridReplicationParameters;
  }
  /**
   * @return HybridReplicationParameters
   */
  public function getHybridReplicationParameters()
  {
    return $this->hybridReplicationParameters;
  }
  /**
   * Optional. Flag indicating if the volume is a kerberos volume or not, export
   * policy rules control kerberos security modes (krb5, krb5i, krb5p).
   *
   * @param bool $kerberosEnabled
   */
  public function setKerberosEnabled($kerberosEnabled)
  {
    $this->kerberosEnabled = $kerberosEnabled;
  }
  /**
   * @return bool
   */
  public function getKerberosEnabled()
  {
    return $this->kerberosEnabled;
  }
  /**
   * Output only. Specifies the KMS config to be used for volume encryption.
   *
   * @param string $kmsConfig
   */
  public function setKmsConfig($kmsConfig)
  {
    $this->kmsConfig = $kmsConfig;
  }
  /**
   * @return string
   */
  public function getKmsConfig()
  {
    return $this->kmsConfig;
  }
  /**
   * Optional. Labels as key value pairs
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
   * Optional. Flag indicating if the volume will be a large capacity volume or
   * a regular volume.
   *
   * @param bool $largeCapacity
   */
  public function setLargeCapacity($largeCapacity)
  {
    $this->largeCapacity = $largeCapacity;
  }
  /**
   * @return bool
   */
  public function getLargeCapacity()
  {
    return $this->largeCapacity;
  }
  /**
   * Output only. Flag indicating if the volume is NFS LDAP enabled or not.
   *
   * @param bool $ldapEnabled
   */
  public function setLdapEnabled($ldapEnabled)
  {
    $this->ldapEnabled = $ldapEnabled;
  }
  /**
   * @return bool
   */
  public function getLdapEnabled()
  {
    return $this->ldapEnabled;
  }
  /**
   * Output only. Mount options of this volume
   *
   * @param MountOption[] $mountOptions
   */
  public function setMountOptions($mountOptions)
  {
    $this->mountOptions = $mountOptions;
  }
  /**
   * @return MountOption[]
   */
  public function getMountOptions()
  {
    return $this->mountOptions;
  }
  /**
   * Optional. Flag indicating if the volume will have an IP address per node
   * for volumes supporting multiple IP endpoints. Only the volume with
   * large_capacity will be allowed to have multiple endpoints.
   *
   * @param bool $multipleEndpoints
   */
  public function setMultipleEndpoints($multipleEndpoints)
  {
    $this->multipleEndpoints = $multipleEndpoints;
  }
  /**
   * @return bool
   */
  public function getMultipleEndpoints()
  {
    return $this->multipleEndpoints;
  }
  /**
   * Identifier. Name of the volume
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
   * Output only. VPC Network name. Format:
   * projects/{project}/global/networks/{network}
   *
   * @param string $network
   */
  public function setNetwork($network)
  {
    $this->network = $network;
  }
  /**
   * @return string
   */
  public function getNetwork()
  {
    return $this->network;
  }
  /**
   * Required. Protocols required for the volume
   *
   * @param string[] $protocols
   */
  public function setProtocols($protocols)
  {
    $this->protocols = $protocols;
  }
  /**
   * @return string[]
   */
  public function getProtocols()
  {
    return $this->protocols;
  }
  /**
   * Output only. This field is not implemented. The values provided in this
   * field are ignored.
   *
   * @param string $psaRange
   */
  public function setPsaRange($psaRange)
  {
    $this->psaRange = $psaRange;
  }
  /**
   * @return string
   */
  public function getPsaRange()
  {
    return $this->psaRange;
  }
  /**
   * Output only. Specifies the replica zone for regional volume.
   *
   * @param string $replicaZone
   */
  public function setReplicaZone($replicaZone)
  {
    $this->replicaZone = $replicaZone;
  }
  /**
   * @return string
   */
  public function getReplicaZone()
  {
    return $this->replicaZone;
  }
  /**
   * Optional. Specifies the source of the volume to be created from.
   *
   * @param RestoreParameters $restoreParameters
   */
  public function setRestoreParameters(RestoreParameters $restoreParameters)
  {
    $this->restoreParameters = $restoreParameters;
  }
  /**
   * @return RestoreParameters
   */
  public function getRestoreParameters()
  {
    return $this->restoreParameters;
  }
  /**
   * Optional. List of actions that are restricted on this volume.
   *
   * @param string[] $restrictedActions
   */
  public function setRestrictedActions($restrictedActions)
  {
    $this->restrictedActions = $restrictedActions;
  }
  /**
   * @return string[]
   */
  public function getRestrictedActions()
  {
    return $this->restrictedActions;
  }
  /**
   * Optional. Security Style of the Volume
   *
   * Accepted values: SECURITY_STYLE_UNSPECIFIED, NTFS, UNIX
   *
   * @param self::SECURITY_STYLE_* $securityStyle
   */
  public function setSecurityStyle($securityStyle)
  {
    $this->securityStyle = $securityStyle;
  }
  /**
   * @return self::SECURITY_STYLE_*
   */
  public function getSecurityStyle()
  {
    return $this->securityStyle;
  }
  /**
   * Output only. Service level of the volume
   *
   * Accepted values: SERVICE_LEVEL_UNSPECIFIED, PREMIUM, EXTREME, STANDARD,
   * FLEX
   *
   * @param self::SERVICE_LEVEL_* $serviceLevel
   */
  public function setServiceLevel($serviceLevel)
  {
    $this->serviceLevel = $serviceLevel;
  }
  /**
   * @return self::SERVICE_LEVEL_*
   */
  public function getServiceLevel()
  {
    return $this->serviceLevel;
  }
  /**
   * Required. Share name of the volume
   *
   * @param string $shareName
   */
  public function setShareName($shareName)
  {
    $this->shareName = $shareName;
  }
  /**
   * @return string
   */
  public function getShareName()
  {
    return $this->shareName;
  }
  /**
   * Optional. SMB share settings for the volume.
   *
   * @param string[] $smbSettings
   */
  public function setSmbSettings($smbSettings)
  {
    $this->smbSettings = $smbSettings;
  }
  /**
   * @return string[]
   */
  public function getSmbSettings()
  {
    return $this->smbSettings;
  }
  public function setSnapReserve($snapReserve)
  {
    $this->snapReserve = $snapReserve;
  }
  public function getSnapReserve()
  {
    return $this->snapReserve;
  }
  /**
   * Optional. Snapshot_directory if enabled (true) the volume will contain a
   * read-only .snapshot directory which provides access to each of the volume's
   * snapshots.
   *
   * @param bool $snapshotDirectory
   */
  public function setSnapshotDirectory($snapshotDirectory)
  {
    $this->snapshotDirectory = $snapshotDirectory;
  }
  /**
   * @return bool
   */
  public function getSnapshotDirectory()
  {
    return $this->snapshotDirectory;
  }
  /**
   * Optional. SnapshotPolicy for a volume.
   *
   * @param SnapshotPolicy $snapshotPolicy
   */
  public function setSnapshotPolicy(SnapshotPolicy $snapshotPolicy)
  {
    $this->snapshotPolicy = $snapshotPolicy;
  }
  /**
   * @return SnapshotPolicy
   */
  public function getSnapshotPolicy()
  {
    return $this->snapshotPolicy;
  }
  /**
   * Output only. State of the volume
   *
   * Accepted values: STATE_UNSPECIFIED, READY, CREATING, DELETING, UPDATING,
   * RESTORING, DISABLED, ERROR, PREPARING, READ_ONLY
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
   * Output only. State details of the volume
   *
   * @param string $stateDetails
   */
  public function setStateDetails($stateDetails)
  {
    $this->stateDetails = $stateDetails;
  }
  /**
   * @return string
   */
  public function getStateDetails()
  {
    return $this->stateDetails;
  }
  /**
   * Required. StoragePool name of the volume
   *
   * @param string $storagePool
   */
  public function setStoragePool($storagePool)
  {
    $this->storagePool = $storagePool;
  }
  /**
   * @return string
   */
  public function getStoragePool()
  {
    return $this->storagePool;
  }
  public function setThroughputMibps($throughputMibps)
  {
    $this->throughputMibps = $throughputMibps;
  }
  public function getThroughputMibps()
  {
    return $this->throughputMibps;
  }
  /**
   * Tiering policy for the volume.
   *
   * @param TieringPolicy $tieringPolicy
   */
  public function setTieringPolicy(TieringPolicy $tieringPolicy)
  {
    $this->tieringPolicy = $tieringPolicy;
  }
  /**
   * @return TieringPolicy
   */
  public function getTieringPolicy()
  {
    return $this->tieringPolicy;
  }
  /**
   * Optional. Default unix style permission (e.g. 777) the mount point will be
   * created with. Applicable for NFS protocol types only.
   *
   * @param string $unixPermissions
   */
  public function setUnixPermissions($unixPermissions)
  {
    $this->unixPermissions = $unixPermissions;
  }
  /**
   * @return string
   */
  public function getUnixPermissions()
  {
    return $this->unixPermissions;
  }
  /**
   * Output only. Used capacity in GIB of the volume. This is computed
   * periodically and it does not represent the realtime usage.
   *
   * @param string $usedGib
   */
  public function setUsedGib($usedGib)
  {
    $this->usedGib = $usedGib;
  }
  /**
   * @return string
   */
  public function getUsedGib()
  {
    return $this->usedGib;
  }
  /**
   * Output only. Specifies the active zone for regional volume.
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
class_alias(Volume::class, 'Google_Service_NetAppFiles_Volume');
