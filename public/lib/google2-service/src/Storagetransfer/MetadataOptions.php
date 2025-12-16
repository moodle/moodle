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

namespace Google\Service\Storagetransfer;

class MetadataOptions extends \Google\Model
{
  /**
   * ACL behavior is unspecified.
   */
  public const ACL_ACL_UNSPECIFIED = 'ACL_UNSPECIFIED';
  /**
   * Use the destination bucket's default object ACLS, if applicable.
   */
  public const ACL_ACL_DESTINATION_BUCKET_DEFAULT = 'ACL_DESTINATION_BUCKET_DEFAULT';
  /**
   * Preserve the object's original ACLs. This requires the service account to
   * have `storage.objects.getIamPolicy` permission for the source object.
   * [Uniform bucket-level
   * access](https://cloud.google.com/storage/docs/uniform-bucket-level-access)
   * must not be enabled on either the source or destination buckets.
   */
  public const ACL_ACL_PRESERVE = 'ACL_PRESERVE';
  /**
   * GID behavior is unspecified.
   */
  public const GID_GID_UNSPECIFIED = 'GID_UNSPECIFIED';
  /**
   * Do not preserve GID during a transfer job.
   */
  public const GID_GID_SKIP = 'GID_SKIP';
  /**
   * Preserve GID during a transfer job.
   */
  public const GID_GID_NUMBER = 'GID_NUMBER';
  /**
   * KmsKey behavior is unspecified.
   */
  public const KMS_KEY_KMS_KEY_UNSPECIFIED = 'KMS_KEY_UNSPECIFIED';
  /**
   * Use the destination bucket's default encryption settings.
   */
  public const KMS_KEY_KMS_KEY_DESTINATION_BUCKET_DEFAULT = 'KMS_KEY_DESTINATION_BUCKET_DEFAULT';
  /**
   * Preserve the object's original Cloud KMS customer-managed encryption key
   * (CMEK) if present. Objects that do not use a Cloud KMS encryption key will
   * be encrypted using the destination bucket's encryption settings.
   */
  public const KMS_KEY_KMS_KEY_PRESERVE = 'KMS_KEY_PRESERVE';
  /**
   * Mode behavior is unspecified.
   */
  public const MODE_MODE_UNSPECIFIED = 'MODE_UNSPECIFIED';
  /**
   * Do not preserve mode during a transfer job.
   */
  public const MODE_MODE_SKIP = 'MODE_SKIP';
  /**
   * Preserve mode during a transfer job.
   */
  public const MODE_MODE_PRESERVE = 'MODE_PRESERVE';
  /**
   * Storage class behavior is unspecified.
   */
  public const STORAGE_CLASS_STORAGE_CLASS_UNSPECIFIED = 'STORAGE_CLASS_UNSPECIFIED';
  /**
   * Use the destination bucket's default storage class.
   */
  public const STORAGE_CLASS_STORAGE_CLASS_DESTINATION_BUCKET_DEFAULT = 'STORAGE_CLASS_DESTINATION_BUCKET_DEFAULT';
  /**
   * Preserve the object's original storage class. This is only supported for
   * transfers from Google Cloud Storage buckets. REGIONAL and MULTI_REGIONAL
   * storage classes will be mapped to STANDARD to ensure they can be written to
   * the destination bucket.
   */
  public const STORAGE_CLASS_STORAGE_CLASS_PRESERVE = 'STORAGE_CLASS_PRESERVE';
  /**
   * Set the storage class to STANDARD.
   */
  public const STORAGE_CLASS_STORAGE_CLASS_STANDARD = 'STORAGE_CLASS_STANDARD';
  /**
   * Set the storage class to NEARLINE.
   */
  public const STORAGE_CLASS_STORAGE_CLASS_NEARLINE = 'STORAGE_CLASS_NEARLINE';
  /**
   * Set the storage class to COLDLINE.
   */
  public const STORAGE_CLASS_STORAGE_CLASS_COLDLINE = 'STORAGE_CLASS_COLDLINE';
  /**
   * Set the storage class to ARCHIVE.
   */
  public const STORAGE_CLASS_STORAGE_CLASS_ARCHIVE = 'STORAGE_CLASS_ARCHIVE';
  /**
   * Symlink behavior is unspecified.
   */
  public const SYMLINK_SYMLINK_UNSPECIFIED = 'SYMLINK_UNSPECIFIED';
  /**
   * Do not preserve symlinks during a transfer job.
   */
  public const SYMLINK_SYMLINK_SKIP = 'SYMLINK_SKIP';
  /**
   * Preserve symlinks during a transfer job.
   */
  public const SYMLINK_SYMLINK_PRESERVE = 'SYMLINK_PRESERVE';
  /**
   * Temporary hold behavior is unspecified.
   */
  public const TEMPORARY_HOLD_TEMPORARY_HOLD_UNSPECIFIED = 'TEMPORARY_HOLD_UNSPECIFIED';
  /**
   * Do not set a temporary hold on the destination object.
   */
  public const TEMPORARY_HOLD_TEMPORARY_HOLD_SKIP = 'TEMPORARY_HOLD_SKIP';
  /**
   * Preserve the object's original temporary hold status.
   */
  public const TEMPORARY_HOLD_TEMPORARY_HOLD_PRESERVE = 'TEMPORARY_HOLD_PRESERVE';
  /**
   * TimeCreated behavior is unspecified.
   */
  public const TIME_CREATED_TIME_CREATED_UNSPECIFIED = 'TIME_CREATED_UNSPECIFIED';
  /**
   * Do not preserve the `timeCreated` metadata from the source object.
   */
  public const TIME_CREATED_TIME_CREATED_SKIP = 'TIME_CREATED_SKIP';
  /**
   * Preserves the source object's `timeCreated` or `lastModified` metadata in
   * the `customTime` field in the destination object. Note that any value
   * stored in the source object's `customTime` field will not be propagated to
   * the destination object.
   */
  public const TIME_CREATED_TIME_CREATED_PRESERVE_AS_CUSTOM_TIME = 'TIME_CREATED_PRESERVE_AS_CUSTOM_TIME';
  /**
   * UID behavior is unspecified.
   */
  public const UID_UID_UNSPECIFIED = 'UID_UNSPECIFIED';
  /**
   * Do not preserve UID during a transfer job.
   */
  public const UID_UID_SKIP = 'UID_SKIP';
  /**
   * Preserve UID during a transfer job.
   */
  public const UID_UID_NUMBER = 'UID_NUMBER';
  /**
   * Specifies how each object's ACLs should be preserved for transfers between
   * Google Cloud Storage buckets. If unspecified, the default behavior is the
   * same as ACL_DESTINATION_BUCKET_DEFAULT.
   *
   * @var string
   */
  public $acl;
  /**
   * Specifies how each file's POSIX group ID (GID) attribute should be handled
   * by the transfer. By default, GID is not preserved. Only applicable to
   * transfers involving POSIX file systems, and ignored for other transfers.
   *
   * @var string
   */
  public $gid;
  /**
   * Specifies how each object's Cloud KMS customer-managed encryption key
   * (CMEK) is preserved for transfers between Google Cloud Storage buckets. If
   * unspecified, the default behavior is the same as
   * KMS_KEY_DESTINATION_BUCKET_DEFAULT.
   *
   * @var string
   */
  public $kmsKey;
  /**
   * Specifies how each file's mode attribute should be handled by the transfer.
   * By default, mode is not preserved. Only applicable to transfers involving
   * POSIX file systems, and ignored for other transfers.
   *
   * @var string
   */
  public $mode;
  /**
   * Specifies the storage class to set on objects being transferred to Google
   * Cloud Storage buckets. If unspecified, the default behavior is the same as
   * STORAGE_CLASS_DESTINATION_BUCKET_DEFAULT.
   *
   * @var string
   */
  public $storageClass;
  /**
   * Specifies how symlinks should be handled by the transfer. By default,
   * symlinks are not preserved. Only applicable to transfers involving POSIX
   * file systems, and ignored for other transfers.
   *
   * @var string
   */
  public $symlink;
  /**
   * Specifies how each object's temporary hold status should be preserved for
   * transfers between Google Cloud Storage buckets. If unspecified, the default
   * behavior is the same as TEMPORARY_HOLD_PRESERVE.
   *
   * @var string
   */
  public $temporaryHold;
  /**
   * Specifies how each object's `timeCreated` metadata is preserved for
   * transfers. If unspecified, the default behavior is the same as
   * TIME_CREATED_SKIP. This behavior is supported for transfers to Cloud
   * Storage buckets from Cloud Storage, Amazon S3, S3-compatible storage, and
   * Azure sources.
   *
   * @var string
   */
  public $timeCreated;
  /**
   * Specifies how each file's POSIX user ID (UID) attribute should be handled
   * by the transfer. By default, UID is not preserved. Only applicable to
   * transfers involving POSIX file systems, and ignored for other transfers.
   *
   * @var string
   */
  public $uid;

