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

namespace Google\Service\CloudFunctions;

class GenerateUploadUrlResponse extends \Google\Model
{
  protected $storageSourceType = StorageSource::class;
  protected $storageSourceDataType = '';
  /**
   * The generated Google Cloud Storage signed URL that should be used for a
   * function source code upload. The uploaded file should be a zip archive
   * which contains a function.
   *
   * @var string
   */
  public $uploadUrl;

  /**
   * The location of the source code in the upload bucket. Once the archive is
   * uploaded using the `upload_url` use this field to set the
   * `function.build_config.source.storage_source` during CreateFunction and
   * UpdateFunction. Generation defaults to 0, as Cloud Storage provides a new
   * generation only upon uploading a new object or version of an object.
   *
   * @param StorageSource $storageSource
   */
  public function setStorageSource(StorageSource $storageSource)
  {
    $this->storageSource = $storageSource;
  }
  /**
   * @return StorageSource
   */
  public function getStorageSource()
  {
    return $this->storageSource;
  }
  /**
   * The generated Google Cloud Storage signed URL that should be used for a
   * function source code upload. The uploaded file should be a zip archive
   * which contains a function.
   *
   * @param string $uploadUrl
   */
  public function setUploadUrl($uploadUrl)
  {
    $this->uploadUrl = $uploadUrl;
  }
  /**
   * @return string
   */
  public function getUploadUrl()
  {
    return $this->uploadUrl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GenerateUploadUrlResponse::class, 'Google_Service_CloudFunctions_GenerateUploadUrlResponse');
