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

class BackupCollection extends \Google\Model
{
  /**
   * Output only. The full resource path of the cluster the backup collection
   * belongs to. Example:
   * projects/{project}/locations/{location}/clusters/{cluster}
   *
   * @var string
   */
  public $cluster;
  /**
   * Output only. The cluster uid of the backup collection.
   *
   * @var string
   */
  public $clusterUid;
  /**
   * Output only. The time when the backup collection was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The KMS key used to encrypt the backups under this backup
   * collection.
   *
   * @var string
   */
  public $kmsKey;
  /**
   * Output only. The last time a backup was created in the backup collection.
   *
   * @var string
   */
  public $lastBackupTime;
  /**
   * Identifier. Full resource path of the backup collection.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Total number of backups in the backup collection.
   *
   * @var string
   */
  public $totalBackupCount;
  /**
   * Output only. Total size of all backups in the backup collection.
   *
   * @var string
   */
  public $totalBackupSizeBytes;
  /**
   * Output only. System assigned unique identifier of the backup collection.
   *
   * @var string
   */
  public $uid;

  /**
   * Output only. The full resource path of the cluster the backup collection
   * belongs to. Example:
   * projects/{project}/locations/{location}/clusters/{cluster}
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
   * Output only. The cluster uid of the backup collection.
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
   * Output only. The time when the backup collection was created.
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
   * Output only. The KMS key used to encrypt the backups under this backup
   * collection.
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
   * Output only. The last time a backup was created in the backup collection.
   *
   * @param string $lastBackupTime
   */
  public function setLastBackupTime($lastBackupTime)
  {
    $this->lastBackupTime = $lastBackupTime;
  }
  /**
   * @return string
   */
  public function getLastBackupTime()
  {
    return $this->lastBackupTime;
  }
  /**
   * Identifier. Full resource path of the backup collection.
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
   * Output only. Total number of backups in the backup collection.
   *
   * @param string $totalBackupCount
   */
  public function setTotalBackupCount($totalBackupCount)
  {
    $this->totalBackupCount = $totalBackupCount;
  }
  /**
   * @return string
   */
  public function getTotalBackupCount()
  {
    return $this->totalBackupCount;
  }
  /**
   * Output only. Total size of all backups in the backup collection.
   *
   * @param string $totalBackupSizeBytes
   */
  public function setTotalBackupSizeBytes($totalBackupSizeBytes)
  {
    $this->totalBackupSizeBytes = $totalBackupSizeBytes;
  }
  /**
   * @return string
   */
  public function getTotalBackupSizeBytes()
  {
    return $this->totalBackupSizeBytes;
  }
  /**
   * Output only. System assigned unique identifier of the backup collection.
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
class_alias(BackupCollection::class, 'Google_Service_CloudRedis_BackupCollection');
