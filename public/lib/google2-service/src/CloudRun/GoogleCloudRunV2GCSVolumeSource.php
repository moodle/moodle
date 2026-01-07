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

namespace Google\Service\CloudRun;

class GoogleCloudRunV2GCSVolumeSource extends \Google\Collection
{
  protected $collection_key = 'mountOptions';
  /**
   * Cloud Storage Bucket name.
   *
   * @var string
   */
  public $bucket;
  /**
   * A list of additional flags to pass to the gcsfuse CLI. Options should be
   * specified without the leading "--".
   *
   * @var string[]
   */
  public $mountOptions;
  /**
   * If true, the volume will be mounted as read only for all mounts.
   *
   * @var bool
   */
  public $readOnly;

  /**
   * Cloud Storage Bucket name.
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
   * A list of additional flags to pass to the gcsfuse CLI. Options should be
   * specified without the leading "--".
   *
   * @param string[] $mountOptions
   */
  public function setMountOptions($mountOptions)
  {
    $this->mountOptions = $mountOptions;
  }
  /**
   * @return string[]
   */
  public function getMountOptions()
  {
    return $this->mountOptions;
  }
  /**
   * If true, the volume will be mounted as read only for all mounts.
   *
   * @param bool $readOnly
   */
  public function setReadOnly($readOnly)
  {
    $this->readOnly = $readOnly;
  }
  /**
   * @return bool
   */
  public function getReadOnly()
  {
    return $this->readOnly;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRunV2GCSVolumeSource::class, 'Google_Service_CloudRun_GoogleCloudRunV2GCSVolumeSource');
