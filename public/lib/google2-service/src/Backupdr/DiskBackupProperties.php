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

class DiskBackupProperties extends \Google\Collection
{
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
  protected $collection_key = 'replicaZones';
  /**
   * The access mode of the source disk.
   *
   * @var string
   */
  public $accessMode;
  /**
   * The architecture of the source disk. Valid values are ARM64 or X86_64.
   *
   * @var string
   */
  public $architecture;
  /**
   * A description of the source disk.
   *
   * @var string
   */
  public $description;
  /**
   * Indicates whether the source disk is using confidential compute mode.
   *
   * @var bool
   */
  public $enableConfidentialCompute;
  protected $guestOsFeatureType = GuestOsFeature::class;
  protected $guestOsFeatureDataType = 'array';
  /**
   * The labels of the source disk.
   *
   * @var string[]
   */
  public $labels;
  /**
   * A list of publicly available licenses that are applicable to this backup.
   * This is applicable if the original image had licenses attached, e.g.
   * Windows image.
   *
   * @var string[]
   */
  public $licenses;
  /**
   * The physical block size of the source disk.
   *
   * @var string
   */
  public $physicalBlockSizeBytes;
  /**
   * The number of IOPS provisioned for the source disk.
   *
   * @var string
   */
  public $provisionedIops;
  /**
   * The number of throughput provisioned for the source disk.
   *
   * @var string
   */
  public $provisionedThroughput;
  /**
   * Region and zone are mutually exclusive fields. The URL of the region of the
   * source disk.
   *
   * @var string
   */
  public $region;
  /**
   * The URL of the Zones where the source disk should be replicated.
   *
   * @var string[]
   */
  public $replicaZones;
  /**
   * Size(in GB) of the source disk.
   *
   * @var string
   */
  public $sizeGb;
  /**
   * The source disk used to create this backup.
   *
   * @var string
   */
  public $sourceDisk;
  /**
   * The storage pool of the source disk.
   *
   * @var string
   */
  public $storagePool;
  /**
   * The URL of the type of the disk.
   *
   * @var string
   */
  public $type;
  /**
   * The URL of the Zone where the source disk.
   *
   * @var string
   */
  public $zone;

  /**
   * The access mode of the source disk.
   *
   * @param string $accessMode
   */
  public function setAccessMode($accessMode)
  {
    $this->accessMode = $accessMode;
  }
  /**
   * @return string
   */
  public function getAccessMode()
  {
    return $this->accessMode;
  }
  /**
   * The architecture of the source disk. Valid values are ARM64 or X86_64.
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
   * A description of the source disk.
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
   * Indicates whether the source disk is using confidential compute mode.
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
   * A list of guest OS features that are applicable to this backup.
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
   * The labels of the source disk.
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
   * A list of publicly available licenses that are applicable to this backup.
   * This is applicable if the original image had licenses attached, e.g.
   * Windows image.
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
   * The physical block size of the source disk.
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
   * The number of IOPS provisioned for the source disk.
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
   * The number of throughput provisioned for the source disk.
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
   * Region and zone are mutually exclusive fields. The URL of the region of the
   * source disk.
   *
   * @param string $region
   */
  public function setRegion($region)
  {
    $this->region = $region;
  }
  /**
   * @return string
   */
  public function getRegion()
  {
    return $this->region;
  }
  /**
   * The URL of the Zones where the source disk should be replicated.
   *
   * @param string[] $replicaZones
   */
  public function setReplicaZones($replicaZones)
  {
    $this->replicaZones = $replicaZones;
  }
  /**
   * @return string[]
   */
  public function getReplicaZones()
  {
    return $this->replicaZones;
  }
  /**
   * Size(in GB) of the source disk.
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
   * The source disk used to create this backup.
   *
   * @param string $sourceDisk
   */
  public function setSourceDisk($sourceDisk)
  {
    $this->sourceDisk = $sourceDisk;
  }
  /**
   * @return string
   */
  public function getSourceDisk()
  {
    return $this->sourceDisk;
  }
  /**
   * The storage pool of the source disk.
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
   * The URL of the type of the disk.
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
  /**
   * The URL of the Zone where the source disk.
   *
   * @param string $zone
   */
  public function setZone($zone)
  {
    $this->zone = $zone;
  }
  /**
   * @return string
   */
  public function getZone()
  {
    return $this->zone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DiskBackupProperties::class, 'Google_Service_Backupdr_DiskBackupProperties');
