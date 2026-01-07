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

class GoogleCloudRunV2EmptyDirVolumeSource extends \Google\Model
{
  /**
   * When not specified, falls back to the default implementation which is
   * currently in memory (this may change over time).
   */
  public const MEDIUM_MEDIUM_UNSPECIFIED = 'MEDIUM_UNSPECIFIED';
  /**
   * Explicitly set the EmptyDir to be in memory. Uses tmpfs.
   */
  public const MEDIUM_MEMORY = 'MEMORY';
  /**
   * The medium on which the data is stored. Acceptable values today is only
   * MEMORY or none. When none, the default will currently be backed by memory
   * but could change over time. +optional
   *
   * @var string
   */
  public $medium;
  /**
   * Limit on the storage usable by this EmptyDir volume. The size limit is also
   * applicable for memory medium. The maximum usage on memory medium EmptyDir
   * would be the minimum value between the SizeLimit specified here and the sum
   * of memory limits of all containers. The default is nil which means that the
   * limit is undefined. More info:
   * https://cloud.google.com/run/docs/configuring/in-memory-volumes#configure-
   * volume. Info in Kubernetes:
   * https://kubernetes.io/docs/concepts/storage/volumes/#emptydir
   *
   * @var string
   */
  public $sizeLimit;

  /**
   * The medium on which the data is stored. Acceptable values today is only
   * MEMORY or none. When none, the default will currently be backed by memory
   * but could change over time. +optional
   *
   * Accepted values: MEDIUM_UNSPECIFIED, MEMORY
   *
   * @param self::MEDIUM_* $medium
   */
  public function setMedium($medium)
  {
    $this->medium = $medium;
  }
  /**
   * @return self::MEDIUM_*
   */
  public function getMedium()
  {
    return $this->medium;
  }
  /**
   * Limit on the storage usable by this EmptyDir volume. The size limit is also
   * applicable for memory medium. The maximum usage on memory medium EmptyDir
   * would be the minimum value between the SizeLimit specified here and the sum
   * of memory limits of all containers. The default is nil which means that the
   * limit is undefined. More info:
   * https://cloud.google.com/run/docs/configuring/in-memory-volumes#configure-
   * volume. Info in Kubernetes:
   * https://kubernetes.io/docs/concepts/storage/volumes/#emptydir
   *
   * @param string $sizeLimit
   */
  public function setSizeLimit($sizeLimit)
  {
    $this->sizeLimit = $sizeLimit;
  }
  /**
   * @return string
   */
  public function getSizeLimit()
  {
    return $this->sizeLimit;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRunV2EmptyDirVolumeSource::class, 'Google_Service_CloudRun_GoogleCloudRunV2EmptyDirVolumeSource');
