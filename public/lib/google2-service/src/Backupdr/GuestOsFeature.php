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

class GuestOsFeature extends \Google\Model
{
  /**
   * Default value, which is unused.
   */
  public const TYPE_FEATURE_TYPE_UNSPECIFIED = 'FEATURE_TYPE_UNSPECIFIED';
  /**
   * VIRTIO_SCSI_MULTIQUEUE feature type.
   */
  public const TYPE_VIRTIO_SCSI_MULTIQUEUE = 'VIRTIO_SCSI_MULTIQUEUE';
  /**
   * WINDOWS feature type.
   */
  public const TYPE_WINDOWS = 'WINDOWS';
  /**
   * MULTI_IP_SUBNET feature type.
   */
  public const TYPE_MULTI_IP_SUBNET = 'MULTI_IP_SUBNET';
  /**
   * UEFI_COMPATIBLE feature type.
   */
  public const TYPE_UEFI_COMPATIBLE = 'UEFI_COMPATIBLE';
  /**
   * SECURE_BOOT feature type.
   */
  public const TYPE_SECURE_BOOT = 'SECURE_BOOT';
  /**
   * GVNIC feature type.
   */
  public const TYPE_GVNIC = 'GVNIC';
  /**
   * SEV_CAPABLE feature type.
   */
  public const TYPE_SEV_CAPABLE = 'SEV_CAPABLE';
  /**
   * BARE_METAL_LINUX_COMPATIBLE feature type.
   */
  public const TYPE_BARE_METAL_LINUX_COMPATIBLE = 'BARE_METAL_LINUX_COMPATIBLE';
  /**
   * SUSPEND_RESUME_COMPATIBLE feature type.
   */
  public const TYPE_SUSPEND_RESUME_COMPATIBLE = 'SUSPEND_RESUME_COMPATIBLE';
  /**
   * SEV_LIVE_MIGRATABLE feature type.
   */
  public const TYPE_SEV_LIVE_MIGRATABLE = 'SEV_LIVE_MIGRATABLE';
  /**
   * SEV_SNP_CAPABLE feature type.
   */
  public const TYPE_SEV_SNP_CAPABLE = 'SEV_SNP_CAPABLE';
  /**
   * TDX_CAPABLE feature type.
   */
  public const TYPE_TDX_CAPABLE = 'TDX_CAPABLE';
  /**
   * IDPF feature type.
   */
  public const TYPE_IDPF = 'IDPF';
  /**
   * SEV_LIVE_MIGRATABLE_V2 feature type.
   */
  public const TYPE_SEV_LIVE_MIGRATABLE_V2 = 'SEV_LIVE_MIGRATABLE_V2';
  /**
   * The ID of a supported feature.
   *
   * @var string
   */
  public $type;

  /**
   * The ID of a supported feature.
   *
   * Accepted values: FEATURE_TYPE_UNSPECIFIED, VIRTIO_SCSI_MULTIQUEUE, WINDOWS,
   * MULTI_IP_SUBNET, UEFI_COMPATIBLE, SECURE_BOOT, GVNIC, SEV_CAPABLE,
   * BARE_METAL_LINUX_COMPATIBLE, SUSPEND_RESUME_COMPATIBLE,
   * SEV_LIVE_MIGRATABLE, SEV_SNP_CAPABLE, TDX_CAPABLE, IDPF,
   * SEV_LIVE_MIGRATABLE_V2
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
class_alias(GuestOsFeature::class, 'Google_Service_Backupdr_GuestOsFeature');
