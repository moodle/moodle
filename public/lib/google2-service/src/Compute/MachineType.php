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

class MachineType extends \Google\Collection
{
  /**
   * Default value indicating Architecture is not set.
   */
  public const ARCHITECTURE_ARCHITECTURE_UNSPECIFIED = 'ARCHITECTURE_UNSPECIFIED';
  /**
   * Machines with architecture ARM64
   */
  public const ARCHITECTURE_ARM64 = 'ARM64';
  /**
   * Machines with architecture X86_64
   */
  public const ARCHITECTURE_X86_64 = 'X86_64';
  protected $collection_key = 'accelerators';
  protected $acceleratorsType = MachineTypeAccelerators::class;
  protected $acceleratorsDataType = 'array';
  /**
   * [Output Only] The architecture of the machine type.
   *
   * @var string
   */
  public $architecture;
  /**
   * [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @var string
   */
  public $creationTimestamp;
  protected $deprecatedType = DeprecationStatus::class;
  protected $deprecatedDataType = '';
  /**
   * [Output Only] An optional textual description of the resource.
   *
   * @var string
   */
  public $description;
  /**
   * [Output Only] The number of virtual CPUs that are available to the
   * instance.
   *
   * @var int
   */
  public $guestCpus;
  /**
   * [Output Only] The unique identifier for the resource. This identifier is
   * defined by the server.
   *
   * @var string
   */
  public $id;
  /**
   * [Deprecated] This property is deprecated and will never be populated with
   * any relevant values.
   *
   * @var int
   */
  public $imageSpaceGb;
  /**
   * [Output Only] Whether this machine type has a shared CPU. SeeShared-core
   * machine types for more information.
   *
   * @var bool
   */
  public $isSharedCpu;
  /**
   * Output only. [Output Only] The type of the resource.
   * Alwayscompute#machineType for machine types.
   *
   * @var string
   */
  public $kind;
  /**
   * [Output Only] Maximum persistent disks allowed.
   *
   * @var int
   */
  public $maximumPersistentDisks;
  /**
   * [Output Only] Maximum total persistent disks size (GB) allowed.
   *
   * @var string
   */
  public $maximumPersistentDisksSizeGb;
  /**
   * [Output Only] The amount of physical memory available to the instance,
   * defined in MB.
   *
   * @var int
   */
  public $memoryMb;
  /**
   * [Output Only] Name of the resource.
   *
   * @var string
   */
  public $name;
  /**
   * [Output Only] Server-defined URL for the resource.
   *
   * @var string
   */
  public $selfLink;
  /**
   * [Output Only] The name of the zone where the machine type resides, such as
   * us-central1-a.
   *
   * @var string
   */
  public $zone;

  /**
   * [Output Only] A list of accelerator configurations assigned to this machine
   * type.
   *
   * @param MachineTypeAccelerators[] $accelerators
   */
  public function setAccelerators($accelerators)
  {
    $this->accelerators = $accelerators;
  }
  /**
   * @return MachineTypeAccelerators[]
   */
  public function getAccelerators()
  {
    return $this->accelerators;
  }
  /**
   * [Output Only] The architecture of the machine type.
   *
   * Accepted values: ARCHITECTURE_UNSPECIFIED, ARM64, X86_64
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
   * [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @param string $creationTimestamp
   */
  public function setCreationTimestamp($creationTimestamp)
  {
    $this->creationTimestamp = $creationTimestamp;
  }
  /**
   * @return string
   */
  public function getCreationTimestamp()
  {
    return $this->creationTimestamp;
  }
  /**
   * [Output Only] The deprecation status associated with this machine type.
   * Only applicable if the machine type is unavailable.
   *
   * @param DeprecationStatus $deprecated
   */
  public function setDeprecated(DeprecationStatus $deprecated)
  {
    $this->deprecated = $deprecated;
  }
  /**
   * @return DeprecationStatus
   */
  public function getDeprecated()
  {
    return $this->deprecated;
  }
  /**
   * [Output Only] An optional textual description of the resource.
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
   * [Output Only] The number of virtual CPUs that are available to the
   * instance.
   *
   * @param int $guestCpus
   */
  public function setGuestCpus($guestCpus)
  {
    $this->guestCpus = $guestCpus;
  }
  /**
   * @return int
   */
  public function getGuestCpus()
  {
    return $this->guestCpus;
  }
  /**
   * [Output Only] The unique identifier for the resource. This identifier is
   * defined by the server.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * [Deprecated] This property is deprecated and will never be populated with
   * any relevant values.
   *
   * @param int $imageSpaceGb
   */
  public function setImageSpaceGb($imageSpaceGb)
  {
    $this->imageSpaceGb = $imageSpaceGb;
  }
  /**
   * @return int
   */
  public function getImageSpaceGb()
  {
    return $this->imageSpaceGb;
  }
  /**
   * [Output Only] Whether this machine type has a shared CPU. SeeShared-core
   * machine types for more information.
   *
   * @param bool $isSharedCpu
   */
  public function setIsSharedCpu($isSharedCpu)
  {
    $this->isSharedCpu = $isSharedCpu;
  }
  /**
   * @return bool
   */
  public function getIsSharedCpu()
  {
    return $this->isSharedCpu;
  }
  /**
   * Output only. [Output Only] The type of the resource.
   * Alwayscompute#machineType for machine types.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * [Output Only] Maximum persistent disks allowed.
   *
   * @param int $maximumPersistentDisks
   */
  public function setMaximumPersistentDisks($maximumPersistentDisks)
  {
    $this->maximumPersistentDisks = $maximumPersistentDisks;
  }
  /**
   * @return int
   */
  public function getMaximumPersistentDisks()
  {
    return $this->maximumPersistentDisks;
  }
  /**
   * [Output Only] Maximum total persistent disks size (GB) allowed.
   *
   * @param string $maximumPersistentDisksSizeGb
   */
  public function setMaximumPersistentDisksSizeGb($maximumPersistentDisksSizeGb)
  {
    $this->maximumPersistentDisksSizeGb = $maximumPersistentDisksSizeGb;
  }
  /**
   * @return string
   */
  public function getMaximumPersistentDisksSizeGb()
  {
    return $this->maximumPersistentDisksSizeGb;
  }
  /**
   * [Output Only] The amount of physical memory available to the instance,
   * defined in MB.
   *
   * @param int $memoryMb
   */
  public function setMemoryMb($memoryMb)
  {
    $this->memoryMb = $memoryMb;
  }
  /**
   * @return int
   */
  public function getMemoryMb()
  {
    return $this->memoryMb;
  }
  /**
   * [Output Only] Name of the resource.
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
   * [Output Only] Server-defined URL for the resource.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * [Output Only] The name of the zone where the machine type resides, such as
   * us-central1-a.
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
class_alias(MachineType::class, 'Google_Service_Compute_MachineType');
