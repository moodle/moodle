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

namespace Google\Service\ServiceNetworking;

class Connection extends \Google\Collection
{
  protected $collection_key = 'reservedPeeringRanges';
  /**
   * Required. The name of service consumer's VPC network that's connected with
   * service producer network, in the following format:
   * `projects/{project}/global/networks/{network}`. `{project}` is a project
   * number, such as in `12345` that includes the VPC service consumer's VPC
   * network. `{network}` is the name of the service consumer's VPC network.
   *
   * @var string
   */
  public $network;
  /**
   * Output only. The name of the VPC Network Peering connection that was
   * created by the service producer.
   *
   * @var string
   */
  public $peering;
  /**
   * The name of one or more allocated IP address ranges for this service
   * producer of type `PEERING`. Note that invoking CreateConnection method with
   * a different range when connection is already established will not modify
   * already provisioned service producer subnetworks. If CreateConnection
   * method is invoked repeatedly to reconnect when peering connection had been
   * disconnected on the consumer side, leaving this field empty will restore
   * previously allocated IP ranges.
   *
   * @var string[]
   */
  public $reservedPeeringRanges;
  /**
   * Output only. The name of the peering service that's associated with this
   * connection, in the following format: `services/{service name}`.
   *
   * @var string
   */
  public $service;

  /**
   * Required. The name of service consumer's VPC network that's connected with
   * service producer network, in the following format:
   * `projects/{project}/global/networks/{network}`. `{project}` is a project
   * number, such as in `12345` that includes the VPC service consumer's VPC
   * network. `{network}` is the name of the service consumer's VPC network.
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
   * Output only. The name of the VPC Network Peering connection that was
   * created by the service producer.
   *
   * @param string $peering
   */
  public function setPeering($peering)
  {
    $this->peering = $peering;
  }
  /**
   * @return string
   */
  public function getPeering()
  {
    return $this->peering;
  }
  /**
   * The name of one or more allocated IP address ranges for this service
   * producer of type `PEERING`. Note that invoking CreateConnection method with
   * a different range when connection is already established will not modify
   * already provisioned service producer subnetworks. If CreateConnection
   * method is invoked repeatedly to reconnect when peering connection had been
   * disconnected on the consumer side, leaving this field empty will restore
   * previously allocated IP ranges.
   *
   * @param string[] $reservedPeeringRanges
   */
  public function setReservedPeeringRanges($reservedPeeringRanges)
  {
    $this->reservedPeeringRanges = $reservedPeeringRanges;
  }
  /**
   * @return string[]
   */
  public function getReservedPeeringRanges()
  {
    return $this->reservedPeeringRanges;
  }
  /**
   * Output only. The name of the peering service that's associated with this
   * connection, in the following format: `services/{service name}`.
   *
   * @param string $service
   */
  public function setService($service)
  {
    $this->service = $service;
  }
  /**
   * @return string
   */
  public function getService()
  {
    return $this->service;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Connection::class, 'Google_Service_ServiceNetworking_Connection');
