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

namespace Google\Service\Backupdr;

class DiskRestoreProperties extends \Google\Collection
{
  /**
   * The default AccessMode, means the disk can be attached to single instance
   * in RW mode.
   */
  public const ACCESS_MODE_READ_WRITE_SINGLE = 'READ_WRITE_SINGLE';
  /**
   * The AccessMode means the disk can be attached to multiple instances in RW
   * mode.
   */
  public const ACCESS_MODE_READ_WRITE_MANY = 'READ_WRITE_MANY';
  /**
   * The AccessMode means the disk can be attached to multiple instances in RO
   * mode.
   */
  public const ACCESS_MODE_READ_ONLY_MANY = 'READ_ONLY_MANY';
  /**
   * Default value. This value is unused.
   */
  public const ARCHITECTURE_ARCHITECTURE_UNSPECIFIED = 'ARCHITECTURE_UNSPECIFIED';
  /**
   * Disks with architecture X86_64
   */
  public const ARCHITECTURE_X86_64 = 'X86_64';
  /**
   * Disks with architecture ARM64
   */
  public const ARCHITECTURE_ARM64 = 'ARM64';
  protected $collection_key = 'resourcePolicy';
  /**
   * Optional. The access mode of the disk.
   *
   * @var string
   */
  public $accessMode;
  /**
   * Optional. The architecture of the source disk. Valid values are ARM64 or
   * X86_64.
   *
   * @var string
   */
  public $architecture;
  /**
   * Optional. An optional description of this resource. Provide this property
   * when you create the resource.
   *
   * @var string
   */
  public $description;
  protected $diskEncryptionKeyType = CustomerEncryptionKey::class;
  protected $diskEncryptionKeyDataType = '';
  /**
   * Optional. Indicates whether this disk is using confidential compute mode.
   * Encryption with a Cloud KMS key is required to enable this option.
   *
   * @var bool
   */
  public $enableConfidentialCompute;
  protected $guestOsFeatureType = GuestOsFeature::class;
  protected $guestOsFeatureDataType = 'array';
  /**
   * Optional. Labels to apply to this disk. These can be modified later using
   * setLabels method. Label values can be empty.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Optional. A list of publicly available licenses that are applicable to this
   * backup. This is applicable if the original image had licenses attached,
   * e.g. Windows image
   *
   * @var string[]
   */
  public $licenses;
  /**
   * Required. Name of the disk.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Physical block size of the persistent disk, in bytes. If not
   * present in a request, a default value is used. Currently, the supported
   * size is 4096.
   *
   * @var string
   */
  public $physicalBlockSizeBytes;
  /**
   * Optional. Indicates how many IOPS to provision for the disk. This sets the
   * number of I/O operations per second that the disk can handle.
   *
   * @var string
   */
  public $provisionedIops;
  /**
   * Optional. Indicates how much throughput to provision for the disk. This
   * sets the number of throughput MB per second that the disk can handle.
   *
   * @var string
   */
  public $provisionedThroughput;
  /**
   * Optional. Resource manager tags to be bound to the disk.
   *
   * @var string[]
   */
  public $resourceManagerTags;
  /**
   * Optional. Resource policies applied to this disk.
   *
   * @var string[]
   */
  public $resourcePolicy;
  /**
   * Required. The size of the disk in GB.
   *
   * @var string
   */
  public $sizeGb;
  /**
   * Optional. The storage pool in which the new disk is created. You can
   * provide this as a partial or full URL to the resource.
   *
   * @var string
   */
  public $storagePool;
  /**
   * Required. URL of the disk type resource describing which disk type to use
   * to create the disk.
   *
   * @var string
   */
  public $type;

