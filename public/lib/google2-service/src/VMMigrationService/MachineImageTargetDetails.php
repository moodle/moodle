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

class MachineImageTargetDetails extends \Google\Collection
{
  protected $collection_key = 'tags';
  /**
   * Optional. Additional licenses to assign to the instance created by the
   * machine image. Format: https://www.googleapis.com/compute/v1/projects/PROJE
   * CT_ID/global/licenses/LICENSE_NAME Or https://www.googleapis.com/compute/be
   * ta/projects/PROJECT_ID/global/licenses/LICENSE_NAME
   *
   * @var string[]
   */
  public $additionalLicenses;
  /**
   * Optional. An optional description of the machine image.
   *
   * @var string
   */
  public $description;
  protected $encryptionType = Encryption::class;
  protected $encryptionDataType = '';
  /**
   * Optional. The labels to apply to the instance created by the machine image.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Required. The name of the machine image to be created.
   *
   * @var string
   */
  public $machineImageName;
  protected $machineImageParametersOverridesType = MachineImageParametersOverrides::class;
  protected $machineImageParametersOverridesDataType = '';
  protected $networkInterfacesType = NetworkInterface::class;
  protected $networkInterfacesDataType = 'array';
  protected $osAdaptationParametersType = ImageImportOsAdaptationParameters::class;
  protected $osAdaptationParametersDataType = '';
  protected $serviceAccountType = ServiceAccount::class;
  protected $serviceAccountDataType = '';
  protected $shieldedInstanceConfigType = ShieldedInstanceConfig::class;
  protected $shieldedInstanceConfigDataType = '';
  /**
   * Optional. Set to true to set the machine image storageLocations to the
   * single region of the import job. When false, the closest multi-region is
   * selected.
   *
   * @var bool
   */
  public $singleRegionStorage;
  protected $skipOsAdaptationType = SkipOsAdaptation::class;
  protected $skipOsAdaptationDataType = '';
  /**
   * Optional. The tags to apply to the instance created by the machine image.
   *
   * @var string[]
   */
  public $tags;
  /**
   * Required. Reference to the TargetProject resource that represents the
   * target project in which the imported machine image will be created.
   *
   * @var string
   */
  public $targetProject;

  /**
   * Optional. Additional licenses to assign to the instance created by the
   * machine image. Format: https://www.googleapis.com/compute/v1/projects/PROJE
   * CT_ID/global/licenses/LICENSE_NAME Or https://www.googleapis.com/compute/be
   * ta/projects/PROJECT_ID/global/licenses/LICENSE_NAME
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
   * Optional. An optional description of the machine image.
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
   * Immutable. The encryption to apply to the machine image. If the Image
   * Import resource has an encryption, this field must be set to the same
   * encryption key.
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
   * Optional. The labels to apply to the instance created by the machine image.
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
   * Required. The name of the machine image to be created.
   *
   * @param string $machineImageName
   */
  public function setMachineImageName($machineImageName)
  {
    $this->machineImageName = $machineImageName;
  }
  /**
   * @return string
   */
  public function getMachineImageName()
  {
    return $this->machineImageName;
  }
  /**
   * Optional. Parameters overriding decisions based on the source machine image
   * configurations.
   *
   * @param MachineImageParametersOverrides $machineImageParametersOverrides
   */
  public function setMachineImageParametersOverrides(MachineImageParametersOverrides $machineImageParametersOverrides)
  {
    $this->machineImageParametersOverrides = $machineImageParametersOverrides;
  }
  /**
   * @return MachineImageParametersOverrides
   */
  public function getMachineImageParametersOverrides()
  {
    return $this->machineImageParametersOverrides;
  }
  /**
   * Optional. The network interfaces to create with the instance created by the
   * machine image. Internal and external IP addresses, and network tiers are
   * ignored for machine image import.
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
   * Optional. Use to set the parameters relevant for the OS adaptation process.
   *
   * @param ImageImportOsAdaptationParameters $osAdaptationParameters
   */
  public function setOsAdaptationParameters(ImageImportOsAdaptationParameters $osAdaptationParameters)
  {
    $this->osAdaptationParameters = $osAdaptationParameters;
  }
  /**
   * @return ImageImportOsAdaptationParameters
   */
  public function getOsAdaptationParameters()
  {
    return $this->osAdaptationParameters;
  }
  /**
   * Optional. The service account to assign to the instance created by the
   * machine image.
   *
   * @param ServiceAccount $serviceAccount
   */
  public function setServiceAccount(ServiceAccount $serviceAccount)
  {
    $this->serviceAccount = $serviceAccount;
  }
  /**
   * @return ServiceAccount
   */
  public function getServiceAccount()
  {
    return $this->serviceAccount;
  }
  /**
   * Optional. Shielded instance configuration.
   *
   * @param ShieldedInstanceConfig $shieldedInstanceConfig
   */
  public function setShieldedInstanceConfig(ShieldedInstanceConfig $shieldedInstanceConfig)
  {
    $this->shieldedInstanceConfig = $shieldedInstanceConfig;
  }
  /**
   * @return ShieldedInstanceConfig
   */
  public function getShieldedInstanceConfig()
  {
    return $this->shieldedInstanceConfig;
  }
  /**
   * Optional. Set to true to set the machine image storageLocations to the
   * single region of the import job. When false, the closest multi-region is
   * selected.
   *
   * @param bool $singleRegionStorage
   */
  public function setSingleRegionStorage($singleRegionStorage)
  {
    $this->singleRegionStorage = $singleRegionStorage;
  }
  /**
   * @return bool
   */
  public function getSingleRegionStorage()
  {
    return $this->singleRegionStorage;
  }
  /**
   * Optional. Use to skip OS adaptation process.
   *
   * @param SkipOsAdaptation $skipOsAdaptation
   */
  public function setSkipOsAdaptation(SkipOsAdaptation $skipOsAdaptation)
  {
    $this->skipOsAdaptation = $skipOsAdaptation;
  }
  /**
   * @return SkipOsAdaptation
   */
  public function getSkipOsAdaptation()
  {
    return $this->skipOsAdaptation;
  }
  /**
   * Optional. The tags to apply to the instance created by the machine image.
   *
   * @param string[] $tags
   */
  public function setTags($tags)
  {
    $this->tags = $tags;
  }
  /**
   * @return string[]
   */
  public function getTags()
  {
    return $this->tags;
  }
  /**
   * Required. Reference to the TargetProject resource that represents the
   * target project in which the imported machine image will be created.
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
class_alias(MachineImageTargetDetails::class, 'Google_Service_VMMigrationService_MachineImageTargetDetails');
