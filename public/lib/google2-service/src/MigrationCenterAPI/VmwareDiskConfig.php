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

namespace Google\Service\MigrationCenterAPI;

class VmwareDiskConfig extends \Google\Model
{
  /**
   * Default value.
   */
  public const BACKING_TYPE_BACKING_TYPE_UNSPECIFIED = 'BACKING_TYPE_UNSPECIFIED';
  /**
   * Flat v1.
   */
  public const BACKING_TYPE_BACKING_TYPE_FLAT_V1 = 'BACKING_TYPE_FLAT_V1';
  /**
   * Flat v2.
   */
  public const BACKING_TYPE_BACKING_TYPE_FLAT_V2 = 'BACKING_TYPE_FLAT_V2';
  /**
   * Persistent memory, also known as Non-Volatile Memory (NVM).
   */
  public const BACKING_TYPE_BACKING_TYPE_PMEM = 'BACKING_TYPE_PMEM';
  /**
   * Raw Disk Memory v1.
   */
  public const BACKING_TYPE_BACKING_TYPE_RDM_V1 = 'BACKING_TYPE_RDM_V1';
  /**
   * Raw Disk Memory v2.
   */
  public const BACKING_TYPE_BACKING_TYPE_RDM_V2 = 'BACKING_TYPE_RDM_V2';
  /**
   * SEsparse is a snapshot format introduced in vSphere 5.5 for large disks.
   */
  public const BACKING_TYPE_BACKING_TYPE_SESPARSE = 'BACKING_TYPE_SESPARSE';
  /**
   * SEsparse v1.
   */
  public const BACKING_TYPE_BACKING_TYPE_SESPARSE_V1 = 'BACKING_TYPE_SESPARSE_V1';
  /**
   * SEsparse v1.
   */
  public const BACKING_TYPE_BACKING_TYPE_SESPARSE_V2 = 'BACKING_TYPE_SESPARSE_V2';
  /**
   * Compatibility mode unspecified or unknown.
   */
  public const RDM_COMPATIBILITY_RDM_COMPATIBILITY_UNSPECIFIED = 'RDM_COMPATIBILITY_UNSPECIFIED';
  /**
   * Physical compatibility mode.
   */
  public const RDM_COMPATIBILITY_PHYSICAL_COMPATIBILITY = 'PHYSICAL_COMPATIBILITY';
  /**
   * Virtual compatibility mode.
   */
  public const RDM_COMPATIBILITY_VIRTUAL_COMPATIBILITY = 'VIRTUAL_COMPATIBILITY';
  /**
   * VMDK disk mode unspecified or unknown.
   */
  public const VMDK_MODE_VMDK_MODE_UNSPECIFIED = 'VMDK_MODE_UNSPECIFIED';
  /**
   * Dependent disk mode.
   */
  public const VMDK_MODE_DEPENDENT = 'DEPENDENT';
  /**
   * Independent - Persistent disk mode.
   */
  public const VMDK_MODE_INDEPENDENT_PERSISTENT = 'INDEPENDENT_PERSISTENT';
  /**
   * Independent - Nonpersistent disk mode.
   */
  public const VMDK_MODE_INDEPENDENT_NONPERSISTENT = 'INDEPENDENT_NONPERSISTENT';
  /**
   * VMDK backing type.
   *
   * @var string
   */
  public $backingType;
  /**
   * RDM compatibility mode.
   *
   * @var string
   */
  public $rdmCompatibility;
  /**
   * Is VMDK shared with other VMs.
   *
   * @var bool
   */
  public $shared;
  /**
   * VMDK disk mode.
   *
   * @var string
   */
  public $vmdkMode;

  /**
   * VMDK backing type.
   *
   * Accepted values: BACKING_TYPE_UNSPECIFIED, BACKING_TYPE_FLAT_V1,
   * BACKING_TYPE_FLAT_V2, BACKING_TYPE_PMEM, BACKING_TYPE_RDM_V1,
   * BACKING_TYPE_RDM_V2, BACKING_TYPE_SESPARSE, BACKING_TYPE_SESPARSE_V1,
   * BACKING_TYPE_SESPARSE_V2
   *
   * @param self::BACKING_TYPE_* $backingType
   */
  public function setBackingType($backingType)
  {
    $this->backingType = $backingType;
  }
  /**
   * @return self::BACKING_TYPE_*
   */
  public function getBackingType()
  {
    return $this->backingType;
  }
  /**
   * RDM compatibility mode.
   *
   * Accepted values: RDM_COMPATIBILITY_UNSPECIFIED, PHYSICAL_COMPATIBILITY,
   * VIRTUAL_COMPATIBILITY
   *
   * @param self::RDM_COMPATIBILITY_* $rdmCompatibility
   */
  public function setRdmCompatibility($rdmCompatibility)
  {
    $this->rdmCompatibility = $rdmCompatibility;
  }
  /**
   * @return self::RDM_COMPATIBILITY_*
   */
  public function getRdmCompatibility()
  {
    return $this->rdmCompatibility;
  }
  /**
   * Is VMDK shared with other VMs.
   *
   * @param bool $shared
   */
  public function setShared($shared)
  {
    $this->shared = $shared;
  }
  /**
   * @return bool
   */
  public function getShared()
  {
    return $this->shared;
  }
  /**
   * VMDK disk mode.
   *
   * Accepted values: VMDK_MODE_UNSPECIFIED, DEPENDENT, INDEPENDENT_PERSISTENT,
   * INDEPENDENT_NONPERSISTENT
   *
   * @param self::VMDK_MODE_* $vmdkMode
   */
  public function setVmdkMode($vmdkMode)
  {
    $this->vmdkMode = $vmdkMode;
  }
  /**
   * @return self::VMDK_MODE_*
   */
  public function getVmdkMode()
  {
    return $this->vmdkMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VmwareDiskConfig::class, 'Google_Service_MigrationCenterAPI_VmwareDiskConfig');
