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

namespace Google\Service\SQLAdmin;

class PscAutoConnectionConfig extends \Google\Model
{
  /**
   * Optional. The consumer network of this consumer endpoint. This must be a
   * resource path that includes both the host project and the network name. For
   * example, `projects/project1/global/networks/network1`. The consumer host
   * project of this network might be different from the consumer service
   * project.
   *
   * @var string
   */
  public $consumerNetwork;
  /**
   * The connection policy status of the consumer network.
   *
   * @var string
   */
  public $consumerNetworkStatus;
  /**
   * Optional. This is the project ID of consumer service project of this
   * consumer endpoint. Optional. This is only applicable if consumer_network is
   * a shared vpc network.
   *
   * @var string
   */
  public $consumerProject;
  /**
   * The IP address of the consumer endpoint.
   *
   * @var string
   */
  public $ipAddress;
  /**
   * The connection status of the consumer endpoint.
   *
   * @var string
   */
  public $status;

  /**
   * Optional. The consumer network of this consumer endpoint. This must be a
   * resource path that includes both the host project and the network name. For
   * example, `projects/project1/global/networks/network1`. The consumer host
   * project of this network might be different from the consumer service
   * project.
   *
   * @param string $consumerNetwork
   */
  public function setConsumerNetwork($consumerNetwork)
  {
    $this->consumerNetwork = $consumerNetwork;
  }
  /**
   * @return string
   */
  public function getConsumerNetwork()
  {
    return $this->consumerNetwork;
  }
  /**
   * The connection policy status of the consumer network.
   *
   * @param string $consumerNetworkStatus
   */
  public function setConsumerNetworkStatus($consumerNetworkStatus)
  {
    $this->consumerNetworkStatus = $consumerNetworkStatus;
  }
  /**
   * @return string
   */
  public function getConsumerNetworkStatus()
  {
    return $this->consumerNetworkStatus;
  }
  /**
   * Optional. This is the project ID of consumer service project of this
   * consumer endpoint. Optional. This is only applicable if consumer_network is
   * a shared vpc network.
   *
   * @param string $consumerProject
   */
  public function setConsumerProject($consumerProject)
  {
    $this->consumerProject = $consumerProject;
  }
  /**
   * @return string
   */
  public function getConsumerProject()
  {
    return $this->consumerProject;
  }
  /**
   * The IP address of the consumer endpoint.
   *
   * @param string $ipAddress
   */
  public function setIpAddress($ipAddress)
  {
    $this->ipAddress = $ipAddress;
  }
  /**
   * @return string
   */
  public function getIpAddress()
  {
    return $this->ipAddress;
  }
  /**
   * The connection status of the consumer endpoint.
   *
   * @param string $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return string
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PscAutoConnectionConfig::class, 'Google_Service_SQLAdmin_PscAutoConnectionConfig');
