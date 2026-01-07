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

class ComputeEngineTargetDetails extends \Google\Collection
{
  /**
   * Unspecified conversion type.
   */
  public const BOOT_CONVERSION_BOOT_CONVERSION_UNSPECIFIED = 'BOOT_CONVERSION_UNSPECIFIED';
  /**
   * No conversion.
   */
  public const BOOT_CONVERSION_NONE = 'NONE';
  /**
   * Convert from BIOS to EFI.
   */
  public const BOOT_CONVERSION_BIOS_TO_EFI = 'BIOS_TO_EFI';
  /**
   * The boot option is unknown.
   */
  public const BOOT_OPTION_COMPUTE_ENGINE_BOOT_OPTION_UNSPECIFIED = 'COMPUTE_ENGINE_BOOT_OPTION_UNSPECIFIED';
  /**
   * The boot option is EFI.
   */
  public const BOOT_OPTION_COMPUTE_ENGINE_BOOT_OPTION_EFI = 'COMPUTE_ENGINE_BOOT_OPTION_EFI';
  /**
   * The boot option is BIOS.
   */
  public const BOOT_OPTION_COMPUTE_ENGINE_BOOT_OPTION_BIOS = 'COMPUTE_ENGINE_BOOT_OPTION_BIOS';
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
   * The license type is the default for the OS.
   */
  public const LICENSE_TYPE_COMPUTE_ENGINE_LICENSE_TYPE_DEFAULT = 'COMPUTE_ENGINE_LICENSE_TYPE_DEFAULT';
  /**
   * The license type is Pay As You Go license type.
   */
  public const LICENSE_TYPE_COMPUTE_ENGINE_LICENSE_TYPE_PAYG = 'COMPUTE_ENGINE_LICENSE_TYPE_PAYG';
  /**
   * The license type is Bring Your Own License type.
   */
  public const LICENSE_TYPE_COMPUTE_ENGINE_LICENSE_TYPE_BYOL = 'COMPUTE_ENGINE_LICENSE_TYPE_BYOL';
  protected $collection_key = 'networkTags';
  protected $adaptationModifiersType = AdaptationModifier::class;
  protected $adaptationModifiersDataType = 'array';
  /**
   * Additional licenses to assign to the VM.
   *
   * @var string[]
   */
  public $additionalLicenses;
  protected $appliedLicenseType = AppliedLicense::class;
  protected $appliedLicenseDataType = '';
  /**
   * Optional. By default the virtual machine will keep its existing boot
   * option. Setting this property will trigger an internal process which will
   * convert the virtual machine from using the existing boot option to another.
   *
   * @var string
   */
  public $bootConversion;
  /**
   * The VM Boot Option, as set in the source VM.
   *
   * @var string
   */
  public $bootOption;
  protected $computeSchedulingType = ComputeScheduling::class;
  protected $computeSchedulingDataType = '';
  /**
   * Optional. Additional replica zones of the target regional disks. If this
   * list is not empty a regional disk will be created. The first supported zone
   * would be the one stated in the zone field. The rest are taken from this
   * list. Please refer to the [regional disk creation
   * API](https://cloud.google.com/compute/docs/regions-zones/global-regional-
   * zonal-resources) for further details about regional vs zonal disks. If not
   * specified, a zonal disk will be created in the same zone the VM is created.
   *
   * @var string[]
   */
  public $diskReplicaZones;
  /**
   * The disk type to use in the VM.
   *
   * @var string
   */
  public $diskType;
  /**
   * Optional. Defines whether the instance has integrity monitoring enabled.
   *
   * @var bool
   */
  public $enableIntegrityMonitoring;
  /**
   * Optional. Defines whether the instance has vTPM enabled.
   *
   * @var bool
   */
  public $enableVtpm;
  protected $encryptionType = Encryption::class;
  protected $encryptionDataType = '';
  /**
   * The hostname to assign to the VM.
   *
   * @var string
   */
  public $hostname;
  /**
   * A map of labels to associate with the VM.
   *
   * @var string[]
   */
  public $labels;
  /**
   * The license type to use in OS adaptation.
   *
   * @var string
   */
  public $licenseType;
  /**
   * The machine type to create the VM with.
   *
   * @var string
   */
  public $machineType;
  /**
   * The machine type series to create the VM with.
   *
   * @var string
   */
  public $machineTypeSeries;
  /**
   * The metadata key/value pairs to assign to the VM.
   *
   * @var string[]
   */
  public $metadata;
  protected $networkInterfacesType = NetworkInterface::class;
  protected $networkInterfacesDataType = 'array';
  /**
   * A list of network tags to associate with the VM.
   *
   * @var string[]
   */
  public $networkTags;
  /**
   * The Google Cloud target project ID or project name.
   *
   * @var string
   */
  public $project;
  /**
   * Defines whether the instance has Secure Boot enabled. This can be set to
   * true only if the VM boot option is EFI.
   *
   * @var bool
   */
  public $secureBoot;
  /**
   * The service account to associate the VM with.
   *
   * @var string
   */
  public $serviceAccount;
  /**
   * Optional. The storage pool used for the VM disks. If specified this will be
   * the storage pool in which the disk is created. This is the full path of the
   * storage pool resource, for example: "projects/my-project/zones/us-
   * central1-a/storagePools/my-storage-pool". The storage pool must be in the
   * same project and zone as the target disks. The storage pool's type must
   * match the disk type.
   *
   * @var string
   */
  public $storagePool;
  /**
   * The name of the VM to create.
   *
   * @var string
   */
  public $vmName;
  /**
   * The zone in which to create the VM.
   *
   * @var string
   */
  public $zone;

