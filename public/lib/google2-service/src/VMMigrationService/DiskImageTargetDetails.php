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

class DiskImageTargetDetails extends \Google\Collection
{
  protected $collection_key = 'additionalLicenses';
  /**
   * Optional. Additional licenses to assign to the image. Format: https://www.g
   * oogleapis.com/compute/v1/projects/PROJECT_ID/global/licenses/LICENSE_NAME
   * Or https://www.googleapis.com/compute/beta/projects/PROJECT_ID/global/licen
   * ses/LICENSE_NAME
   *
   * @var string[]
   */
  public $additionalLicenses;
  protected $dataDiskImageImportType = DataDiskImageImport::class;
  protected $dataDiskImageImportDataType = '';
  /**
   * Optional. An optional description of the image.
   *
   * @var string
   */
  public $description;
  protected $encryptionType = Encryption::class;
  protected $encryptionDataType = '';
  /**
   * Optional. The name of the image family to which the new image belongs.
   *
   * @var string
   */
  public $familyName;
  /**
   * Required. The name of the image to be created.
   *
   * @var string
   */
  public $imageName;
  /**
   * Optional. A map of labels to associate with the image.
   *
   * @var string[]
   */
  public $labels;
  protected $osAdaptationParametersType = ImageImportOsAdaptationParameters::class;
  protected $osAdaptationParametersDataType = '';
  /**
   * Optional. Set to true to set the image storageLocations to the single
   * region of the import job. When false, the closest multi-region is selected.
   *
   * @var bool
   */
  public $singleRegionStorage;
  /**
   * Required. Reference to the TargetProject resource that represents the
   * target project in which the imported image will be created.
   *
   * @var string
   */
  public $targetProject;

  /**
   * Optional. Additional licenses to assign to the image. Format: https://www.g
   * oogleapis.com/compute/v1/projects/PROJECT_ID/global/licenses/LICENSE_NAME
   * Or https://www.googleapis.com/compute/beta/projects/PROJECT_ID/global/licen
   * ses/LICENSE_NAME
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
   * Optional. Use to skip OS adaptation process.
   *
   * @param DataDiskImageImport $dataDiskImageImport
   */
  public function setDataDiskImageImport(DataDiskImageImport $dataDiskImageImport)
  {
    $this->dataDiskImageImport = $dataDiskImageImport;
  }
  /**
   * @return DataDiskImageImport
   */
  public function getDataDiskImageImport()
  {
    return $this->dataDiskImageImport;
  }
  /**
   * Optional. An optional description of the image.
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
   * Immutable. The encryption to apply to the image.
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
   * Optional. The name of the image family to which the new image belongs.
   *
   * @param string $familyName
   */
  public function setFamilyName($familyName)
  {
    $this->familyName = $familyName;
  }
  /**
   * @return string
   */
  public function getFamilyName()
  {
    return $this->familyName;
  }
  /**
   * Required. The name of the image to be created.
   *
   * @param string $imageName
   */
  public function setImageName($imageName)
  {
    $this->imageName = $imageName;
  }
  /**
   * @return string
   */
  public function getImageName()
  {
    return $this->imageName;
  }
  /**
   * Optional. A map of labels to associate with the image.
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
   * Optional. Set to true to set the image storageLocations to the single
   * region of the import job. When false, the closest multi-region is selected.
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
   * Required. Reference to the TargetProject resource that represents the
   * target project in which the imported image will be created.
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
class_alias(DiskImageTargetDetails::class, 'Google_Service_VMMigrationService_DiskImageTargetDetails');
