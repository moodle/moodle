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

class GcsData extends \Google\Model
{
  /**
   * Required. Cloud Storage bucket name. Must meet [Bucket Name
   * Requirements](/storage/docs/naming#requirements).
   *
   * @var string
   */
  public $bucketName;
  /**
   * Preview. Enables the transfer of managed folders between Cloud Storage
   * buckets. Set this option on the gcs_data_source. If set to true: - Managed
   * folders in the source bucket are transferred to the destination bucket. -
   * Managed folders in the destination bucket are overwritten. Other OVERWRITE
   * options are not supported. See [Transfer Cloud Storage managed
   * folders](/storage-transfer/docs/managed-folders).
   *
   * @var bool
   */
  public $managedFolderTransferEnabled;
  /**
   * Root path to transfer objects. Must be an empty string or full path name
   * that ends with a '/'. This field is treated as an object prefix. As such,
   * it should generally not begin with a '/'. The root path value must meet
   * [Object Name Requirements](/storage/docs/naming#objectnames).
   *
   * @var string
   */
  public $path;

  /**
   * Required. Cloud Storage bucket name. Must meet [Bucket Name
   * Requirements](/storage/docs/naming#requirements).
   *
   * @param string $bucketName
   */
  public function setBucketName($bucketName)
  {
    $this->bucketName = $bucketName;
  }
  /**
   * @return string
   */
  public function getBucketName()
  {
    return $this->bucketName;
  }
  /**
   * Preview. Enables the transfer of managed folders between Cloud Storage
   * buckets. Set this option on the gcs_data_source. If set to true: - Managed
   * folders in the source bucket are transferred to the destination bucket. -
   * Managed folders in the destination bucket are overwritten. Other OVERWRITE
   * options are not supported. See [Transfer Cloud Storage managed
   * folders](/storage-transfer/docs/managed-folders).
   *
   * @param bool $managedFolderTransferEnabled
   */
  public function setManagedFolderTransferEnabled($managedFolderTransferEnabled)
  {
    $this->managedFolderTransferEnabled = $managedFolderTransferEnabled;
  }
  /**
   * @return bool
   */
  public function getManagedFolderTransferEnabled()
  {
    return $this->managedFolderTransferEnabled;
  }
  /**
   * Root path to transfer objects. Must be an empty string or full path name
   * that ends with a '/'. This field is treated as an object prefix. As such,
   * it should generally not begin with a '/'. The root path value must meet
   * [Object Name Requirements](/storage/docs/naming#objectnames).
   *
   * @param string $path
   */
  public function setPath($path)
  {
    $this->path = $path;
  }
  /**
   * @return string
   */
  public function getPath()
  {
    return $this->path;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GcsData::class, 'Google_Service_Storagetransfer_GcsData');
