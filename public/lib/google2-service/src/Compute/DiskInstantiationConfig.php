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

namespace Google\Service\Compute;

class DiskInstantiationConfig extends \Google\Model
{
  /**
   * Attach the existing disk in read-only mode. The request will fail if the
   * disk was attached in read-write mode on the source instance. Applicable to:
   * read-only disks.
   */
  public const INSTANTIATE_FROM_ATTACH_READ_ONLY = 'ATTACH_READ_ONLY';
  /**
   * Create a blank disk. The disk will be created unformatted. Applicable to:
   * additional read-write disks, local SSDs.
   */
  public const INSTANTIATE_FROM_BLANK = 'BLANK';
  /**
   * Use the custom image specified in the custom_image field. Applicable to:
   * boot disk, additional read-write disks.
   */
  public const INSTANTIATE_FROM_CUSTOM_IMAGE = 'CUSTOM_IMAGE';
  /**
   * Use the default instantiation option for the corresponding type of disk.
   * For boot disk and any other R/W disks, new custom images will be created
   * from each disk. For read-only disks, they will be attached in read-only
   * mode. Local SSD disks will be created as blank volumes.
   */
  public const INSTANTIATE_FROM_DEFAULT = 'DEFAULT';
  /**
   * Do not include the disk in the instance template. Applicable to: additional
   * read-write disks, local SSDs, read-only disks.
   */
  public const INSTANTIATE_FROM_DO_NOT_INCLUDE = 'DO_NOT_INCLUDE';
  /**
   * Use the same source image used for creation of the source instance's
   * corresponding disk. The request will fail if the source VM's disk was
   * created from a snapshot. Applicable to: boot disk, additional read-write
   * disks.
   */
  public const INSTANTIATE_FROM_SOURCE_IMAGE = 'SOURCE_IMAGE';
  /**
   * Use the same source image family used for creation of the source instance's
   * corresponding disk. The request will fail if the source image of the source
   * disk does not belong to any image family. Applicable to: boot disk,
   * additional read-write disks.
   */
  public const INSTANTIATE_FROM_SOURCE_IMAGE_FAMILY = 'SOURCE_IMAGE_FAMILY';
  /**
   * Specifies whether the disk will be auto-deleted when the instance is
   * deleted (but not when the disk is detached from the instance).
   *
   * @var bool
   */
  public $autoDelete;
  /**
   * The custom source image to be used to restore this disk when instantiating
   * this instance template.
   *
   * @var string
   */
  public $customImage;
  /**
   * Specifies the device name of the disk to which the configurations apply to.
   *
   * @var string
   */
  public $deviceName;
  /**
   * Specifies whether to include the disk and what image to use. Possible
   * values are:              - source-image: to use the same image that was
   * used to      create the source instance's corresponding disk. Applicable to
   * the boot      disk and additional read-write disks.      - source-image-
   * family: to use the same image family that      was used to create the
   * source instance's corresponding disk. Applicable      to the boot disk and
   * additional read-write disks.      - custom-image: to use a user-provided
   * image url for disk      creation. Applicable to the boot disk and
   * additional read-write      disks.     - attach-read-only: to attach a read-
   * only      disk. Applicable to read-only disks.      - do-not-include: to
   * exclude a disk from the template.      Applicable to additional read-write
   * disks, local SSDs, and read-only      disks.
   *
   * @var string
   */
  public $instantiateFrom;

  /**
   * Specifies whether the disk will be auto-deleted when the instance is
   * deleted (but not when the disk is detached from the instance).
   *
   * @param bool $autoDelete
   */
  public function setAutoDelete($autoDelete)
  {
    $this->autoDelete = $autoDelete;
  }
  /**
   * @return bool
   */
  public function getAutoDelete()
  {
    return $this->autoDelete;
  }
  /**
   * The custom source image to be used to restore this disk when instantiating
   * this instance template.
   *
   * @param string $customImage
   */
  public function setCustomImage($customImage)
  {
    $this->customImage = $customImage;
  }
  /**
   * @return string
   */
  public function getCustomImage()
  {
    return $this->customImage;
  }
  /**
   * Specifies the device name of the disk to which the configurations apply to.
   *
   * @param string $deviceName
   */
  public function setDeviceName($deviceName)
  {
    $this->deviceName = $deviceName;
  }
  /**
   * @return string
   */
  public function getDeviceName()
  {
    return $this->deviceName;
  }
  /**
   * Specifies whether to include the disk and what image to use. Possible
   * values are:              - source-image: to use the same image that was
   * used to      create the source instance's corresponding disk. Applicable to
   * the boot      disk and additional read-write disks.      - source-image-
   * family: to use the same image family that      was used to create the
   * source instance's corresponding disk. Applicable      to the boot disk and
   * additional read-write disks.      - custom-image: to use a user-provided
   * image url for disk      creation. Applicable to the boot disk and
   * additional read-write      disks.     - attach-read-only: to attach a read-
   * only      disk. Applicable to read-only disks.      - do-not-include: to
   * exclude a disk from the template.      Applicable to additional read-write
   * disks, local SSDs, and read-only      disks.
   *
   * Accepted values: ATTACH_READ_ONLY, BLANK, CUSTOM_IMAGE, DEFAULT,
   * DO_NOT_INCLUDE, SOURCE_IMAGE, SOURCE_IMAGE_FAMILY
   *
   * @param self::INSTANTIATE_FROM_* $instantiateFrom
   */
  public function setInstantiateFrom($instantiateFrom)
  {
    $this->instantiateFrom = $instantiateFrom;
  }
  /**
   * @return self::INSTANTIATE_FROM_*
   */
  public function getInstantiateFrom()
  {
    return $this->instantiateFrom;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DiskInstantiationConfig::class, 'Google_Service_Compute_DiskInstantiationConfig');
