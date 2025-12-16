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

namespace Google\Service\CloudFilestore;

class Backup extends \Google\Model
{
  /**
   * FILE_PROTOCOL_UNSPECIFIED serves a "not set" default value when a
   * FileProtocol is a separate field in a message.
   */
  public const FILE_SYSTEM_PROTOCOL_FILE_PROTOCOL_UNSPECIFIED = 'FILE_PROTOCOL_UNSPECIFIED';
  /**
   * NFS 3.0.
   */
  public const FILE_SYSTEM_PROTOCOL_NFS_V3 = 'NFS_V3';
  /**
   * NFS 4.1.
   */
  public const FILE_SYSTEM_PROTOCOL_NFS_V4_1 = 'NFS_V4_1';
  /**
   * Not set.
   */
  public const SOURCE_INSTANCE_TIER_TIER_UNSPECIFIED = 'TIER_UNSPECIFIED';
  /**
   * STANDARD tier. BASIC_HDD is the preferred term for this tier.
   */
  public const SOURCE_INSTANCE_TIER_STANDARD = 'STANDARD';
  /**
   * PREMIUM tier. BASIC_SSD is the preferred term for this tier.
   */
  public const SOURCE_INSTANCE_TIER_PREMIUM = 'PREMIUM';
  /**
   * BASIC instances offer a maximum capacity of 63.9 TB. BASIC_HDD is an alias
   * for STANDARD Tier, offering economical performance backed by HDD.
   */
  public const SOURCE_INSTANCE_TIER_BASIC_HDD = 'BASIC_HDD';
  /**
   * BASIC instances offer a maximum capacity of 63.9 TB. BASIC_SSD is an alias
   * for PREMIUM Tier, and offers improved performance backed by SSD.
   */
  public const SOURCE_INSTANCE_TIER_BASIC_SSD = 'BASIC_SSD';
  /**
   * HIGH_SCALE instances offer expanded capacity and performance scaling
   * capabilities.
   */
  public const SOURCE_INSTANCE_TIER_HIGH_SCALE_SSD = 'HIGH_SCALE_SSD';
  /**
   * ENTERPRISE instances offer the features and availability needed for
   * mission-critical workloads.
   */
  public const SOURCE_INSTANCE_TIER_ENTERPRISE = 'ENTERPRISE';
  /**
   * ZONAL instances offer expanded capacity and performance scaling
   * capabilities.
   */
  public const SOURCE_INSTANCE_TIER_ZONAL = 'ZONAL';
  /**
   * REGIONAL instances offer the features and availability needed for mission-
   * critical workloads.
   */
  public const SOURCE_INSTANCE_TIER_REGIONAL = 'REGIONAL';
  /**
   * State not set.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Backup is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * Backup has been taken and the operation is being finalized. At this point,
   * changes to the file share will not be reflected in the backup.
   */
  public const STATE_FINALIZING = 'FINALIZING';
  /**
   * Backup is available for use.
   */
  public const STATE_READY = 'READY';
  /**
   * Backup is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Backup is not valid and cannot be used for creating new instances or
   * restoring existing instances.
   */
  public const STATE_INVALID = 'INVALID';
  /**
   * Output only. Capacity of the source file share when the backup was created.
   *
   * @var string
   */
  public $capacityGb;
  /**
   * Output only. The time when the backup was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * A description of the backup with 2048 characters or less. Requests with
   * longer descriptions will be rejected.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. Amount of bytes that will be downloaded if the backup is
   * restored. This may be different than storage bytes, since sequential
   * backups of the same disk will share storage.
   *
   * @var string
   */
  public $downloadBytes;
  /**
   * Output only. The file system protocol of the source Filestore instance that
   * this backup is created from.
   *
   * @var string
   */
  public $fileSystemProtocol;
  /**
   * Immutable. KMS key name used for data encryption.
   *
   * @var string
   */
  public $kmsKey;
  /**
   * Resource labels to represent user provided metadata.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. The resource name of the backup, in the format
   * `projects/{project_number}/locations/{location_id}/backups/{backup_id}`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzi;
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzs;
  /**
   * Name of the file share in the source Filestore instance that the backup is
   * created from.
   *
   * @var string
   */
  public $sourceFileShare;
  /**
   * The resource name of the source Filestore instance, in the format `projects
   * /{project_number}/locations/{location_id}/instances/{instance_id}`, used to
   * create this backup.
   *
   * @var string
   */
  public $sourceInstance;
  /**
   * Output only. The service tier of the source Filestore instance that this
   * backup is created from.
   *
   * @var string
   */
  public $sourceInstanceTier;
  /**
   * Output only. The backup state.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The size of the storage used by the backup. As backups share
   * storage, this number is expected to change with backup creation/deletion.
   *
   * @var string
   */
  public $storageBytes;
  /**
   * Optional. Input only. Immutable. Tag key-value pairs bound to this
   * resource. Each key must be a namespaced name and each value a short name.
   * Example: "123456789012/environment" : "production",
   * "123456789013/costCenter" : "marketing" See the documentation for more
   * information: - Namespaced name: https://cloud.google.com/resource-
   * manager/docs/tags/tags-creating-and-managing#retrieving_tag_key - Short
   * name: https://cloud.google.com/resource-manager/docs/tags/tags-creating-
   * and-managing#retrieving_tag_value
   *
   * @var string[]
   */
  public $tags;

