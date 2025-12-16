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

class AzureVmPlatformDetails extends \Google\Model
{
  /**
   * Simultaneous Multithreading status unknown.
   */
  public const HYPERTHREADING_HYPERTHREADING_STATUS_UNSPECIFIED = 'HYPERTHREADING_STATUS_UNSPECIFIED';
  /**
   * Simultaneous Multithreading is disabled or unavailable.
   */
  public const HYPERTHREADING_HYPERTHREADING_STATUS_DISABLED = 'HYPERTHREADING_STATUS_DISABLED';
  /**
   * Simultaneous Multithreading is enabled.
   */
  public const HYPERTHREADING_HYPERTHREADING_STATUS_ENABLED = 'HYPERTHREADING_STATUS_ENABLED';
  /**
   * Whether the machine is hyperthreaded.
   *
   * @var string
   */
  public $hyperthreading;
  /**
   * The location of the machine in the Azure format.
   *
   * @var string
   */
  public $location;
  /**
   * Azure platform's machine type label.
   *
   * @var string
   */
  public $machineTypeLabel;
  /**
   * Azure platform's provisioning state.
   *
   * @var string
   */
  public $provisioningState;

  /**
   * Whether the machine is hyperthreaded.
   *
   * Accepted values: HYPERTHREADING_STATUS_UNSPECIFIED,
   * HYPERTHREADING_STATUS_DISABLED, HYPERTHREADING_STATUS_ENABLED
   *
   * @param self::HYPERTHREADING_* $hyperthreading
   */
  public function setHyperthreading($hyperthreading)
  {
    $this->hyperthreading = $hyperthreading;
  }
  /**
   * @return self::HYPERTHREADING_*
   */
  public function getHyperthreading()
  {
    return $this->hyperthreading;
  }
  /**
   * The location of the machine in the Azure format.
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * Azure platform's machine type label.
   *
   * @param string $machineTypeLabel
   */
  public function setMachineTypeLabel($machineTypeLabel)
  {
    $this->machineTypeLabel = $machineTypeLabel;
  }
  /**
   * @return string
   */
  public function getMachineTypeLabel()
  {
    return $this->machineTypeLabel;
  }
  /**
   * Azure platform's provisioning state.
   *
   * @param string $provisioningState
   */
  public function setProvisioningState($provisioningState)
  {
    $this->provisioningState = $provisioningState;
  }
  /**
   * @return string
   */
  public function getProvisioningState()
  {
    return $this->provisioningState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AzureVmPlatformDetails::class, 'Google_Service_MigrationCenterAPI_AzureVmPlatformDetails');