  /**
   * Specifies how each object's ACLs should be preserved for transfers between
   * Google Cloud Storage buckets. If unspecified, the default behavior is the
   * same as ACL_DESTINATION_BUCKET_DEFAULT.
   *
   * Accepted values: ACL_UNSPECIFIED, ACL_DESTINATION_BUCKET_DEFAULT,
   * ACL_PRESERVE
   *
   * @param self::ACL_* $acl
   */
  public function setAcl($acl)
  {
    $this->acl = $acl;
  }
  /**
   * @return self::ACL_*
   */
  public function getAcl()
  {
    return $this->acl;
  }
  /**
   * Specifies how each file's POSIX group ID (GID) attribute should be handled
   * by the transfer. By default, GID is not preserved. Only applicable to
   * transfers involving POSIX file systems, and ignored for other transfers.
   *
   * Accepted values: GID_UNSPECIFIED, GID_SKIP, GID_NUMBER
   *
   * @param self::GID_* $gid
   */
  public function setGid($gid)
  {
    $this->gid = $gid;
  }
  /**
   * @return self::GID_*
   */
  public function getGid()
  {
    return $this->gid;
  }
  /**
   * Specifies how each object's Cloud KMS customer-managed encryption key
   * (CMEK) is preserved for transfers between Google Cloud Storage buckets. If
   * unspecified, the default behavior is the same as
   * KMS_KEY_DESTINATION_BUCKET_DEFAULT.
   *
   * Accepted values: KMS_KEY_UNSPECIFIED, KMS_KEY_DESTINATION_BUCKET_DEFAULT,
   * KMS_KEY_PRESERVE
   *
   * @param self::KMS_KEY_* $kmsKey
   */
  public function setKmsKey($kmsKey)
  {
    $this->kmsKey = $kmsKey;
  }
  /**
   * @return self::KMS_KEY_*
   */
  public function getKmsKey()
  {
    return $this->kmsKey;
  }
  /**
   * Specifies how each file's mode attribute should be handled by the transfer.
   * By default, mode is not preserved. Only applicable to transfers involving
   * POSIX file systems, and ignored for other transfers.
   *
   * Accepted values: MODE_UNSPECIFIED, MODE_SKIP, MODE_PRESERVE
   *
   * @param self::MODE_* $mode
   */
  public function setMode($mode)
  {
    $this->mode = $mode;
  }
  /**
   * @return self::MODE_*
   */
  public function getMode()
  {
    return $this->mode;
  }
  /**
   * Specifies the storage class to set on objects being transferred to Google
   * Cloud Storage buckets. If unspecified, the default behavior is the same as
   * STORAGE_CLASS_DESTINATION_BUCKET_DEFAULT.
   *
   * Accepted values: STORAGE_CLASS_UNSPECIFIED,
   * STORAGE_CLASS_DESTINATION_BUCKET_DEFAULT, STORAGE_CLASS_PRESERVE,
   * STORAGE_CLASS_STANDARD, STORAGE_CLASS_NEARLINE, STORAGE_CLASS_COLDLINE,
   * STORAGE_CLASS_ARCHIVE
   *
   * @param self::STORAGE_CLASS_* $storageClass
   */
  public function setStorageClass($storageClass)
  {
    $this->storageClass = $storageClass;
  }
  /**
   * @return self::STORAGE_CLASS_*
   */
  public function getStorageClass()
  {
    return $this->storageClass;
  }
  /**
   * Specifies how symlinks should be handled by the transfer. By default,
   * symlinks are not preserved. Only applicable to transfers involving POSIX
   * file systems, and ignored for other transfers.
   *
   * Accepted values: SYMLINK_UNSPECIFIED, SYMLINK_SKIP, SYMLINK_PRESERVE
   *
   * @param self::SYMLINK_* $symlink
   */
  public function setSymlink($symlink)
  {
    $this->symlink = $symlink;
  }
  /**
   * @return self::SYMLINK_*
   */
  public function getSymlink()
  {
    return $this->symlink;
  }
  /**
   * Specifies how each object's temporary hold status should be preserved for
   * transfers between Google Cloud Storage buckets. If unspecified, the default
   * behavior is the same as TEMPORARY_HOLD_PRESERVE.
   *
   * Accepted values: TEMPORARY_HOLD_UNSPECIFIED, TEMPORARY_HOLD_SKIP,
   * TEMPORARY_HOLD_PRESERVE
   *
   * @param self::TEMPORARY_HOLD_* $temporaryHold
   */
  public function setTemporaryHold($temporaryHold)
  {
    $this->temporaryHold = $temporaryHold;
  }
  /**
   * @return self::TEMPORARY_HOLD_*
   */
  public function getTemporaryHold()
  {
    return $this->temporaryHold;
  }
  /**
   * Specifies how each object's `timeCreated` metadata is preserved for
   * transfers. If unspecified, the default behavior is the same as
   * TIME_CREATED_SKIP. This behavior is supported for transfers to Cloud
   * Storage buckets from Cloud Storage, Amazon S3, S3-compatible storage, and
   * Azure sources.
   *
   * Accepted values: TIME_CREATED_UNSPECIFIED, TIME_CREATED_SKIP,
   * TIME_CREATED_PRESERVE_AS_CUSTOM_TIME
   *
   * @param self::TIME_CREATED_* $timeCreated
   */
  public function setTimeCreated($timeCreated)
  {
    $this->timeCreated = $timeCreated;
  }
  /**
   * @return self::TIME_CREATED_*
   */
  public function getTimeCreated()
  {
    return $this->timeCreated;
  }
  /**
   * Specifies how each file's POSIX user ID (UID) attribute should be handled
   * by the transfer. By default, UID is not preserved. Only applicable to
   * transfers involving POSIX file systems, and ignored for other transfers.
   *
   * Accepted values: UID_UNSPECIFIED, UID_SKIP, UID_NUMBER
   *
   * @param self::UID_* $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return self::UID_*
   */
  public function getUid()
  {
    return $this->uid;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MetadataOptions::class, 'Google_Service_Storagetransfer_MetadataOptions');
