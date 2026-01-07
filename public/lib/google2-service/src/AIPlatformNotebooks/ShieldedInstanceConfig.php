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

namespace Google\Service\AIPlatformNotebooks;

class ShieldedInstanceConfig extends \Google\Model
{
  /**
   * Optional. Defines whether the VM instance has integrity monitoring enabled.
   * Enables monitoring and attestation of the boot integrity of the VM
   * instance. The attestation is performed against the integrity policy
   * baseline. This baseline is initially derived from the implicitly trusted
   * boot image when the VM instance is created.
   *
   * @var bool
   */
  public $enableIntegrityMonitoring;
  /**
   * Optional. Defines whether the VM instance has Secure Boot enabled. Secure
   * Boot helps ensure that the system only runs authentic software by verifying
   * the digital signature of all boot components, and halting the boot process
   * if signature verification fails. Disabled by default.
   *
   * @var bool
   */
  public $enableSecureBoot;
  /**
   * Optional. Defines whether the VM instance has the vTPM enabled.
   *
   * @var bool
   */
  public $enableVtpm;

  /**
   * Optional. Defines whether the VM instance has integrity monitoring enabled.
   * Enables monitoring and attestation of the boot integrity of the VM
   * instance. The attestation is performed against the integrity policy
   * baseline. This baseline is initially derived from the implicitly trusted
   * boot image when the VM instance is created.
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
   * Optional. Defines whether the VM instance has Secure Boot enabled. Secure
   * Boot helps ensure that the system only runs authentic software by verifying
   * the digital signature of all boot components, and halting the boot process
   * if signature verification fails. Disabled by default.
   *
   * @param bool $enableSecureBoot
   */
  public function setEnableSecureBoot($enableSecureBoot)
  {
    $this->enableSecureBoot = $enableSecureBoot;
  }
  /**
   * @return bool
   */
  public function getEnableSecureBoot()
  {
    return $this->enableSecureBoot;
  }
  /**
   * Optional. Defines whether the VM instance has the vTPM enabled.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ShieldedInstanceConfig::class, 'Google_Service_AIPlatformNotebooks_ShieldedInstanceConfig');