  /**
   * Optional. The access mode of the disk.
   *
   * Accepted values: READ_WRITE_SINGLE, READ_WRITE_MANY, READ_ONLY_MANY
   *
   * @param self::ACCESS_MODE_* $accessMode
   */
  public function setAccessMode($accessMode)
  {
    $this->accessMode = $accessMode;
  }
  /**
   * @return self::ACCESS_MODE_*
   */
  public function getAccessMode()
  {
    return $this->accessMode;
  }
  /**
   * Optional. The architecture of the source disk. Valid values are ARM64 or
   * X86_64.
   *
   * Accepted values: ARCHITECTURE_UNSPECIFIED, X86_64, ARM64
   *
   * @param self::ARCHITECTURE_* $architecture
   */
  public function setArchitecture($architecture)
  {
    $this->architecture = $architecture;
  }
  /**
   * @return self::ARCHITECTURE_*
   */
  public function getArchitecture()
  {
    return $this->architecture;
  }
  /**
   * Optional. An optional description of this resource. Provide this property
   * when you create the resource.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Optional. Encrypts the disk using a customer-supplied encryption key or a
   * customer-managed encryption key.
   *
   * @param CustomerEncryptionKey $diskEncryptionKey
   */
  public function setDiskEncryptionKey(CustomerEncryptionKey $diskEncryptionKey)
  {
    $this->diskEncryptionKey = $diskEncryptionKey;
  }
  /**
   * @return CustomerEncryptionKey
   */
  public function getDiskEncryptionKey()
  {
    return $this->diskEncryptionKey;
  }
  /**
   * Optional. Indicates whether this disk is using confidential compute mode.
   * Encryption with a Cloud KMS key is required to enable this option.
   *
   * @param bool $enableConfidentialCompute
   */
  public function setEnableConfidentialCompute($enableConfidentialCompute)
  {
    $this->enableConfidentialCompute = $enableConfidentialCompute;
  }
  /**
   * @return bool
   */
  public function getEnableConfidentialCompute()
  {
    return $this->enableConfidentialCompute;
  }
  /**
   * Optional. A list of features to enable in the guest operating system. This
   * is applicable only for bootable images.
   *
   * @param GuestOsFeature[] $guestOsFeature
   */
  public function setGuestOsFeature($guestOsFeature)
  {
    $this->guestOsFeature = $guestOsFeature;
  }
  /**
   * @return GuestOsFeature[]
   */
  public function getGuestOsFeature()
  {
    return $this->guestOsFeature;
  }
  /**
   * Optional. Labels to apply to this disk. These can be modified later using
   * setLabels method. Label values can be empty.
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
   * Optional. A list of publicly available licenses that are applicable to this
   * backup. This is applicable if the original image had licenses attached,
   * e.g. Windows image
   *
   * @param string[] $licenses
   */
  public function setLicenses($licenses)
  {
    $this->licenses = $licenses;
  }
  /**
   * @return string[]
   */
  public function getLicenses()
  {
    return $this->licenses;
  }
  /**
   * Required. Name of the disk.
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
   * Optional. Physical block size of the persistent disk, in bytes. If not
   * present in a request, a default value is used. Currently, the supported
   * size is 4096.
   *
   * @param string $physicalBlockSizeBytes
   */
  public function setPhysicalBlockSizeBytes($physicalBlockSizeBytes)
  {
    $this->physicalBlockSizeBytes = $physicalBlockSizeBytes;
  }
  /**
   * @return string
   */
  public function getPhysicalBlockSizeBytes()
  {
    return $this->physicalBlockSizeBytes;
  }
  /**
   * Optional. Indicates how many IOPS to provision for the disk. This sets the
   * number of I/O operations per second that the disk can handle.
   *
   * @param string $provisionedIops
   */
  public function setProvisionedIops($provisionedIops)
  {
    $this->provisionedIops = $provisionedIops;
  }
  /**
   * @return string
   */
  public function getProvisionedIops()
  {
    return $this->provisionedIops;
  }
  /**
   * Optional. Indicates how much throughput to provision for the disk. This
   * sets the number of throughput MB per second that the disk can handle.
   *
   * @param string $provisionedThroughput
   */
  public function setProvisionedThroughput($provisionedThroughput)
  {
    $this->provisionedThroughput = $provisionedThroughput;
  }
  /**
   * @return string
   */
  public function getProvisionedThroughput()
  {
    return $this->provisionedThroughput;
  }
  /**
   * Optional. Resource manager tags to be bound to the disk.
   *
   * @param string[] $resourceManagerTags
   */
  public function setResourceManagerTags($resourceManagerTags)
  {
    $this->resourceManagerTags = $resourceManagerTags;
  }
  /**
   * @return string[]
   */
  public function getResourceManagerTags()
  {
    return $this->resourceManagerTags;
  }
  /**
   * Optional. Resource policies applied to this disk.
   *
   * @param string[] $resourcePolicy
   */
  public function setResourcePolicy($resourcePolicy)
  {
    $this->resourcePolicy = $resourcePolicy;
  }
  /**
   * @return string[]
   */
  public function getResourcePolicy()
  {
    return $this->resourcePolicy;
  }
  /**
   * Required. The size of the disk in GB.
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
   * Optional. The storage pool in which the new disk is created. You can
   * provide this as a partial or full URL to the resource.
   *
   * @param string $storagePool
   */
  public function setStoragePool($storagePool)
  {
    $this->storagePool = $storagePool;
  }
  /**
   * @return string
   */
  public function getStoragePool()
  {
    return $this->storagePool;
  }
  /**
   * Required. URL of the disk type resource describing which disk type to use
   * to create the disk.
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
class_alias(DiskRestoreProperties::class, 'Google_Service_Backupdr_DiskRestoreProperties');
