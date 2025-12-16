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

class ReverseSshConnectivity extends \Google\Model
{
  /**
   * The name of the virtual machine (Compute Engine) used as the bastion server
   * for the SSH tunnel.
   *
   * @var string
   */
  public $vm;
  /**
   * Required. The IP of the virtual machine (Compute Engine) used as the
   * bastion server for the SSH tunnel.
   *
   * @var string
   */
  public $vmIp;
  /**
   * Required. The forwarding port of the virtual machine (Compute Engine) used
   * as the bastion server for the SSH tunnel.
   *
   * @var int
   */
  public $vmPort;
  /**
   * The name of the VPC to peer with the Cloud SQL private network.
   *
   * @var string
   */
  public $vpc;

  /**
   * The name of the virtual machine (Compute Engine) used as the bastion server
   * for the SSH tunnel.
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
   * Required. The IP of the virtual machine (Compute Engine) used as the
   * bastion server for the SSH tunnel.
   *
   * @param string $vmIp
   */
  public function setVmIp($vmIp)
  {
    $this->vmIp = $vmIp;
  }
  /**
   * @return string
   */
  public function getVmIp()
  {
    return $this->vmIp;
  }
  /**
   * Required. The forwarding port of the virtual machine (Compute Engine) used
   * as the bastion server for the SSH tunnel.
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
   * The name of the VPC to peer with the Cloud SQL private network.
   *
   * @param string $vpc
   */
  public function setVpc($vpc)
  {
    $this->vpc = $vpc;
  }
  /**
   * @return string
   */
  public function getVpc()
  {
    return $this->vpc;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReverseSshConnectivity::class, 'Google_Service_DatabaseMigrationService_ReverseSshConnectivity');
