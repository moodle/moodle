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

namespace Google\Service\DatabaseMigrationService;

class VmCreationConfig extends \Google\Model
{
  /**
   * The subnet name the vm needs to be created in.
   *
   * @var string
   */
  public $subnet;
  /**
   * Required. VM instance machine type to create.
   *
   * @var string
   */
  public $vmMachineType;
  /**
   * The Google Cloud Platform zone to create the VM in.
   *
   * @var string
   */
  public $vmZone;

  /**
   * The subnet name the vm needs to be created in.
   *
   * @param string $subnet
   */
  public function setSubnet($subnet)
  {
    $this->subnet = $subnet;
  }
  /**
   * @return string
   */
  public function getSubnet()
  {
    return $this->subnet;
  }
  /**
   * Required. VM instance machine type to create.
   *
   * @param string $vmMachineType
   */
  public function setVmMachineType($vmMachineType)
  {
    $this->vmMachineType = $vmMachineType;
  }
  /**
   * @return string
   */
  public function getVmMachineType()
  {
    return $this->vmMachineType;
  }
  /**
   * The Google Cloud Platform zone to create the VM in.
   *
   * @param string $vmZone
   */
  public function setVmZone($vmZone)
  {
    $this->vmZone = $vmZone;
  }
  /**
   * @return string
   */
  public function getVmZone()
  {
    return $this->vmZone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VmCreationConfig::class, 'Google_Service_DatabaseMigrationService_VmCreationConfig');