  /**
   * Output only. Capacity of the source file share when the backup was created.
   *
   * @param string $capacityGb
   */
  public function setCapacityGb($capacityGb)
  {
    $this->capacityGb = $capacityGb;
  }
  /**
   * @return string
   */
  public function getCapacityGb()
  {
    return $this->capacityGb;
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
   * A description of the backup with 2048 characters or less. Requests with
   * longer descriptions will be rejected.
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
   * Output only. Amount of bytes that will be downloaded if the backup is
   * restored. This may be different than storage bytes, since sequential
   * backups of the same disk will share storage.
   *
   * @param string $downloadBytes
   */
  public function setDownloadBytes($downloadBytes)
  {
    $this->downloadBytes = $downloadBytes;
  }
  /**
   * @return string
   */
  public function getDownloadBytes()
  {
    return $this->downloadBytes;
  }
  /**
   * Output only. The file system protocol of the source Filestore instance that
   * this backup is created from.
   *
   * Accepted values: FILE_PROTOCOL_UNSPECIFIED, NFS_V3, NFS_V4_1
   *
   * @param self::FILE_SYSTEM_PROTOCOL_* $fileSystemProtocol
   */
  public function setFileSystemProtocol($fileSystemProtocol)
  {
    $this->fileSystemProtocol = $fileSystemProtocol;
  }
  /**
   * @return self::FILE_SYSTEM_PROTOCOL_*
   */
  public function getFileSystemProtocol()
  {
    return $this->fileSystemProtocol;
  }
  /**
   * Immutable. KMS key name used for data encryption.
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
   * Resource labels to represent user provided metadata.
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
   * Output only. The resource name of the backup, in the format
   * `projects/{project_number}/locations/{location_id}/backups/{backup_id}`.
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
   * Output only. Reserved for future use.
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
   * Name of the file share in the source Filestore instance that the backup is
   * created from.
   *
   * @param string $sourceFileShare
   */
  public function setSourceFileShare($sourceFileShare)
  {
    $this->sourceFileShare = $sourceFileShare;
  }
  /**
   * @return string
   */
  public function getSourceFileShare()
  {
    return $this->sourceFileShare;
  }
  /**
   * The resource name of the source Filestore instance, in the format `projects
   * /{project_number}/locations/{location_id}/instances/{instance_id}`, used to
   * create this backup.
   *
   * @param string $sourceInstance
   */
  public function setSourceInstance($sourceInstance)
  {
    $this->sourceInstance = $sourceInstance;
  }
  /**
   * @return string
   */
  public function getSourceInstance()
  {
    return $this->sourceInstance;
  }
  /**
   * Output only. The service tier of the source Filestore instance that this
   * backup is created from.
   *
   * Accepted values: TIER_UNSPECIFIED, STANDARD, PREMIUM, BASIC_HDD, BASIC_SSD,
   * HIGH_SCALE_SSD, ENTERPRISE, ZONAL, REGIONAL
   *
   * @param self::SOURCE_INSTANCE_TIER_* $sourceInstanceTier
   */
  public function setSourceInstanceTier($sourceInstanceTier)
  {
    $this->sourceInstanceTier = $sourceInstanceTier;
  }
  /**
   * @return self::SOURCE_INSTANCE_TIER_*
   */
  public function getSourceInstanceTier()
  {
    return $this->sourceInstanceTier;
  }
  /**
   * Output only. The backup state.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, FINALIZING, READY, DELETING,
   * INVALID
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
   * Output only. The size of the storage used by the backup. As backups share
   * storage, this number is expected to change with backup creation/deletion.
   *
   * @param string $storageBytes
   */
  public function setStorageBytes($storageBytes)
  {
    $this->storageBytes = $storageBytes;
  }
  /**
   * @return string
   */
  public function getStorageBytes()
  {
    return $this->storageBytes;
  }
  /**
   * Optional. Input only. Immutable. Tag key-value pairs bound to this
   * resource. Each key must be a namespaced name and each value a short name.
   * Example: "123456789012/environment" : "production",
   * "123456789013/costCenter" : "marketing" See the documentation for more
   * information: - Namespaced name: https://cloud.google.com/resource-
   * manager/docs/tags/tags-creating-and-managing#retrieving_tag_key - Short
   * name: https://cloud.google.com/resource-manager/docs/tags/tags-creating-
   * and-managing#retrieving_tag_value
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Backup::class, 'Google_Service_CloudFilestore_Backup');