  /**
   * Optional. Modifiers to be used as configuration of the OS adaptation
   * process.
   *
   * @param AdaptationModifier[] $adaptationModifiers
   */
  public function setAdaptationModifiers($adaptationModifiers)
  {
    $this->adaptationModifiers = $adaptationModifiers;
  }
  /**
   * @return AdaptationModifier[]
   */
  public function getAdaptationModifiers()
  {
    return $this->adaptationModifiers;
  }
  /**
   * Additional licenses to assign to the VM.
   *
   * @param string[] $additionalLicenses
   */
  public function setAdditionalLicenses($additionalLicenses)
  {
    $this->additionalLicenses = $additionalLicenses;
  }
  /**
   * @return string[]
   */
  public function getAdditionalLicenses()
  {
    return $this->additionalLicenses;
  }
  /**
   * The OS license returned from the adaptation module report.
   *
   * @param AppliedLicense $appliedLicense
   */
  public function setAppliedLicense(AppliedLicense $appliedLicense)
  {
    $this->appliedLicense = $appliedLicense;
  }
  /**
   * @return AppliedLicense
   */
  public function getAppliedLicense()
  {
    return $this->appliedLicense;
  }
  /**
   * Optional. By default the virtual machine will keep its existing boot
   * option. Setting this property will trigger an internal process which will
   * convert the virtual machine from using the existing boot option to another.
   *
   * Accepted values: BOOT_CONVERSION_UNSPECIFIED, NONE, BIOS_TO_EFI
   *
   * @param self::BOOT_CONVERSION_* $bootConversion
   */
  public function setBootConversion($bootConversion)
  {
    $this->bootConversion = $bootConversion;
  }
  /**
   * @return self::BOOT_CONVERSION_*
   */
  public function getBootConversion()
  {
    return $this->bootConversion;
  }
  /**
   * The VM Boot Option, as set in the source VM.
   *
   * Accepted values: COMPUTE_ENGINE_BOOT_OPTION_UNSPECIFIED,
   * COMPUTE_ENGINE_BOOT_OPTION_EFI, COMPUTE_ENGINE_BOOT_OPTION_BIOS
   *
   * @param self::BOOT_OPTION_* $bootOption
   */
  public function setBootOption($bootOption)
  {
    $this->bootOption = $bootOption;
  }
  /**
   * @return self::BOOT_OPTION_*
   */
  public function getBootOption()
  {
    return $this->bootOption;
  }
  /**
   * Compute instance scheduling information (if empty default is used).
   *
   * @param ComputeScheduling $computeScheduling
   */
  public function setComputeScheduling(ComputeScheduling $computeScheduling)
  {
    $this->computeScheduling = $computeScheduling;
  }
  /**
   * @return ComputeScheduling
   */
  public function getComputeScheduling()
  {
    return $this->computeScheduling;
  }
  /**
   * Optional. Additional replica zones of the target regional disks. If this
   * list is not empty a regional disk will be created. The first supported zone
   * would be the one stated in the zone field. The rest are taken from this
   * list. Please refer to the [regional disk creation
   * API](https://cloud.google.com/compute/docs/regions-zones/global-regional-
   * zonal-resources) for further details about regional vs zonal disks. If not
   * specified, a zonal disk will be created in the same zone the VM is created.
   *
   * @param string[] $diskReplicaZones
   */
  public function setDiskReplicaZones($diskReplicaZones)
  {
    $this->diskReplicaZones = $diskReplicaZones;
  }
  /**
   * @return string[]
   */
  public function getDiskReplicaZones()
  {
    return $this->diskReplicaZones;
  }
  /**
   * The disk type to use in the VM.
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
   * Optional. Defines whether the instance has integrity monitoring enabled.
   *
   * @param bool $enableIntegrityMonitoring
   */
  public function setEnableIntegrityMonitoring($enableIntegrityMonitoring)
  {
    $this->enableIntegrityMonitoring = $enableIntegrityMonitoring;
  }
  /**
   * @return bool
   */
  public function getEnableIntegrityMonitoring()
  {
    return $this->enableIntegrityMonitoring;
  }
  /**
   * Optional. Defines whether the instance has vTPM enabled.
   *
   * @param bool $enableVtpm
   */
  public function setEnableVtpm($enableVtpm)
  {
    $this->enableVtpm = $enableVtpm;
  }
  /**
   * @return bool
   */
  public function getEnableVtpm()
  {
    return $this->enableVtpm;
  }
  /**
   * Optional. The encryption to apply to the VM disks.
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
   * The hostname to assign to the VM.
   *
   * @param string $hostname
   */
  public function setHostname($hostname)
  {
    $this->hostname = $hostname;
  }
  /**
   * @return string
   */
  public function getHostname()
  {
    return $this->hostname;
  }
  /**
   * A map of labels to associate with the VM.
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
   * The license type to use in OS adaptation.
   *
   * Accepted values: COMPUTE_ENGINE_LICENSE_TYPE_DEFAULT,
   * COMPUTE_ENGINE_LICENSE_TYPE_PAYG, COMPUTE_ENGINE_LICENSE_TYPE_BYOL
   *
   * @param self::LICENSE_TYPE_* $licenseType
   */
  public function setLicenseType($licenseType)
  {
    $this->licenseType = $licenseType;
  }
  /**
   * @return self::LICENSE_TYPE_*
   */
  public function getLicenseType()
  {
    return $this->licenseType;
  }
  /**
   * The machine type to create the VM with.
   *
   * @param string $machineType
   */
  public function setMachineType($machineType)
  {
    $this->machineType = $machineType;
  }
  /**
   * @return string
   */
  public function getMachineType()
  {
    return $this->machineType;
  }
  /**
   * The machine type series to create the VM with.
   *
   * @param string $machineTypeSeries
   */
  public function setMachineTypeSeries($machineTypeSeries)
  {
    $this->machineTypeSeries = $machineTypeSeries;
  }
  /**
   * @return string
   */
  public function getMachineTypeSeries()
  {
    return $this->machineTypeSeries;
  }
  /**
   * The metadata key/value pairs to assign to the VM.
   *
   * @param string[] $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return string[]
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * List of NICs connected to this VM.
   *
   * @param NetworkInterface[] $networkInterfaces
   */
  public function setNetworkInterfaces($networkInterfaces)
  {
    $this->networkInterfaces = $networkInterfaces;
  }
  /**
   * @return NetworkInterface[]
   */
  public function getNetworkInterfaces()
  {
    return $this->networkInterfaces;
  }
  /**
   * A list of network tags to associate with the VM.
   *
   * @param string[] $networkTags
   */
  public function setNetworkTags($networkTags)
  {
    $this->networkTags = $networkTags;
  }
  /**
   * @return string[]
   */
  public function getNetworkTags()
  {
    return $this->networkTags;
  }
  /**
   * The Google Cloud target project ID or project name.
   *
   * @param string $project
   */
  public function setProject($project)
  {
    $this->project = $project;
  }
  /**
   * @return string
   */
  public function getProject()
  {
    return $this->project;
  }
  /**
   * Defines whether the instance has Secure Boot enabled. This can be set to
   * true only if the VM boot option is EFI.
   *
   * @param bool $secureBoot
   */
  public function setSecureBoot($secureBoot)
  {
    $this->secureBoot = $secureBoot;
  }
  /**
   * @return bool
   */
  public function getSecureBoot()
  {
    return $this->secureBoot;
  }
  /**
   * The service account to associate the VM with.
   *
   * @param string $serviceAccount
   */
  public function setServiceAccount($serviceAccount)
  {
    $this->serviceAccount = $serviceAccount;
  }
  /**
   * @return string
   */
  public function getServiceAccount()
  {
    return $this->serviceAccount;
  }
  /**
   * Optional. The storage pool used for the VM disks. If specified this will be
   * the storage pool in which the disk is created. This is the full path of the
   * storage pool resource, for example: "projects/my-project/zones/us-
   * central1-a/storagePools/my-storage-pool". The storage pool must be in the
   * same project and zone as the target disks. The storage pool's type must
   * match the disk type.
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
   * The name of the VM to create.
   *
   * @param string $vmName
   */
  public function setVmName($vmName)
  {
    $this->vmName = $vmName;
  }
  /**
   * @return string
   */
  public function getVmName()
  {
    return $this->vmName;
  }
  /**
   * The zone in which to create the VM.
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
class_alias(ComputeEngineTargetDetails::class, 'Google_Service_VMMigrationService_ComputeEngineTargetDetails');
