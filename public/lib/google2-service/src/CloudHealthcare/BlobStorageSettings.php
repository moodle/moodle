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

class BlobStorageSettings extends \Google\Model
{
  /**
   * If unspecified in CreateDataset, the StorageClass defaults to STANDARD. If
   * unspecified in UpdateDataset and the StorageClass is set in the field mask,
   * an InvalidRequest error is thrown.
   */
  public const BLOB_STORAGE_CLASS_BLOB_STORAGE_CLASS_UNSPECIFIED = 'BLOB_STORAGE_CLASS_UNSPECIFIED';
  /**
   * This stores the Object in Blob Standard Storage:
   * https://cloud.google.com/storage/docs/storage-classes#standard
   */
  public const BLOB_STORAGE_CLASS_STANDARD = 'STANDARD';
  /**
   * This stores the Object in Blob Nearline Storage:
   * https://cloud.google.com/storage/docs/storage-classes#nearline
   */
  public const BLOB_STORAGE_CLASS_NEARLINE = 'NEARLINE';
  /**
   * This stores the Object in Blob Coldline Storage:
   * https://cloud.google.com/storage/docs/storage-classes#coldline
   */
  public const BLOB_STORAGE_CLASS_COLDLINE = 'COLDLINE';
  /**
   * This stores the Object in Blob Archive Storage:
   * https://cloud.google.com/storage/docs/storage-classes#archive
   */
  public const BLOB_STORAGE_CLASS_ARCHIVE = 'ARCHIVE';
  /**
   * The Storage class in which the Blob data is stored.
   *
   * @var string
   */
  public $blobStorageClass;

  /**
   * The Storage class in which the Blob data is stored.
   *
   * Accepted values: BLOB_STORAGE_CLASS_UNSPECIFIED, STANDARD, NEARLINE,
   * COLDLINE, ARCHIVE
   *
   * @param self::BLOB_STORAGE_CLASS_* $blobStorageClass
   */
  public function setBlobStorageClass($blobStorageClass)
  {
    $this->blobStorageClass = $blobStorageClass;
  }
  /**
   * @return self::BLOB_STORAGE_CLASS_*
   */
  public function getBlobStorageClass()
  {
    return $this->blobStorageClass;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BlobStorageSettings::class, 'Google_Service_CloudHealthcare_BlobStorageSettings');
