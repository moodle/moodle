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

class GenerateTcpProxyScriptRequest extends \Google\Model
{
  /**
   * Required. The type of the Compute instance that will host the proxy.
   *
   * @var string
   */
  public $vmMachineType;
  /**
   * Required. The name of the Compute instance that will host the proxy.
   *
   * @var string
   */
  public $vmName;
  /**
   * Required. The name of the subnet the Compute instance will use for private
   * connectivity. Must be supplied in the form of
   * projects/{project}/regions/{region}/subnetworks/{subnetwork}. Note: the
   * region for the subnet must match the Compute instance region.
   *
   * @var string
   */
  public $vmSubnet;
  /**
   * Optional. The Google Cloud Platform zone to create the VM in. The fully
   * qualified name of the zone must be specified, including the region name,
   * for example "us-central1-b". If not specified, uses the "-b" zone of the
   * destination Connection Profile's region.
   *
   * @var string
   */
  public $vmZone;

  /**
   * Required. The type of the Compute instance that will host the proxy.
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
   * Required. The name of the Compute instance that will host the proxy.
   *
   * @param string $vmName
   */
  public function setVmName($vmName)
  {
    $this->vmName = $vmName;
  }
  /**
   * @return string
   */
  public function getVmName()
  {
    return $this->vmName;
  }
  /**
   * Required. The name of the subnet the Compute instance will use for private
   * connectivity. Must be supplied in the form of
   * projects/{project}/regions/{region}/subnetworks/{subnetwork}. Note: the
   * region for the subnet must match the Compute instance region.
   *
   * @param string $vmSubnet
   */
  public function setVmSubnet($vmSubnet)
  {
    $this->vmSubnet = $vmSubnet;
  }
  /**
   * @return string
   */
  public function getVmSubnet()
  {
    return $this->vmSubnet;
  }
  /**
   * Optional. The Google Cloud Platform zone to create the VM in. The fully
   * qualified name of the zone must be specified, including the region name,
   * for example "us-central1-b". If not specified, uses the "-b" zone of the
   * destination Connection Profile's region.
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
class_alias(GenerateTcpProxyScriptRequest::class, 'Google_Service_DatabaseMigrationService_GenerateTcpProxyScriptRequest');
