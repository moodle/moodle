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

class StorageSource extends \Google\Model
{
  /**
   * Google Cloud Storage bucket containing the source (see [Bucket Name
   * Requirements](https://cloud.google.com/storage/docs/bucket-
   * naming#requirements)).
   *
   * @var string
   */
  public $bucket;
  /**
   * Google Cloud Storage generation for the object. If the generation is
   * omitted, the latest generation will be used.
   *
   * @var string
   */
  public $generation;
  /**
   * Google Cloud Storage object containing the source. This object must be a
   * gzipped archive file (`.tar.gz`) containing source to build.
   *
   * @var string
   */
  public $object;
  /**
   * When the specified storage bucket is a 1st gen function uploard url bucket,
   * this field should be set as the generated upload url for 1st gen
   * deployment.
   *
   * @var string
   */
  public $sourceUploadUrl;

  /**
   * Google Cloud Storage bucket containing the source (see [Bucket Name
   * Requirements](https://cloud.google.com/storage/docs/bucket-
   * naming#requirements)).
   *
   * @param string $bucket
   */
  public function setBucket($bucket)
  {
    $this->bucket = $bucket;
  }
  /**
   * @return string
   */
  public function getBucket()
  {
    return $this->bucket;
  }
  /**
   * Google Cloud Storage generation for the object. If the generation is
   * omitted, the latest generation will be used.
   *
   * @param string $generation
   */
  public function setGeneration($generation)
  {
    $this->generation = $generation;
  }
  /**
   * @return string
   */
  public function getGeneration()
  {
    return $this->generation;
  }
  /**
   * Google Cloud Storage object containing the source. This object must be a
   * gzipped archive file (`.tar.gz`) containing source to build.
   *
   * @param string $object
   */
  public function setObject($object)
  {
    $this->object = $object;
  }
  /**
   * @return string
   */
  public function getObject()
  {
    return $this->object;
  }
  /**
   * When the specified storage bucket is a 1st gen function uploard url bucket,
   * this field should be set as the generated upload url for 1st gen
   * deployment.
   *
   * @param string $sourceUploadUrl
   */
  public function setSourceUploadUrl($sourceUploadUrl)
  {
    $this->sourceUploadUrl = $sourceUploadUrl;
  }
  /**
   * @return string
   */
  public function getSourceUploadUrl()
  {
    return $this->sourceUploadUrl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StorageSource::class, 'Google_Service_CloudFunctions_StorageSource');
