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

class Backup extends \Google\Collection
{
  /**
   * The default value, not set.
   */
  public const BACKUP_TYPE_BACKUP_TYPE_UNSPECIFIED = 'BACKUP_TYPE_UNSPECIFIED';
  /**
   * On-demand backup.
   */
  public const BACKUP_TYPE_ON_DEMAND = 'ON_DEMAND';
  /**
   * Automated backup.
   */
  public const BACKUP_TYPE_AUTOMATED = 'AUTOMATED';
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
   * The default value, not set.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The backup is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The backup is active to be used.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The backup is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The backup is currently suspended due to reasons like project deletion,
   * billing account closure, etc.
   */
  public const STATE_SUSPENDED = 'SUSPENDED';
  protected $collection_key = 'backupFiles';
  protected $backupFilesType = BackupFile::class;
  protected $backupFilesDataType = 'array';
  /**
   * Output only. Type of the backup.
   *
   * @var string
   */
  public $backupType;
  /**
   * Output only. Cluster resource path of this backup.
   *
   * @var string
   */
  public $cluster;
  /**
   * Output only. Cluster uid of this backup.
   *
   * @var string
   */
  public $clusterUid;
  /**
   * Output only. The time when the backup was created.
   *
   * @var string
   */
  public $createTime;
  protected $encryptionInfoType = EncryptionInfo::class;
  protected $encryptionInfoDataType = '';
  /**
   * Output only. redis-7.2, valkey-7.5
   *
   * @var string
   */
  public $engineVersion;
  /**
   * Output only. The time when the backup will expire.
   *
   * @var string
   */
  public $expireTime;
  /**
   * Identifier. Full resource path of the backup. the last part of the name is
   * the backup id with the following format: [YYYYMMDDHHMMSS]_[Shorted Cluster
   * UID] OR customer specified while backup cluster. Example:
   * 20240515123000_1234
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Node type of the cluster.
   *
   * @var string
   */
  public $nodeType;
  /**
   * Output only. Number of replicas for the cluster.
   *
   * @var int
   */
  public $replicaCount;
  /**
   * Output only. Number of shards for the cluster.
   *
   * @var int
   */
  public $shardCount;
  /**
   * Output only. State of the backup.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Total size of the backup in bytes.
   *
   * @var string
   */
  public $totalSizeBytes;
  /**
   * Output only. System assigned unique identifier of the backup.
   *
   * @var string
   */
  public $uid;

  /**
   * Output only. List of backup files of the backup.
   *
   * @param BackupFile[] $backupFiles
   */
  public function setBackupFiles($backupFiles)
  {
    $this->backupFiles = $backupFiles;
  }
  /**
   * @return BackupFile[]
   */
  public function getBackupFiles()
  {
    return $this->backupFiles;
  }
  /**
   * Output only. Type of the backup.
   *
   * Accepted values: BACKUP_TYPE_UNSPECIFIED, ON_DEMAND, AUTOMATED
   *
   * @param self::BACKUP_TYPE_* $backupType
   */
  public function setBackupType($backupType)
  {
    $this->backupType = $backupType;
  }
  /**
   * @return self::BACKUP_TYPE_*
   */
  public function getBackupType()
  {
    return $this->backupType;
  }
  /**
   * Output only. Cluster resource path of this backup.
   *
   * @param string $cluster
   */
  public function setCluster($cluster)
  {
    $this->cluster = $cluster;
  }
  /**
   * @return string
   */
  public function getCluster()
  {
    return $this->cluster;
  }
  /**
   * Output only. Cluster uid of this backup.
   *
   * @param string $clusterUid
   */
  public function setClusterUid($clusterUid)
  {
    $this->clusterUid = $clusterUid;
  }
  /**
   * @return string
   */
  public function getClusterUid()
  {
    return $this->clusterUid;
  }
  /**
   * Output only. The time when the backup was created.
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
   * Output only. Encryption information of the backup.
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
   * Output only. redis-7.2, valkey-7.5
   *
   * @param string $engineVersion
   */
  public function setEngineVersion($engineVersion)
  {
    $this->engineVersion = $engineVersion;
  }
  /**
   * @return string
   */
  public function getEngineVersion()
  {
    return $this->engineVersion;
  }
  /**
   * Output only. The time when the backup will expire.
   *
   * @param string $expireTime
   */
  public function setExpireTime($expireTime)
  {
    $this->expireTime = $expireTime;
  }
  /**
   * @return string
   */
  public function getExpireTime()
  {
    return $this->expireTime;
  }
  /**
   * Identifier. Full resource path of the backup. the last part of the name is
   * the backup id with the following format: [YYYYMMDDHHMMSS]_[Shorted Cluster
   * UID] OR customer specified while backup cluster. Example:
   * 20240515123000_1234
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
   * Output only. Node type of the cluster.
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
   * Output only. Number of replicas for the cluster.
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
   * Output only. Number of shards for the cluster.
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
   * Output only. State of the backup.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, ACTIVE, DELETING, SUSPENDED
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
   * Output only. Total size of the backup in bytes.
   *
   * @param string $totalSizeBytes
   */
  public function setTotalSizeBytes($totalSizeBytes)
  {
    $this->totalSizeBytes = $totalSizeBytes;
  }
  /**
   * @return string
   */
  public function getTotalSizeBytes()
  {
    return $this->totalSizeBytes;
  }
  /**
   * Output only. System assigned unique identifier of the backup.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Backup::class, 'Google_Service_CloudRedis_Backup');
