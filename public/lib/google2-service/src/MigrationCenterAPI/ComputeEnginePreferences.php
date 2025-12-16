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

class ComputeEnginePreferences extends \Google\Model
{
  /**
   * Unspecified (default value).
   */
  public const LICENSE_TYPE_LICENSE_TYPE_UNSPECIFIED = 'LICENSE_TYPE_UNSPECIFIED';
  /**
   * Default Google Cloud licensing plan. Licensing is charged per usage. This a
   * good value to start with.
   */
  public const LICENSE_TYPE_LICENSE_TYPE_DEFAULT = 'LICENSE_TYPE_DEFAULT';
  /**
   * Bring-your-own-license (BYOL) plan. User provides the OS license.
   */
  public const LICENSE_TYPE_LICENSE_TYPE_BRING_YOUR_OWN_LICENSE = 'LICENSE_TYPE_BRING_YOUR_OWN_LICENSE';
  /**
   * Unspecified. Fallback to default value based on context.
   */
  public const PERSISTENT_DISK_TYPE_PERSISTENT_DISK_TYPE_UNSPECIFIED = 'PERSISTENT_DISK_TYPE_UNSPECIFIED';
  /**
   * Standard HDD Persistent Disk.
   */
  public const PERSISTENT_DISK_TYPE_PERSISTENT_DISK_TYPE_STANDARD = 'PERSISTENT_DISK_TYPE_STANDARD';
  /**
   * Balanced Persistent Disk.
   */
  public const PERSISTENT_DISK_TYPE_PERSISTENT_DISK_TYPE_BALANCED = 'PERSISTENT_DISK_TYPE_BALANCED';
  /**
   * SSD Persistent Disk.
   */
  public const PERSISTENT_DISK_TYPE_PERSISTENT_DISK_TYPE_SSD = 'PERSISTENT_DISK_TYPE_SSD';
  /**
   * License type to consider when calculating costs for virtual machine
   * insights and recommendations. If unspecified, costs are calculated based on
   * the default licensing plan.
   *
   * @var string
   */
  public $licenseType;
  protected $machinePreferencesType = MachinePreferences::class;
  protected $machinePreferencesDataType = '';
  /**
   * Persistent disk type to use. If unspecified (default), all types are
   * considered, based on available usage data.
   *
   * @var string
   */
  public $persistentDiskType;

  /**
   * License type to consider when calculating costs for virtual machine
   * insights and recommendations. If unspecified, costs are calculated based on
   * the default licensing plan.
   *
   * Accepted values: LICENSE_TYPE_UNSPECIFIED, LICENSE_TYPE_DEFAULT,
   * LICENSE_TYPE_BRING_YOUR_OWN_LICENSE
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
   * Preferences concerning the machine types to consider on Compute Engine.
   *
   * @param MachinePreferences $machinePreferences
   */
  public function setMachinePreferences(MachinePreferences $machinePreferences)
  {
    $this->machinePreferences = $machinePreferences;
  }
  /**
   * @return MachinePreferences
   */
  public function getMachinePreferences()
  {
    return $this->machinePreferences;
  }
  /**
   * Persistent disk type to use. If unspecified (default), all types are
   * considered, based on available usage data.
   *
   * Accepted values: PERSISTENT_DISK_TYPE_UNSPECIFIED,
   * PERSISTENT_DISK_TYPE_STANDARD, PERSISTENT_DISK_TYPE_BALANCED,
   * PERSISTENT_DISK_TYPE_SSD
   *
   * @param self::PERSISTENT_DISK_TYPE_* $persistentDiskType
   */
  public function setPersistentDiskType($persistentDiskType)
  {
    $this->persistentDiskType = $persistentDiskType;
  }
  /**
   * @return self::PERSISTENT_DISK_TYPE_*
   */
  public function getPersistentDiskType()
  {
    return $this->persistentDiskType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ComputeEnginePreferences::class, 'Google_Service_MigrationCenterAPI_ComputeEnginePreferences');
