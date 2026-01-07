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

class Volume extends \Google\Collection
{
  protected $collection_key = 'mountOptions';
  /**
   * Device name of an attached disk volume, which should align with a
   * device_name specified by
   * job.allocation_policy.instances[0].policy.disks[i].device_name or defined
   * by the given instance template in
   * job.allocation_policy.instances[0].instance_template.
   *
   * @var string
   */
  public $deviceName;
  protected $gcsType = GCS::class;
  protected $gcsDataType = '';
  /**
   * Mount options vary based on the type of storage volume: * For a Cloud
   * Storage bucket, all the mount options provided by the [`gcsfuse`
   * tool](https://cloud.google.com/storage/docs/gcsfuse-cli) are supported. *
   * For an existing persistent disk, all mount options provided by the [`mount`
   * command](https://man7.org/linux/man-pages/man8/mount.8.html) except writing
   * are supported. This is due to restrictions of [multi-writer
   * mode](https://cloud.google.com/compute/docs/disks/sharing-disks-between-
   * vms). * For any other disk or a Network File System (NFS), all the mount
   * options provided by the `mount` command are supported.
   *
   * @var string[]
   */
  public $mountOptions;
  /**
   * The mount path for the volume, e.g. /mnt/disks/share.
   *
   * @var string
   */
  public $mountPath;
  protected $nfsType = NFS::class;
  protected $nfsDataType = '';

  /**
   * Device name of an attached disk volume, which should align with a
   * device_name specified by
   * job.allocation_policy.instances[0].policy.disks[i].device_name or defined
   * by the given instance template in
   * job.allocation_policy.instances[0].instance_template.
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
   * A Google Cloud Storage (GCS) volume.
   *
   * @param GCS $gcs
   */
  public function setGcs(GCS $gcs)
  {
    $this->gcs = $gcs;
  }
  /**
   * @return GCS
   */
  public function getGcs()
  {
    return $this->gcs;
  }
  /**
   * Mount options vary based on the type of storage volume: * For a Cloud
   * Storage bucket, all the mount options provided by the [`gcsfuse`
   * tool](https://cloud.google.com/storage/docs/gcsfuse-cli) are supported. *
   * For an existing persistent disk, all mount options provided by the [`mount`
   * command](https://man7.org/linux/man-pages/man8/mount.8.html) except writing
   * are supported. This is due to restrictions of [multi-writer
   * mode](https://cloud.google.com/compute/docs/disks/sharing-disks-between-
   * vms). * For any other disk or a Network File System (NFS), all the mount
   * options provided by the `mount` command are supported.
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
   * The mount path for the volume, e.g. /mnt/disks/share.
   *
   * @param string $mountPath
   */
  public function setMountPath($mountPath)
  {
    $this->mountPath = $mountPath;
  }
  /**
   * @return string
   */
  public function getMountPath()
  {
    return $this->mountPath;
  }
  /**
   * A Network File System (NFS) volume. For example, a Filestore file share.
   *
   * @param NFS $nfs
   */
  public function setNfs(NFS $nfs)
  {
    $this->nfs = $nfs;
  }
  /**
   * @return NFS
   */
  public function getNfs()
  {
    return $this->nfs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Volume::class, 'Google_Service_Batch_Volume');
