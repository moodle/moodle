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

class PersistentDiskDefaults extends \Google\Model
{
  /**
   * An unspecified disk type. Will be used as STANDARD.
   */
  public const DISK_TYPE_COMPUTE_ENGINE_DISK_TYPE_UNSPECIFIED = 'COMPUTE_ENGINE_DISK_TYPE_UNSPECIFIED';
  /**
   * A Standard disk type.
   */
  public const DISK_TYPE_COMPUTE_ENGINE_DISK_TYPE_STANDARD = 'COMPUTE_ENGINE_DISK_TYPE_STANDARD';
  /**
   * SSD hard disk type.
   */
  public const DISK_TYPE_COMPUTE_ENGINE_DISK_TYPE_SSD = 'COMPUTE_ENGINE_DISK_TYPE_SSD';
  /**
   * An alternative to SSD persistent disks that balance performance and cost.
   */
  public const DISK_TYPE_COMPUTE_ENGINE_DISK_TYPE_BALANCED = 'COMPUTE_ENGINE_DISK_TYPE_BALANCED';
  /**
   * Hyperdisk balanced disk type.
   */
  public const DISK_TYPE_COMPUTE_ENGINE_DISK_TYPE_HYPERDISK_BALANCED = 'COMPUTE_ENGINE_DISK_TYPE_HYPERDISK_BALANCED';
  /**
   * A map of labels to associate with the Persistent Disk.
   *
   * @var string[]
   */
  public $additionalLabels;
  /**
   * Optional. The name of the Persistent Disk to create.
   *
   * @var string
   */
  public $diskName;
  /**
   * The disk type to use.
   *
   * @var string
   */
  public $diskType;
  protected $encryptionType = Encryption::class;
  protected $encryptionDataType = '';
  /**
   * Required. The ordinal number of the source VM disk.
   *
   * @var int
   */
  public $sourceDiskNumber;
  protected $vmAttachmentDetailsType = VmAttachmentDetails::class;
  protected $vmAttachmentDetailsDataType = '';

  /**
   * A map of labels to associate with the Persistent Disk.
   *
   * @param string[] $additionalLabels
   */
  public function setAdditionalLabels($additionalLabels)
  {
    $this->additionalLabels = $additionalLabels;
  }
  /**
   * @return string[]
   */
  public function getAdditionalLabels()
  {
    return $this->additionalLabels;
  }
  /**
   * Optional. The name of the Persistent Disk to create.
   *
   * @param string $diskName
   */
  public function setDiskName($diskName)
  {
    $this->diskName = $diskName;
  }
  /**
   * @return string
   */
  public function getDiskName()
  {
    return $this->diskName;
  }
  /**
   * The disk type to use.
   *
   * Accepted values: COMPUTE_ENGINE_DISK_TYPE_UNSPECIFIED,
   * COMPUTE_ENGINE_DISK_TYPE_STANDARD, COMPUTE_ENGINE_DISK_TYPE_SSD,
   * COMPUTE_ENGINE_DISK_TYPE_BALANCED,
   * COMPUTE_ENGINE_DISK_TYPE_HYPERDISK_BALANCED
   *
   * @param self::DISK_TYPE_* $diskType
   */
  public function setDiskType($diskType)
  {
    $this->diskType = $diskType;
  }
  /**
   * @return self::DISK_TYPE_*
   */
  public function getDiskType()
  {
    return $this->diskType;
  }
  /**
   * Optional. The encryption to apply to the disk.
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
   * Required. The ordinal number of the source VM disk.
   *
   * @param int $sourceDiskNumber
   */
  public function setSourceDiskNumber($sourceDiskNumber)
  {
    $this->sourceDiskNumber = $sourceDiskNumber;
  }
  /**
   * @return int
   */
  public function getSourceDiskNumber()
  {
    return $this->sourceDiskNumber;
  }
  /**
   * Optional. Details for attachment of the disk to a VM. Used when the disk is
   * set to be attached to a target VM.
   *
   * @param VmAttachmentDetails $vmAttachmentDetails
   */
  public function setVmAttachmentDetails(VmAttachmentDetails $vmAttachmentDetails)
  {
    $this->vmAttachmentDetails = $vmAttachmentDetails;
  }
  /**
   * @return VmAttachmentDetails
   */
  public function getVmAttachmentDetails()
  {
    return $this->vmAttachmentDetails;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PersistentDiskDefaults::class, 'Google_Service_VMMigrationService_PersistentDiskDefaults');
