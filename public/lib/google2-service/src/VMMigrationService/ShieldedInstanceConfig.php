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

class ShieldedInstanceConfig extends \Google\Model
{
  /**
   * No explicit value is selected. Will use the configuration of the source (if
   * exists, otherwise the default will be false).
   */
  public const SECURE_BOOT_SECURE_BOOT_UNSPECIFIED = 'SECURE_BOOT_UNSPECIFIED';
  /**
   * Use secure boot. This can be set to true only if the image boot option is
   * EFI.
   */
  public const SECURE_BOOT_TRUE = 'TRUE';
  /**
   * Do not use secure boot.
   */
  public const SECURE_BOOT_FALSE = 'FALSE';
  /**
   * Optional. Defines whether the instance created by the machine image has
   * integrity monitoring enabled. This can be set to true only if the image
   * boot option is EFI, and vTPM is enabled.
   *
   * @var bool
   */
  public $enableIntegrityMonitoring;
  /**
   * Optional. Defines whether the instance created by the machine image has
   * vTPM enabled. This can be set to true only if the image boot option is EFI.
   *
   * @var bool
   */
  public $enableVtpm;
  /**
   * Optional. Defines whether the instance created by the machine image has
   * Secure Boot enabled. This can be set to true only if the image boot option
   * is EFI.
   *
   * @var string
   */
  public $secureBoot;

  /**
   * Optional. Defines whether the instance created by the machine image has
   * integrity monitoring enabled. This can be set to true only if the image
   * boot option is EFI, and vTPM is enabled.
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
   * Optional. Defines whether the instance created by the machine image has
   * vTPM enabled. This can be set to true only if the image boot option is EFI.
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
   * Optional. Defines whether the instance created by the machine image has
   * Secure Boot enabled. This can be set to true only if the image boot option
   * is EFI.
   *
   * Accepted values: SECURE_BOOT_UNSPECIFIED, TRUE, FALSE
   *
   * @param self::SECURE_BOOT_* $secureBoot
   */
  public function setSecureBoot($secureBoot)
  {
    $this->secureBoot = $secureBoot;
  }
  /**
   * @return self::SECURE_BOOT_*
   */
  public function getSecureBoot()
  {
    return $this->secureBoot;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ShieldedInstanceConfig::class, 'Google_Service_VMMigrationService_ShieldedInstanceConfig');
