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

class Backup extends \Google\Model
{
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
   * The state of the backup is unknown.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The backup is ready.
   */
  public const STATE_READY = 'READY';
  /**
   * The backup is creating.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The backup failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The backup is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Backup Type is unknown.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * ON_DEMAND backups that were triggered by the customer (e.g., not
   * AUTOMATED).
   */
  public const TYPE_ON_DEMAND = 'ON_DEMAND';
  /**
   * AUTOMATED backups triggered by the automated backups scheduler pursuant to
   * an automated backup policy.
   */
  public const TYPE_AUTOMATED = 'AUTOMATED';
  /**
   * CONTINUOUS backups triggered by the automated backups scheduler due to a
   * continuous backup policy.
   */
  public const TYPE_CONTINUOUS = 'CONTINUOUS';
  /**
   * Annotations to allow client tools to store small amount of arbitrary data.
   * This is distinct from labels. https://google.aip.dev/128
   *
   * @var string[]
   */
  public $annotations;
  /**
   * Required. The full resource name of the backup source cluster (e.g.,
   * projects/{project}/locations/{region}/clusters/{cluster_id}).
   *
   * @var string
   */
  public $clusterName;
  /**
   * Output only. The system-generated UID of the cluster which was used to
   * create this resource.
   *
   * @var string
   */
  public $clusterUid;
  /**
   * Output only. Timestamp when the resource finished being created.
   *
   * @var string
   */
  public $createCompletionTime;
  /**
   * Output only. Create time stamp
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The database engine major version of the cluster this backup
   * was created from. Any restored cluster created from this backup will have
   * the same database version.
   *
   * @var string
   */
  public $databaseVersion;
  /**
   * Output only. Delete time stamp
   *
   * @var string
   */
  public $deleteTime;
  /**
   * User-provided description of the backup.
   *
   * @var string
   */
  public $description;
  /**
   * User-settable and human-readable display name for the Backup.
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
  protected $expiryQuantityType = QuantityBasedExpiry::class;
  protected $expiryQuantityDataType = '';
  /**
   * Output only. The time at which after the backup is eligible to be garbage
   * collected. It is the duration specified by the backup's retention policy,
   * added to the backup's create_time.
   *
   * @var string
   */
  public $expiryTime;
  /**
   * Labels as key value pairs
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. The name of the backup resource with the format: *
   * projects/{project}/locations/{region}/backups/{backup_id} where the cluster
   * and backup ID segments should satisfy the regex expression
   * `[a-z]([a-z0-9-]{0,61}[a-z0-9])?`, e.g. 1-63 characters of lowercase
   * letters, numbers, and dashes, starting with a letter, and ending with a
   * letter or number. For more details see https://google.aip.dev/122. The
   * prefix of the backup resource name is the name of the parent resource: *
   * projects/{project}/locations/{region}
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Reconciling (https://google.aip.dev/128#reconciliation), if
   * true, indicates that the service is actively updating the resource. This
   * can happen due to user-triggered updates or system actions like failover or
   * maintenance.
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
  /**
   * Output only. The size of the backup in bytes.
   *
   * @var string
   */
  public $sizeBytes;
  /**
   * Output only. The current state of the backup.
   *
   * @var string
   */
  public $state;
  /**
   * Optional. Input only. Immutable. Tag keys/values directly bound to this
   * resource. For example: ``` "123/environment": "production",
   * "123/costCenter": "marketing" ```
   *
   * @var string[]
   */
  public $tags;
  /**
   * The backup type, which suggests the trigger for the backup.
   *
   * @var string
   */
  public $type;
  /**
   * Output only. The system-generated UID of the resource. The UID is assigned
   * when the resource is created, and it is retained until it is deleted.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. Update time stamp Users should not infer any meaning from this
   * field. Its value is generally unrelated to the timing of the backup
   * creation operation.
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
   * Required. The full resource name of the backup source cluster (e.g.,
   * projects/{project}/locations/{region}/clusters/{cluster_id}).
   *
   * @param string $clusterName
   */
  public function setClusterName($clusterName)
  {
    $this->clusterName = $clusterName;
  }
  /**
   * @return string
   */
  public function getClusterName()
  {
    return $this->clusterName;
  }
  /**
   * Output only. The system-generated UID of the cluster which was used to
   * create this resource.
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
   * Output only. Timestamp when the resource finished being created.
   *
   * @param string $createCompletionTime
   */
  public function setCreateCompletionTime($createCompletionTime)
  {
    $this->createCompletionTime = $createCompletionTime;
  }
  /**
   * @return string
   */
  public function getCreateCompletionTime()
  {
    return $this->createCompletionTime;
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
   * Output only. The database engine major version of the cluster this backup
   * was created from. Any restored cluster created from this backup will have
   * the same database version.
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
   * User-provided description of the backup.
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
   * User-settable and human-readable display name for the Backup.
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
   * Optional. The encryption config can be specified to encrypt the backup with
   * a customer-managed encryption key (CMEK). When this field is not specified,
   * the backup will then use default encryption scheme to protect the user
   * data.
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
   * Output only. The encryption information for the backup.
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
   * Output only. The QuantityBasedExpiry of the backup, specified by the
   * backup's retention policy. Once the expiry quantity is over retention, the
   * backup is eligible to be garbage collected.
   *
   * @param QuantityBasedExpiry $expiryQuantity
   */
  public function setExpiryQuantity(QuantityBasedExpiry $expiryQuantity)
  {
    $this->expiryQuantity = $expiryQuantity;
  }
  /**
   * @return QuantityBasedExpiry
   */
  public function getExpiryQuantity()
  {
    return $this->expiryQuantity;
  }
  /**
   * Output only. The time at which after the backup is eligible to be garbage
   * collected. It is the duration specified by the backup's retention policy,
   * added to the backup's create_time.
   *
   * @param string $expiryTime
   */
  public function setExpiryTime($expiryTime)
  {
    $this->expiryTime = $expiryTime;
  }
  /**
   * @return string
   */
  public function getExpiryTime()
  {
    return $this->expiryTime;
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
   * Output only. The name of the backup resource with the format: *
   * projects/{project}/locations/{region}/backups/{backup_id} where the cluster
   * and backup ID segments should satisfy the regex expression
   * `[a-z]([a-z0-9-]{0,61}[a-z0-9])?`, e.g. 1-63 characters of lowercase
   * letters, numbers, and dashes, starting with a letter, and ending with a
   * letter or number. For more details see https://google.aip.dev/122. The
   * prefix of the backup resource name is the name of the parent resource: *
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
   * Output only. Reconciling (https://google.aip.dev/128#reconciliation), if
   * true, indicates that the service is actively updating the resource. This
   * can happen due to user-triggered updates or system actions like failover or
   * maintenance.
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
   * Output only. The size of the backup in bytes.
   *
   * @param string $sizeBytes
   */
  public function setSizeBytes($sizeBytes)
  {
    $this->sizeBytes = $sizeBytes;
  }
  /**
   * @return string
   */
  public function getSizeBytes()
  {
    return $this->sizeBytes;
  }
  /**
   * Output only. The current state of the backup.
   *
   * Accepted values: STATE_UNSPECIFIED, READY, CREATING, FAILED, DELETING
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
   * The backup type, which suggests the trigger for the backup.
   *
   * Accepted values: TYPE_UNSPECIFIED, ON_DEMAND, AUTOMATED, CONTINUOUS
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
   * Output only. Update time stamp Users should not infer any meaning from this
   * field. Its value is generally unrelated to the timing of the backup
   * creation operation.
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
class_alias(Backup::class, 'Google_Service_CloudAlloyDBAdmin_Backup');
