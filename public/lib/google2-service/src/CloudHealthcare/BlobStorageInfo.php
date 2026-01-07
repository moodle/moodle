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

namespace Google\Service\CloudHealthcare;

class BlobStorageInfo extends \Google\Model
{
  /**
   * If unspecified in CreateDataset, the StorageClass defaults to STANDARD. If
   * unspecified in UpdateDataset and the StorageClass is set in the field mask,
   * an InvalidRequest error is thrown.
   */
  public const STORAGE_CLASS_BLOB_STORAGE_CLASS_UNSPECIFIED = 'BLOB_STORAGE_CLASS_UNSPECIFIED';
  /**
   * This stores the Object in Blob Standard Storage:
   * https://cloud.google.com/storage/docs/storage-classes#standard
   */
  public const STORAGE_CLASS_STANDARD = 'STANDARD';
  /**
   * This stores the Object in Blob Nearline Storage:
   * https://cloud.google.com/storage/docs/storage-classes#nearline
   */
  public const STORAGE_CLASS_NEARLINE = 'NEARLINE';
  /**
   * This stores the Object in Blob Coldline Storage:
   * https://cloud.google.com/storage/docs/storage-classes#coldline
   */
  public const STORAGE_CLASS_COLDLINE = 'COLDLINE';
  /**
   * This stores the Object in Blob Archive Storage:
   * https://cloud.google.com/storage/docs/storage-classes#archive
   */
  public const STORAGE_CLASS_ARCHIVE = 'ARCHIVE';
  /**
   * Size in bytes of data stored in Blob Storage.
   *
   * @var string
   */
  public $sizeBytes;
  /**
   * The storage class in which the Blob data is stored.
   *
   * @var string
   */
  public $storageClass;
  /**
   * The time at which the storage class was updated. This is used to compute
   * early deletion fees of the resource.
   *
   * @var string
   */
  public $storageClassUpdateTime;

  /**
   * Size in bytes of data stored in Blob Storage.
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
   * The storage class in which the Blob data is stored.
   *
   * Accepted values: BLOB_STORAGE_CLASS_UNSPECIFIED, STANDARD, NEARLINE,
   * COLDLINE, ARCHIVE
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
   * The time at which the storage class was updated. This is used to compute
   * early deletion fees of the resource.
   *
   * @param string $storageClassUpdateTime
   */
  public function setStorageClassUpdateTime($storageClassUpdateTime)
  {
    $this->storageClassUpdateTime = $storageClassUpdateTime;
  }
  /**
   * @return string
   */
  public function getStorageClassUpdateTime()
  {
    return $this->storageClassUpdateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BlobStorageInfo::class, 'Google_Service_CloudHealthcare_BlobStorageInfo');
