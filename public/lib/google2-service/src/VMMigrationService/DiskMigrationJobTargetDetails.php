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

class DiskMigrationJobTargetDetails extends \Google\Model
{
  protected $encryptionType = Encryption::class;
  protected $encryptionDataType = '';
  /**
   * Optional. A map of labels to associate with the disk.
   *
   * @var string[]
   */
  public $labels;
  protected $targetDiskType = ComputeEngineDisk::class;
  protected $targetDiskDataType = '';
  /**
   * Required. The name of the resource of type TargetProject which represents
   * the Compute Engine project in which to create the disk. Should be of the
   * form: projects/{project}/locations/global/targetProjects/{target-project}
   *
   * @var string
   */
  public $targetProject;

  /**
   * Optional. The encryption to apply to the disk. If the DiskMigrationJob
   * parent Source resource has an encryption, this field must be set to the
   * same encryption key.
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
   * Optional. A map of labels to associate with the disk.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Required. The target disk.
   *
   * @param ComputeEngineDisk $targetDisk
   */
  public function setTargetDisk(ComputeEngineDisk $targetDisk)
  {
    $this->targetDisk = $targetDisk;
  }
  /**
   * @return ComputeEngineDisk
   */
  public function getTargetDisk()
  {
    return $this->targetDisk;
  }
  /**
   * Required. The name of the resource of type TargetProject which represents
   * the Compute Engine project in which to create the disk. Should be of the
   * form: projects/{project}/locations/global/targetProjects/{target-project}
   *
   * @param string $targetProject
   */
  public function setTargetProject($targetProject)
  {
    $this->targetProject = $targetProject;
  }
  /**
   * @return string
   */
  public function getTargetProject()
  {
    return $this->targetProject;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DiskMigrationJobTargetDetails::class, 'Google_Service_VMMigrationService_DiskMigrationJobTargetDetails');
