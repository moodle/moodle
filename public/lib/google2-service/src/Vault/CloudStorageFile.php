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

namespace Google\Service\Vault;

class CloudStorageFile extends \Google\Model
{
  /**
   * The name of the Cloud Storage bucket for the export file. You can use this
   * value in the Cloud Storage [JSON
   * API](https://cloud.google.com/storage/docs/json_api) or [XML
   * API](https://cloud.google.com/storage/docs/xml-api), but not to list the
   * bucket contents. Instead, you can [get individual export
   * files](https://cloud.google.com/storage/docs/json_api/v1/objects/get) by
   * object name.
   *
   * @var string
   */
  public $bucketName;
  /**
   * The md5 hash of the file.
   *
   * @var string
   */
  public $md5Hash;
  /**
   * The name of the Cloud Storage object for the export file. You can use this
   * value in the Cloud Storage [JSON
   * API](https://cloud.google.com/storage/docs/json_api) or [XML
   * API](https://cloud.google.com/storage/docs/xml-api).
   *
   * @var string
   */
  public $objectName;
  /**
   * The export file size.
   *
   * @var string
   */
  public $size;

  /**
   * The name of the Cloud Storage bucket for the export file. You can use this
   * value in the Cloud Storage [JSON
   * API](https://cloud.google.com/storage/docs/json_api) or [XML
   * API](https://cloud.google.com/storage/docs/xml-api), but not to list the
   * bucket contents. Instead, you can [get individual export
   * files](https://cloud.google.com/storage/docs/json_api/v1/objects/get) by
   * object name.
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
   * The md5 hash of the file.
   *
   * @param string $md5Hash
   */
  public function setMd5Hash($md5Hash)
  {
    $this->md5Hash = $md5Hash;
  }
  /**
   * @return string
   */
  public function getMd5Hash()
  {
    return $this->md5Hash;
  }
  /**
   * The name of the Cloud Storage object for the export file. You can use this
   * value in the Cloud Storage [JSON
   * API](https://cloud.google.com/storage/docs/json_api) or [XML
   * API](https://cloud.google.com/storage/docs/xml-api).
   *
   * @param string $objectName
   */
  public function setObjectName($objectName)
  {
    $this->objectName = $objectName;
  }
  /**
   * @return string
   */
  public function getObjectName()
  {
    return $this->objectName;
  }
  /**
   * The export file size.
   *
   * @param string $size
   */
  public function setSize($size)
  {
    $this->size = $size;
  }
  /**
   * @return string
   */
  public function getSize()
  {
    return $this->size;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudStorageFile::class, 'Google_Service_Vault_CloudStorageFile');
