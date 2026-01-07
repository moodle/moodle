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

class GenerateSshScriptRequest extends \Google\Model
{
  /**
   * Required. Bastion VM Instance name to use or to create.
   *
   * @var string
   */
  public $vm;
  protected $vmCreationConfigType = VmCreationConfig::class;
  protected $vmCreationConfigDataType = '';
  /**
   * The port that will be open on the bastion host.
   *
   * @var int
   */
  public $vmPort;
  protected $vmSelectionConfigType = VmSelectionConfig::class;
  protected $vmSelectionConfigDataType = '';

  /**
   * Required. Bastion VM Instance name to use or to create.
   *
   * @param string $vm
   */
  public function setVm($vm)
  {
    $this->vm = $vm;
  }
  /**
   * @return string
   */
  public function getVm()
  {
    return $this->vm;
  }
  /**
   * The VM creation configuration
   *
   * @param VmCreationConfig $vmCreationConfig
   */
  public function setVmCreationConfig(VmCreationConfig $vmCreationConfig)
  {
    $this->vmCreationConfig = $vmCreationConfig;
  }
  /**
   * @return VmCreationConfig
   */
  public function getVmCreationConfig()
  {
    return $this->vmCreationConfig;
  }
  /**
   * The port that will be open on the bastion host.
   *
   * @param int $vmPort
   */
  public function setVmPort($vmPort)
  {
    $this->vmPort = $vmPort;
  }
  /**
   * @return int
   */
  public function getVmPort()
  {
    return $this->vmPort;
  }
  /**
   * The VM selection configuration
   *
   * @param VmSelectionConfig $vmSelectionConfig
   */
  public function setVmSelectionConfig(VmSelectionConfig $vmSelectionConfig)
  {
    $this->vmSelectionConfig = $vmSelectionConfig;
  }
  /**
   * @return VmSelectionConfig
   */
  public function getVmSelectionConfig()
  {
    return $this->vmSelectionConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GenerateSshScriptRequest::class, 'Google_Service_DatabaseMigrationService_GenerateSshScriptRequest');
