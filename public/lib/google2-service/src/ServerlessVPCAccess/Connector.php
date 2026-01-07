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

namespace Google\Service\ServerlessVPCAccess;

class Connector extends \Google\Collection
{
  /**
   * Invalid state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Connector is deployed and ready to receive traffic.
   */
  public const STATE_READY = 'READY';
  /**
   * An Insert operation is in progress. Transient condition.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * A Delete operation is in progress. Transient condition.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Connector is in a bad state, manual deletion recommended.
   */
  public const STATE_ERROR = 'ERROR';
  /**
   * The connector is being updated.
   */
  public const STATE_UPDATING = 'UPDATING';
  protected $collection_key = 'connectedProjects';
  /**
   * Output only. List of projects using the connector.
   *
   * @var string[]
   */
  public $connectedProjects;
  /**
   * Optional. The range of internal addresses that follows RFC 4632 notation.
   * Example: `10.132.0.0/28`.
   *
   * @var string
   */
  public $ipCidrRange;
  /**
   * Machine type of VM Instance underlying connector. Default is e2-micro
   *
   * @var string
   */
  public $machineType;
  /**
   * Maximum value of instances in autoscaling group underlying the connector.
   *
   * @var int
   */
  public $maxInstances;
  /**
   * Maximum throughput of the connector in Mbps. Refers to the expected
   * throughput when using an `e2-micro` machine type. Value must be a multiple
   * of 100 from 300 through 1000. Must be higher than the value specified by
   * --min-throughput. If both max-throughput and max-instances are provided,
   * max-instances takes precedence over max-throughput. The use of `max-
   * throughput` is discouraged in favor of `max-instances`.
   *
   * @deprecated
   * @var int
   */
  public $maxThroughput;
  /**
   * Minimum value of instances in autoscaling group underlying the connector.
   *
   * @var int
   */
  public $minInstances;
  /**
   * Minimum throughput of the connector in Mbps. Refers to the expected
   * throughput when using an `e2-micro` machine type. Value must be a multiple
   * of 100 from 200 through 900. Must be lower than the value specified by
   * --max-throughput. If both min-throughput and min-instances are provided,
   * min-instances takes precedence over min-throughput. The use of `min-
   * throughput` is discouraged in favor of `min-instances`.
   *
   * @deprecated
   * @var int
   */
  public $minThroughput;
  /**
   * The resource name in the format `projects/locations/connectors`.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Name of a VPC network.
   *
   * @var string
   */
  public $network;
  /**
   * Output only. State of the VPC access connector.
   *
   * @var string
   */
  public $state;
  protected $subnetType = Subnet::class;
  protected $subnetDataType = '';

  /**
   * Output only. List of projects using the connector.
   *
   * @param string[] $connectedProjects
   */
  public function setConnectedProjects($connectedProjects)
  {
    $this->connectedProjects = $connectedProjects;
  }
  /**
   * @return string[]
   */
  public function getConnectedProjects()
  {
    return $this->connectedProjects;
  }
  /**
   * Optional. The range of internal addresses that follows RFC 4632 notation.
   * Example: `10.132.0.0/28`.
   *
   * @param string $ipCidrRange
   */
  public function setIpCidrRange($ipCidrRange)
  {
    $this->ipCidrRange = $ipCidrRange;
  }
  /**
   * @return string
   */
  public function getIpCidrRange()
  {
    return $this->ipCidrRange;
  }
  /**
   * Machine type of VM Instance underlying connector. Default is e2-micro
   *
   * @param string $machineType
   */
  public function setMachineType($machineType)
  {
    $this->machineType = $machineType;
  }
  /**
   * @return string
   */
  public function getMachineType()
  {
    return $this->machineType;
  }
  /**
   * Maximum value of instances in autoscaling group underlying the connector.
   *
   * @param int $maxInstances
   */
  public function setMaxInstances($maxInstances)
  {
    $this->maxInstances = $maxInstances;
  }
  /**
   * @return int
   */
  public function getMaxInstances()
  {
    return $this->maxInstances;
  }
  /**
   * Maximum throughput of the connector in Mbps. Refers to the expected
   * throughput when using an `e2-micro` machine type. Value must be a multiple
   * of 100 from 300 through 1000. Must be higher than the value specified by
   * --min-throughput. If both max-throughput and max-instances are provided,
   * max-instances takes precedence over max-throughput. The use of `max-
   * throughput` is discouraged in favor of `max-instances`.
   *
   * @deprecated
   * @param int $maxThroughput
   */
  public function setMaxThroughput($maxThroughput)
  {
    $this->maxThroughput = $maxThroughput;
  }
  /**
   * @deprecated
   * @return int
   */
  public function getMaxThroughput()
  {
    return $this->maxThroughput;
  }
  /**
   * Minimum value of instances in autoscaling group underlying the connector.
   *
   * @param int $minInstances
   */
  public function setMinInstances($minInstances)
  {
    $this->minInstances = $minInstances;
  }
  /**
   * @return int
   */
  public function getMinInstances()
  {
    return $this->minInstances;
  }
  /**
   * Minimum throughput of the connector in Mbps. Refers to the expected
   * throughput when using an `e2-micro` machine type. Value must be a multiple
   * of 100 from 200 through 900. Must be lower than the value specified by
   * --max-throughput. If both min-throughput and min-instances are provided,
   * min-instances takes precedence over min-throughput. The use of `min-
   * throughput` is discouraged in favor of `min-instances`.
   *
   * @deprecated
   * @param int $minThroughput
   */
  public function setMinThroughput($minThroughput)
  {
    $this->minThroughput = $minThroughput;
  }
  /**
   * @deprecated
   * @return int
   */
  public function getMinThroughput()
  {
    return $this->minThroughput;
  }
  /**
   * The resource name in the format `projects/locations/connectors`.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Optional. Name of a VPC network.
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
   * Output only. State of the VPC access connector.
   *
   * Accepted values: STATE_UNSPECIFIED, READY, CREATING, DELETING, ERROR,
   * UPDATING
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Optional. The subnet in which to house the VPC Access Connector.
   *
   * @param Subnet $subnet
   */
  public function setSubnet(Subnet $subnet)
  {
    $this->subnet = $subnet;
  }
  /**
   * @return Subnet
   */
  public function getSubnet()
  {
    return $this->subnet;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Connector::class, 'Google_Service_ServerlessVPCAccess_Connector');
