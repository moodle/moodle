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

class StoragePool extends \Google\Model
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
   * Unspecified QoS Type
   */
  public const QOS_TYPE_QOS_TYPE_UNSPECIFIED = 'QOS_TYPE_UNSPECIFIED';
  /**
   * QoS Type is Auto
   */
  public const QOS_TYPE_AUTO = 'AUTO';
  /**
   * QoS Type is Manual
   */
  public const QOS_TYPE_MANUAL = 'MANUAL';
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
   * Unspecified Storage Pool State
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Storage Pool State is Ready
   */
  public const STATE_READY = 'READY';
  /**
   * Storage Pool State is Creating
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * Storage Pool State is Deleting
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Storage Pool State is Updating
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * Storage Pool State is Restoring
   */
  public const STATE_RESTORING = 'RESTORING';
  /**
   * Storage Pool State is Disabled
   */
  public const STATE_DISABLED = 'DISABLED';
  /**
   * Storage Pool State is Error
   */
  public const STATE_ERROR = 'ERROR';
  /**
   * Storage pool type is not specified.
   */
  public const TYPE_STORAGE_POOL_TYPE_UNSPECIFIED = 'STORAGE_POOL_TYPE_UNSPECIFIED';
  /**
   * Storage pool type is file.
   */
  public const TYPE_FILE = 'FILE';
  /**
   * Storage pool type is unified.
   */
  public const TYPE_UNIFIED = 'UNIFIED';
  /**
   * Storage pool type is unified large capacity.
   */
  public const TYPE_UNIFIED_LARGE_CAPACITY = 'UNIFIED_LARGE_CAPACITY';
  /**
   * Optional. Specifies the Active Directory to be used for creating a SMB
   * volume.
   *
   * @var string
   */
  public $activeDirectory;
  /**
   * Optional. True if the storage pool supports Auto Tiering enabled volumes.
   * Default is false. Auto-tiering can be enabled after storage pool creation
   * but it can't be disabled once enabled.
   *
   * @var bool
   */
  public $allowAutoTiering;
  /**
   * Output only. Available throughput of the storage pool (in MiB/s).
   *
   * @var 
   */
  public $availableThroughputMibps;
  /**
   * Required. Capacity in GIB of the pool
   *
   * @var string
   */
  public $capacityGib;
  /**
   * Output only. Total cold tier data rounded down to the nearest GiB used by
   * the storage pool.
   *
   * @var string
   */
  public $coldTierSizeUsedGib;
  /**
   * Output only. Create time of the storage pool
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. True if using Independent Scaling of capacity and performance
   * (Hyperdisk) By default set to false
   *
   * @var bool
   */
  public $customPerformanceEnabled;
  /**
   * Optional. Description of the storage pool
   *
   * @var string
   */
  public $description;
  /**
   * Optional. Flag indicating that the hot-tier threshold will be auto-
   * increased by 10% of the hot-tier when it hits 100%. Default is true. The
   * increment will kick in only if the new size after increment is still less
   * than or equal to storage pool size.
   *
   * @var bool
   */
  public $enableHotTierAutoResize;
  /**
   * Output only. Specifies the current pool encryption key source.
   *
   * @var string
   */
  public $encryptionType;
  /**
   * Deprecated. Used to allow SO pool to access AD or DNS server from other
   * regions.
   *
   * @deprecated
   * @var bool
   */
  public $globalAccessAllowed;
  /**
   * Optional. Total hot tier capacity for the Storage Pool. It is applicable
   * only to Flex service level. It should be less than the minimum storage pool
   * size and cannot be more than the current storage pool size. It cannot be
   * decreased once set.
   *
   * @var string
   */
  public $hotTierSizeGib;
  /**
   * Output only. Total hot tier data rounded down to the nearest GiB used by
   * the storage pool.
   *
   * @var string
   */
  public $hotTierSizeUsedGib;
  /**
   * Optional. Specifies the KMS config to be used for volume encryption.
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
   * Optional. Flag indicating if the pool is NFS LDAP enabled or not.
   *
   * @var bool
   */
  public $ldapEnabled;
  /**
   * Identifier. Name of the storage pool
   *
   * @var string
   */
  public $name;
  /**
   * Required. VPC Network name. Format:
   * projects/{project}/global/networks/{network}
   *
   * @var string
   */
  public $network;
  /**
   * Optional. This field is not implemented. The values provided in this field
   * are ignored.
   *
   * @var string
   */
  public $psaRange;
  /**
   * Optional. QoS (Quality of Service) Type of the storage pool
   *
   * @var string
   */
  public $qosType;
  /**
   * Optional. Specifies the replica zone for regional storagePool.
   *
   * @var string
   */
  public $replicaZone;
  /**
   * Output only. Reserved for future use
   *
   * @var bool
   */
  public $satisfiesPzi;
  /**
   * Output only. Reserved for future use
   *
   * @var bool
   */
  public $satisfiesPzs;
  /**
   * Required. Service level of the storage pool
   *
   * @var string
   */
  public $serviceLevel;
  /**
   * Output only. State of the storage pool
   *
   * @var string
   */
  public $state;
  /**
   * Output only. State details of the storage pool
   *
   * @var string
   */
  public $stateDetails;
  /**
   * Optional. Custom Performance Total IOPS of the pool if not provided, it
   * will be calculated based on the total_throughput_mibps
   *
   * @var string
   */
  public $totalIops;
  /**
   * Optional. Custom Performance Total Throughput of the pool (in MiBps)
   *
   * @var string
   */
  public $totalThroughputMibps;
  /**
   * Optional. Type of the storage pool. This field is used to control whether
   * the pool supports `FILE` based volumes only or `UNIFIED` (both `FILE` and
   * `BLOCK`) volumes or `UNIFIED_LARGE_CAPACITY` (both `FILE` and `BLOCK`)
   * volumes with large capacity. If not specified during creation, it defaults
   * to `FILE`.
   *
   * @var string
   */
  public $type;
  /**
   * Output only. Allocated size of all volumes in GIB in the storage pool
   *
   * @var string
   */
  public $volumeCapacityGib;
  /**
   * Output only. Volume count of the storage pool
   *
   * @var int
   */
  public $volumeCount;
  /**
   * Optional. Specifies the active zone for regional storagePool.
   *
   * @var string
   */
  public $zone;

  /**
   * Optional. Specifies the Active Directory to be used for creating a SMB
   * volume.
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
   * Optional. True if the storage pool supports Auto Tiering enabled volumes.
   * Default is false. Auto-tiering can be enabled after storage pool creation
   * but it can't be disabled once enabled.
   *
   * @param bool $allowAutoTiering
   */
  public function setAllowAutoTiering($allowAutoTiering)
  {
    $this->allowAutoTiering = $allowAutoTiering;
  }
  /**
   * @return bool
   */
  public function getAllowAutoTiering()
  {
    return $this->allowAutoTiering;
  }
  public function setAvailableThroughputMibps($availableThroughputMibps)
  {
    $this->availableThroughputMibps = $availableThroughputMibps;
  }
  public function getAvailableThroughputMibps()
  {
    return $this->availableThroughputMibps;
  }
  /**
   * Required. Capacity in GIB of the pool
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
   * Output only. Total cold tier data rounded down to the nearest GiB used by
   * the storage pool.
   *
   * @param string $coldTierSizeUsedGib
   */
  public function setColdTierSizeUsedGib($coldTierSizeUsedGib)
  {
    $this->coldTierSizeUsedGib = $coldTierSizeUsedGib;
  }
  /**
   * @return string
   */
  public function getColdTierSizeUsedGib()
  {
    return $this->coldTierSizeUsedGib;
  }
  /**
   * Output only. Create time of the storage pool
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
   * Optional. True if using Independent Scaling of capacity and performance
   * (Hyperdisk) By default set to false
   *
   * @param bool $customPerformanceEnabled
   */
  public function setCustomPerformanceEnabled($customPerformanceEnabled)
  {
    $this->customPerformanceEnabled = $customPerformanceEnabled;
  }
  /**
   * @return bool
   */
  public function getCustomPerformanceEnabled()
  {
    return $this->customPerformanceEnabled;
  }
  /**
   * Optional. Description of the storage pool
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
   * Optional. Flag indicating that the hot-tier threshold will be auto-
   * increased by 10% of the hot-tier when it hits 100%. Default is true. The
   * increment will kick in only if the new size after increment is still less
   * than or equal to storage pool size.
   *
   * @param bool $enableHotTierAutoResize
   */
  public function setEnableHotTierAutoResize($enableHotTierAutoResize)
  {
    $this->enableHotTierAutoResize = $enableHotTierAutoResize;
  }
  /**
   * @return bool
   */
  public function getEnableHotTierAutoResize()
  {
    return $this->enableHotTierAutoResize;
  }
  /**
   * Output only. Specifies the current pool encryption key source.
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
   * Deprecated. Used to allow SO pool to access AD or DNS server from other
   * regions.
   *
   * @deprecated
   * @param bool $globalAccessAllowed
   */
  public function setGlobalAccessAllowed($globalAccessAllowed)
  {
    $this->globalAccessAllowed = $globalAccessAllowed;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getGlobalAccessAllowed()
  {
    return $this->globalAccessAllowed;
  }
  /**
   * Optional. Total hot tier capacity for the Storage Pool. It is applicable
   * only to Flex service level. It should be less than the minimum storage pool
   * size and cannot be more than the current storage pool size. It cannot be
   * decreased once set.
   *
   * @param string $hotTierSizeGib
   */
  public function setHotTierSizeGib($hotTierSizeGib)
  {
    $this->hotTierSizeGib = $hotTierSizeGib;
  }
  /**
   * @return string
   */
  public function getHotTierSizeGib()
  {
    return $this->hotTierSizeGib;
  }
  /**
   * Output only. Total hot tier data rounded down to the nearest GiB used by
   * the storage pool.
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
   * Optional. Specifies the KMS config to be used for volume encryption.
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
   * Optional. Flag indicating if the pool is NFS LDAP enabled or not.
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
   * Identifier. Name of the storage pool
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
   * Required. VPC Network name. Format:
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
   * Optional. This field is not implemented. The values provided in this field
   * are ignored.
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
   * Optional. QoS (Quality of Service) Type of the storage pool
   *
   * Accepted values: QOS_TYPE_UNSPECIFIED, AUTO, MANUAL
   *
   * @param self::QOS_TYPE_* $qosType
   */
  public function setQosType($qosType)
  {
    $this->qosType = $qosType;
  }
  /**
   * @return self::QOS_TYPE_*
   */
  public function getQosType()
  {
    return $this->qosType;
  }
  /**
   * Optional. Specifies the replica zone for regional storagePool.
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
   * Output only. Reserved for future use
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
   * Output only. Reserved for future use
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
   * Required. Service level of the storage pool
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
   * Output only. State of the storage pool
   *
   * Accepted values: STATE_UNSPECIFIED, READY, CREATING, DELETING, UPDATING,
   * RESTORING, DISABLED, ERROR
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
   * Output only. State details of the storage pool
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
   * Optional. Custom Performance Total IOPS of the pool if not provided, it
   * will be calculated based on the total_throughput_mibps
   *
   * @param string $totalIops
   */
  public function setTotalIops($totalIops)
  {
    $this->totalIops = $totalIops;
  }
  /**
   * @return string
   */
  public function getTotalIops()
  {
    return $this->totalIops;
  }
  /**
   * Optional. Custom Performance Total Throughput of the pool (in MiBps)
   *
   * @param string $totalThroughputMibps
   */
  public function setTotalThroughputMibps($totalThroughputMibps)
  {
    $this->totalThroughputMibps = $totalThroughputMibps;
  }
  /**
   * @return string
   */
  public function getTotalThroughputMibps()
  {
    return $this->totalThroughputMibps;
  }
  /**
   * Optional. Type of the storage pool. This field is used to control whether
   * the pool supports `FILE` based volumes only or `UNIFIED` (both `FILE` and
   * `BLOCK`) volumes or `UNIFIED_LARGE_CAPACITY` (both `FILE` and `BLOCK`)
   * volumes with large capacity. If not specified during creation, it defaults
   * to `FILE`.
   *
   * Accepted values: STORAGE_POOL_TYPE_UNSPECIFIED, FILE, UNIFIED,
   * UNIFIED_LARGE_CAPACITY
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Output only. Allocated size of all volumes in GIB in the storage pool
   *
   * @param string $volumeCapacityGib
   */
  public function setVolumeCapacityGib($volumeCapacityGib)
  {
    $this->volumeCapacityGib = $volumeCapacityGib;
  }
  /**
   * @return string
   */
  public function getVolumeCapacityGib()
  {
    return $this->volumeCapacityGib;
  }
  /**
   * Output only. Volume count of the storage pool
   *
   * @param int $volumeCount
   */
  public function setVolumeCount($volumeCount)
  {
    $this->volumeCount = $volumeCount;
  }
  /**
   * @return int
   */
  public function getVolumeCount()
  {
    return $this->volumeCount;
  }
  /**
   * Optional. Specifies the active zone for regional storagePool.
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
class_alias(StoragePool::class, 'Google_Service_NetAppFiles_StoragePool');
