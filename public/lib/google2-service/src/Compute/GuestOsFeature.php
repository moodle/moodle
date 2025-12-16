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

class GuestOsFeature extends \Google\Model
{
  public const TYPE_BARE_METAL_LINUX_COMPATIBLE = 'BARE_METAL_LINUX_COMPATIBLE';
  public const TYPE_FEATURE_TYPE_UNSPECIFIED = 'FEATURE_TYPE_UNSPECIFIED';
  public const TYPE_GVNIC = 'GVNIC';
  public const TYPE_IDPF = 'IDPF';
  public const TYPE_MULTI_IP_SUBNET = 'MULTI_IP_SUBNET';
  public const TYPE_SECURE_BOOT = 'SECURE_BOOT';
  public const TYPE_SEV_CAPABLE = 'SEV_CAPABLE';
  public const TYPE_SEV_LIVE_MIGRATABLE = 'SEV_LIVE_MIGRATABLE';
  public const TYPE_SEV_LIVE_MIGRATABLE_V2 = 'SEV_LIVE_MIGRATABLE_V2';
  public const TYPE_SEV_SNP_CAPABLE = 'SEV_SNP_CAPABLE';
  public const TYPE_SNP_SVSM_CAPABLE = 'SNP_SVSM_CAPABLE';
  public const TYPE_TDX_CAPABLE = 'TDX_CAPABLE';
  public const TYPE_UEFI_COMPATIBLE = 'UEFI_COMPATIBLE';
  public const TYPE_VIRTIO_SCSI_MULTIQUEUE = 'VIRTIO_SCSI_MULTIQUEUE';
  public const TYPE_WINDOWS = 'WINDOWS';
  /**
   * The ID of a supported feature. To add multiple values, use commas to
   * separate values. Set to one or more of the following values:        -
   * VIRTIO_SCSI_MULTIQUEUE    - WINDOWS    - MULTI_IP_SUBNET    -
   * UEFI_COMPATIBLE    - GVNIC    - SEV_CAPABLE    - SUSPEND_RESUME_COMPATIBLE
   * - SEV_LIVE_MIGRATABLE_V2    - SEV_SNP_CAPABLE    - TDX_CAPABLE    - IDPF
   * - SNP_SVSM_CAPABLE
   *
   * For more information, see Enabling guest operating system features.
   *
   * @var string
   */
  public $type;

  /**
   * The ID of a supported feature. To add multiple values, use commas to
   * separate values. Set to one or more of the following values:        -
   * VIRTIO_SCSI_MULTIQUEUE    - WINDOWS    - MULTI_IP_SUBNET    -
   * UEFI_COMPATIBLE    - GVNIC    - SEV_CAPABLE    - SUSPEND_RESUME_COMPATIBLE
   * - SEV_LIVE_MIGRATABLE_V2    - SEV_SNP_CAPABLE    - TDX_CAPABLE    - IDPF
   * - SNP_SVSM_CAPABLE
   *
   * For more information, see Enabling guest operating system features.
   *
   * Accepted values: BARE_METAL_LINUX_COMPATIBLE, FEATURE_TYPE_UNSPECIFIED,
   * GVNIC, IDPF, MULTI_IP_SUBNET, SECURE_BOOT, SEV_CAPABLE,
   * SEV_LIVE_MIGRATABLE, SEV_LIVE_MIGRATABLE_V2, SEV_SNP_CAPABLE,
   * SNP_SVSM_CAPABLE, TDX_CAPABLE, UEFI_COMPATIBLE, VIRTIO_SCSI_MULTIQUEUE,
   * WINDOWS
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GuestOsFeature::class, 'Google_Service_Compute_GuestOsFeature');
