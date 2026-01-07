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

namespace Google\Service\VMMigrationService;

class ImageImport extends \Google\Collection
{
  protected $collection_key = 'recentImageImportJobs';
  /**
   * Immutable. The path to the Cloud Storage file from which the image should
   * be imported.
   *
   * @var string
   */
  public $cloudStorageUri;
  /**
   * Output only. The time the image import was created.
   *
   * @var string
   */
  public $createTime;
  protected $diskImageTargetDefaultsType = DiskImageTargetDetails::class;
  protected $diskImageTargetDefaultsDataType = '';
  protected $encryptionType = Encryption::class;
  protected $encryptionDataType = '';
  protected $machineImageTargetDefaultsType = MachineImageTargetDetails::class;
  protected $machineImageTargetDefaultsDataType = '';
  /**
   * Output only. The resource path of the ImageImport.
   *
   * @var string
   */
  public $name;
  protected $recentImageImportJobsType = ImageImportJob::class;
  protected $recentImageImportJobsDataType = 'array';

  /**
   * Immutable. The path to the Cloud Storage file from which the image should
   * be imported.
   *
   * @param string $cloudStorageUri
   */
  public function setCloudStorageUri($cloudStorageUri)
  {
    $this->cloudStorageUri = $cloudStorageUri;
  }
  /**
   * @return string
   */
  public function getCloudStorageUri()
  {
    return $this->cloudStorageUri;
  }
  /**
   * Output only. The time the image import was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Immutable. Target details for importing a disk image, will be used by
   * ImageImportJob.
   *
   * @param DiskImageTargetDetails $diskImageTargetDefaults
   */
  public function setDiskImageTargetDefaults(DiskImageTargetDetails $diskImageTargetDefaults)
  {
    $this->diskImageTargetDefaults = $diskImageTargetDefaults;
  }
  /**
   * @return DiskImageTargetDetails
   */
  public function getDiskImageTargetDefaults()
  {
    return $this->diskImageTargetDefaults;
  }
  /**
   * Immutable. The encryption details used by the image import process during
   * the image adaptation for Compute Engine.
   *
   * @param Encryption $encryption
   */
  public function setEncryption(Encryption $encryption)
  {
    $this->encryption = $encryption;
  }
  /**
   * @return Encryption
   */
  public function getEncryption()
  {
    return $this->encryption;
  }
  /**
   * Immutable. Target details for importing a machine image, will be used by
   * ImageImportJob.
   *
   * @param MachineImageTargetDetails $machineImageTargetDefaults
   */
  public function setMachineImageTargetDefaults(MachineImageTargetDetails $machineImageTargetDefaults)
  {
    $this->machineImageTargetDefaults = $machineImageTargetDefaults;
  }
  /**
   * @return MachineImageTargetDetails
   */
  public function getMachineImageTargetDefaults()
  {
    return $this->machineImageTargetDefaults;
  }
  /**
   * Output only. The resource path of the ImageImport.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. The result of the most recent runs for this ImageImport. All
   * jobs for this ImageImport can be listed via ListImageImportJobs.
   *
   * @param ImageImportJob[] $recentImageImportJobs
   */
  public function setRecentImageImportJobs($recentImageImportJobs)
  {
    $this->recentImageImportJobs = $recentImageImportJobs;
  }
  /**
   * @return ImageImportJob[]
   */
  public function getRecentImageImportJobs()
  {
    return $this->recentImageImportJobs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ImageImport::class, 'Google_Service_VMMigrationService_ImageImport');
