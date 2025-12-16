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

namespace Google\Service\Batch;

class NetworkInterface extends \Google\Model
{
  /**
   * The URL of an existing network resource. You can specify the network as a
   * full or partial URL. For example, the following are all valid URLs: * https
   * ://www.googleapis.com/compute/v1/projects/{project}/global/networks/{networ
   * k} * projects/{project}/global/networks/{network} *
   * global/networks/{network}
   *
   * @var string
   */
  public $network;
  /**
   * Default is false (with an external IP address). Required if no external
   * public IP address is attached to the VM. If no external public IP address,
   * additional configuration is required to allow the VM to access Google
   * Services. See https://cloud.google.com/vpc/docs/configure-private-google-
   * access and https://cloud.google.com/nat/docs/gce-example#create-nat for
   * more information.
   *
   * @var bool
   */
  public $noExternalIpAddress;
  /**
   * The URL of an existing subnetwork resource in the network. You can specify
   * the subnetwork as a full or partial URL. For example, the following are all
   * valid URLs: * https://www.googleapis.com/compute/v1/projects/{project}/regi
   * ons/{region}/subnetworks/{subnetwork} *
   * projects/{project}/regions/{region}/subnetworks/{subnetwork} *
   * regions/{region}/subnetworks/{subnetwork}
   *
   * @var string
   */
  public $subnetwork;

  /**
   * The URL of an existing network resource. You can specify the network as a
   * full or partial URL. For example, the following are all valid URLs: * https
   * ://www.googleapis.com/compute/v1/projects/{project}/global/networks/{networ
   * k} * projects/{project}/global/networks/{network} *
   * global/networks/{network}
   *
   * @param string $network
   */
  public function setNetwork($network)
  {
    $this->network = $network;
  }
  /**
   * @return string
   */
  public function getNetwork()
  {
    return $this->network;
  }
  /**
   * Default is false (with an external IP address). Required if no external
   * public IP address is attached to the VM. If no external public IP address,
   * additional configuration is required to allow the VM to access Google
   * Services. See https://cloud.google.com/vpc/docs/configure-private-google-
   * access and https://cloud.google.com/nat/docs/gce-example#create-nat for
   * more information.
   *
   * @param bool $noExternalIpAddress
   */
  public function setNoExternalIpAddress($noExternalIpAddress)
  {
    $this->noExternalIpAddress = $noExternalIpAddress;
  }
  /**
   * @return bool
   */
  public function getNoExternalIpAddress()
  {
    return $this->noExternalIpAddress;
  }
  /**
   * The URL of an existing subnetwork resource in the network. You can specify
   * the subnetwork as a full or partial URL. For example, the following are all
   * valid URLs: * https://www.googleapis.com/compute/v1/projects/{project}/regi
   * ons/{region}/subnetworks/{subnetwork} *
   * projects/{project}/regions/{region}/subnetworks/{subnetwork} *
   * regions/{region}/subnetworks/{subnetwork}
   *
   * @param string $subnetwork
   */
  public function setSubnetwork($subnetwork)
  {
    $this->subnetwork = $subnetwork;
  }
  /**
   * @return string
   */
  public function getSubnetwork()
  {
    return $this->subnetwork;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NetworkInterface::class, 'Google_Service_Batch_NetworkInterface');
