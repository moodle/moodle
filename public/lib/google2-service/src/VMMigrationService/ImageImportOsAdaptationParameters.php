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

class ImageImportOsAdaptationParameters extends \Google\Collection
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
  protected $collection_key = 'adaptationModifiers';
  protected $adaptationModifiersType = AdaptationModifier::class;
  protected $adaptationModifiersDataType = 'array';
  /**
   * Optional. By default the image will keep its existing boot option. Setting
   * this property will trigger an internal process which will convert the image
   * from using the existing boot option to another. The size of the boot disk
   * might be increased to allow the conversion
   *
   * @var string
   */
  public $bootConversion;
  /**
   * Optional. Set to true in order to generalize the imported image. The
   * generalization process enables co-existence of multiple VMs created from
   * the same image. For Windows, generalizing the image removes computer-
   * specific information such as installed drivers and the computer security
   * identifier (SID).
   *
   * @var bool
   */
  public $generalize;
  /**
   * Optional. Choose which type of license to apply to the imported image.
   *
   * @var string
   */
  public $licenseType;

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
   * Optional. By default the image will keep its existing boot option. Setting
   * this property will trigger an internal process which will convert the image
   * from using the existing boot option to another. The size of the boot disk
   * might be increased to allow the conversion
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
   * Optional. Set to true in order to generalize the imported image. The
   * generalization process enables co-existence of multiple VMs created from
   * the same image. For Windows, generalizing the image removes computer-
   * specific information such as installed drivers and the computer security
   * identifier (SID).
   *
   * @param bool $generalize
   */
  public function setGeneralize($generalize)
  {
    $this->generalize = $generalize;
  }
  /**
   * @return bool
   */
  public function getGeneralize()
  {
    return $this->generalize;
  }
  /**
   * Optional. Choose which type of license to apply to the imported image.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ImageImportOsAdaptationParameters::class, 'Google_Service_VMMigrationService_ImageImportOsAdaptationParameters');
