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

namespace Google\Service\CloudWorkstations;

class GcePersistentDisk extends \Google\Model
{
  /**
   * Optional. Type of the disk to use. Defaults to `"pd-standard"`.
   *
   * @var string
   */
  public $diskType;
  /**
   * Optional. Whether the disk is read only. If true, the disk may be shared by
   * multiple VMs and source_snapshot must be set.
   *
   * @var bool
   */
  public $readOnly;
  /**
   * Optional. Name of the disk image to use as the source for the disk. Must be
   * empty if source_snapshot is set. Updating source_image will update content
   * in the ephemeral directory after the workstation is restarted. Only file
   * systems supported by Container-Optimized OS (COS) are explicitly supported.
   * For a list of supported file systems, please refer to the [COS
   * documentation](https://cloud.google.com/container-optimized-
   * os/docs/concepts/supported-filesystems). This field is mutable.
   *
   * @var string
   */
  public $sourceImage;
  /**
   * Optional. Name of the snapshot to use as the source for the disk. Must be
   * empty if source_image is set. Must be empty if read_only is false. Updating
   * source_snapshot will update content in the ephemeral directory after the
   * workstation is restarted. Only file systems supported by Container-
   * Optimized OS (COS) are explicitly supported. For a list of supported file
   * systems, see [the filesystems available in Container-Optimized
   * OS](https://cloud.google.com/container-optimized-
   * os/docs/concepts/supported-filesystems). This field is mutable.
   *
   * @var string
   */
  public $sourceSnapshot;

  /**
   * Optional. Type of the disk to use. Defaults to `"pd-standard"`.
   *
   * @param string $diskType
   */
  public function setDiskType($diskType)
  {
    $this->diskType = $diskType;
  }
  /**
   * @return string
   */
  public function getDiskType()
  {
    return $this->diskType;
  }
  /**
   * Optional. Whether the disk is read only. If true, the disk may be shared by
   * multiple VMs and source_snapshot must be set.
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
  /**
   * Optional. Name of the disk image to use as the source for the disk. Must be
   * empty if source_snapshot is set. Updating source_image will update content
   * in the ephemeral directory after the workstation is restarted. Only file
   * systems supported by Container-Optimized OS (COS) are explicitly supported.
   * For a list of supported file systems, please refer to the [COS
   * documentation](https://cloud.google.com/container-optimized-
   * os/docs/concepts/supported-filesystems). This field is mutable.
   *
   * @param string $sourceImage
   */
  public function setSourceImage($sourceImage)
  {
    $this->sourceImage = $sourceImage;
  }
  /**
   * @return string
   */
  public function getSourceImage()
  {
    return $this->sourceImage;
  }
  /**
   * Optional. Name of the snapshot to use as the source for the disk. Must be
   * empty if source_image is set. Must be empty if read_only is false. Updating
   * source_snapshot will update content in the ephemeral directory after the
   * workstation is restarted. Only file systems supported by Container-
   * Optimized OS (COS) are explicitly supported. For a list of supported file
   * systems, see [the filesystems available in Container-Optimized
   * OS](https://cloud.google.com/container-optimized-
   * os/docs/concepts/supported-filesystems). This field is mutable.
   *
   * @param string $sourceSnapshot
   */
  public function setSourceSnapshot($sourceSnapshot)
  {
    $this->sourceSnapshot = $sourceSnapshot;
  }
  /**
   * @return string
   */
  public function getSourceSnapshot()
  {
    return $this->sourceSnapshot;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GcePersistentDisk::class, 'Google_Service_CloudWorkstations_GcePersistentDisk');
