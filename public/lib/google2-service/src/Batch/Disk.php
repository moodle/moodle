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

namespace Google\Service\Batch;

class Disk extends \Google\Model
{
  /**
   * Local SSDs are available through both "SCSI" and "NVMe" interfaces. If not
   * indicated, "NVMe" will be the default one for local ssds. This field is
   * ignored for persistent disks as the interface is chosen automatically. See
   * https://cloud.google.com/compute/docs/disks/persistent-
   * disks#choose_an_interface.
   *
   * @var string
   */
  public $diskInterface;
  /**
   * URL for a VM image to use as the data source for this disk. For example,
   * the following are all valid URLs: * Specify the image by its family name:
   * projects/{project}/global/images/family/{image_family} * Specify the image
   * version: projects/{project}/global/images/{image_version} You can also use
   * Batch customized image in short names. The following image values are
   * supported for a boot disk: * `batch-debian`: use Batch Debian images. *
   * `batch-cos`: use Batch Container-Optimized images. * `batch-hpc-rocky`: use
   * Batch HPC Rocky Linux images.
   *
   * @var string
   */
  public $image;
  /**
   * Disk size in GB. **Non-Boot Disk**: If the `type` specifies a persistent
   * disk, this field is ignored if `data_source` is set as `image` or
   * `snapshot`. If the `type` specifies a local SSD, this field should be a
   * multiple of 375 GB, otherwise, the final size will be the next greater
   * multiple of 375 GB. **Boot Disk**: Batch will calculate the boot disk size
   * based on source image and task requirements if you do not speicify the
   * size. If both this field and the `boot_disk_mib` field in task spec's
   * `compute_resource` are defined, Batch will only honor this field. Also,
   * this field should be no smaller than the source disk's size when the
   * `data_source` is set as `snapshot` or `image`. For example, if you set an
   * image as the `data_source` field and the image's default disk size 30 GB,
   * you can only use this field to make the disk larger or equal to 30 GB.
   *
   * @var string
   */
  public $sizeGb;
  /**
   * Name of a snapshot used as the data source. Snapshot is not supported as
   * boot disk now.
   *
   * @var string
   */
  public $snapshot;
  /**
   * Disk type as shown in `gcloud compute disk-types list`. For example, local
   * SSD uses type "local-ssd". Persistent disks and boot disks use "pd-
   * balanced", "pd-extreme", "pd-ssd" or "pd-standard". If not specified, "pd-
   * standard" will be used as the default type for non-boot disks, "pd-
   * balanced" will be used as the default type for boot disks.
   *
   * @var string
   */
  public $type;

  /**
   * Local SSDs are available through both "SCSI" and "NVMe" interfaces. If not
   * indicated, "NVMe" will be the default one for local ssds. This field is
   * ignored for persistent disks as the interface is chosen automatically. See
   * https://cloud.google.com/compute/docs/disks/persistent-
   * disks#choose_an_interface.
   *
   * @param string $diskInterface
   */
  public function setDiskInterface($diskInterface)
  {
    $this->diskInterface = $diskInterface;
  }
  /**
   * @return string
   */
  public function getDiskInterface()
  {
    return $this->diskInterface;
  }
  /**
   * URL for a VM image to use as the data source for this disk. For example,
   * the following are all valid URLs: * Specify the image by its family name:
   * projects/{project}/global/images/family/{image_family} * Specify the image
   * version: projects/{project}/global/images/{image_version} You can also use
   * Batch customized image in short names. The following image values are
   * supported for a boot disk: * `batch-debian`: use Batch Debian images. *
   * `batch-cos`: use Batch Container-Optimized images. * `batch-hpc-rocky`: use
   * Batch HPC Rocky Linux images.
   *
   * @param string $image
   */
  public function setImage($image)
  {
    $this->image = $image;
  }
  /**
   * @return string
   */
  public function getImage()
  {
    return $this->image;
  }
  /**
   * Disk size in GB. **Non-Boot Disk**: If the `type` specifies a persistent
   * disk, this field is ignored if `data_source` is set as `image` or
   * `snapshot`. If the `type` specifies a local SSD, this field should be a
   * multiple of 375 GB, otherwise, the final size will be the next greater
   * multiple of 375 GB. **Boot Disk**: Batch will calculate the boot disk size
   * based on source image and task requirements if you do not speicify the
   * size. If both this field and the `boot_disk_mib` field in task spec's
   * `compute_resource` are defined, Batch will only honor this field. Also,
   * this field should be no smaller than the source disk's size when the
   * `data_source` is set as `snapshot` or `image`. For example, if you set an
   * image as the `data_source` field and the image's default disk size 30 GB,
   * you can only use this field to make the disk larger or equal to 30 GB.
   *
   * @param string $sizeGb
   */
  public function setSizeGb($sizeGb)
  {
    $this->sizeGb = $sizeGb;
  }
  /**
   * @return string
   */
  public function getSizeGb()
  {
    return $this->sizeGb;
  }
  /**
   * Name of a snapshot used as the data source. Snapshot is not supported as
   * boot disk now.
   *
   * @param string $snapshot
   */
  public function setSnapshot($snapshot)
  {
    $this->snapshot = $snapshot;
  }
  /**
   * @return string
   */
  public function getSnapshot()
  {
    return $this->snapshot;
  }
  /**
   * Disk type as shown in `gcloud compute disk-types list`. For example, local
   * SSD uses type "local-ssd". Persistent disks and boot disks use "pd-
   * balanced", "pd-extreme", "pd-ssd" or "pd-standard". If not specified, "pd-
   * standard" will be used as the default type for non-boot disks, "pd-
   * balanced" will be used as the default type for boot disks.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Disk::class, 'Google_Service_Batch_Disk');
