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

namespace Google\Service\VMwareEngine;

class DatastoreNetwork extends \Google\Model
{
  /**
   * Optional. The number of connections of the NFS volume. Spported from
   * vsphere 8.0u1
   *
   * @var int
   */
  public $connectionCount;
  /**
   * Optional. The Maximal Transmission Unit (MTU) of the datastore. System sets
   * default MTU size. It prefers the VPC peering MTU, falling back to the VEN
   * MTU if no peering MTU is found. when detected, and falling back to the VEN
   * MTU otherwise.
   *
   * @var int
   */
  public $mtu;
  /**
   * Output only. The resource name of the network peering, used to access the
   * file share by clients on private cloud. Resource names are schemeless URIs
   * that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. e.g. projects/my-
   * project/locations/us-central1/networkPeerings/my-network-peering
   *
   * @var string
   */
  public $networkPeering;
  /**
   * Required. The resource name of the subnet Resource names are schemeless
   * URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. e.g. projects/my-
   * project/locations/us-central1/subnets/my-subnet
   *
   * @var string
   */
  public $subnet;

  /**
   * Optional. The number of connections of the NFS volume. Spported from
   * vsphere 8.0u1
   *
   * @param int $connectionCount
   */
  public function setConnectionCount($connectionCount)
  {
    $this->connectionCount = $connectionCount;
  }
  /**
   * @return int
   */
  public function getConnectionCount()
  {
    return $this->connectionCount;
  }
  /**
   * Optional. The Maximal Transmission Unit (MTU) of the datastore. System sets
   * default MTU size. It prefers the VPC peering MTU, falling back to the VEN
   * MTU if no peering MTU is found. when detected, and falling back to the VEN
   * MTU otherwise.
   *
   * @param int $mtu
   */
  public function setMtu($mtu)
  {
    $this->mtu = $mtu;
  }
  /**
   * @return int
   */
  public function getMtu()
  {
    return $this->mtu;
  }
  /**
   * Output only. The resource name of the network peering, used to access the
   * file share by clients on private cloud. Resource names are schemeless URIs
   * that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. e.g. projects/my-
   * project/locations/us-central1/networkPeerings/my-network-peering
   *
   * @param string $networkPeering
   */
  public function setNetworkPeering($networkPeering)
  {
    $this->networkPeering = $networkPeering;
  }
  /**
   * @return string
   */
  public function getNetworkPeering()
  {
    return $this->networkPeering;
  }
  /**
   * Required. The resource name of the subnet Resource names are schemeless
   * URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. e.g. projects/my-
   * project/locations/us-central1/subnets/my-subnet
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DatastoreNetwork::class, 'Google_Service_VMwareEngine_DatastoreNetwork');
