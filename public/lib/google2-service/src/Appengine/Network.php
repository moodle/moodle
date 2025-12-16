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

namespace Google\Service\Appengine;

class Network extends \Google\Collection
{
  /**
   * Unspecified is treated as EXTERNAL.
   */
  public const INSTANCE_IP_MODE_INSTANCE_IP_MODE_UNSPECIFIED = 'INSTANCE_IP_MODE_UNSPECIFIED';
  /**
   * Instances are created with both internal and external IP addresses.
   */
  public const INSTANCE_IP_MODE_EXTERNAL = 'EXTERNAL';
  /**
   * Instances are created with internal IP addresses only.
   */
  public const INSTANCE_IP_MODE_INTERNAL = 'INTERNAL';
  protected $collection_key = 'forwardedPorts';
  /**
   * List of ports, or port pairs, to forward from the virtual machine to the
   * application container. Only applicable in the App Engine flexible
   * environment.
   *
   * @var string[]
   */
  public $forwardedPorts;
  /**
   * The IP mode for instances. Only applicable in the App Engine flexible
   * environment.
   *
   * @var string
   */
  public $instanceIpMode;
  /**
   * Tag to apply to the instance during creation. Only applicable in the App
   * Engine flexible environment.
   *
   * @var string
   */
  public $instanceTag;
  /**
   * Google Compute Engine network where the virtual machines are created.
   * Specify the short name, not the resource path.Defaults to default.
   *
   * @var string
   */
  public $name;
  /**
   * Enable session affinity. Only applicable in the App Engine flexible
   * environment.
   *
   * @var bool
   */
  public $sessionAffinity;
  /**
   * Google Cloud Platform sub-network where the virtual machines are created.
   * Specify the short name, not the resource path.If a subnetwork name is
   * specified, a network name will also be required unless it is for the
   * default network. If the network that the instance is being created in is a
   * Legacy network, then the IP address is allocated from the IPv4Range. If the
   * network that the instance is being created in is an auto Subnet Mode
   * Network, then only network name should be specified (not the
   * subnetwork_name) and the IP address is created from the IPCidrRange of the
   * subnetwork that exists in that zone for that network. If the network that
   * the instance is being created in is a custom Subnet Mode Network, then the
   * subnetwork_name must be specified and the IP address is created from the
   * IPCidrRange of the subnetwork.If specified, the subnetwork must exist in
   * the same region as the App Engine flexible environment application.
   *
   * @var string
   */
  public $subnetworkName;

  /**
   * List of ports, or port pairs, to forward from the virtual machine to the
   * application container. Only applicable in the App Engine flexible
   * environment.
   *
   * @param string[] $forwardedPorts
   */
  public function setForwardedPorts($forwardedPorts)
  {
    $this->forwardedPorts = $forwardedPorts;
  }
  /**
   * @return string[]
   */
  public function getForwardedPorts()
  {
    return $this->forwardedPorts;
  }
  /**
   * The IP mode for instances. Only applicable in the App Engine flexible
   * environment.
   *
   * Accepted values: INSTANCE_IP_MODE_UNSPECIFIED, EXTERNAL, INTERNAL
   *
   * @param self::INSTANCE_IP_MODE_* $instanceIpMode
   */
  public function setInstanceIpMode($instanceIpMode)
  {
    $this->instanceIpMode = $instanceIpMode;
  }
  /**
   * @return self::INSTANCE_IP_MODE_*
   */
  public function getInstanceIpMode()
  {
    return $this->instanceIpMode;
  }
  /**
   * Tag to apply to the instance during creation. Only applicable in the App
   * Engine flexible environment.
   *
   * @param string $instanceTag
   */
  public function setInstanceTag($instanceTag)
  {
    $this->instanceTag = $instanceTag;
  }
  /**
   * @return string
   */
  public function getInstanceTag()
  {
    return $this->instanceTag;
  }
  /**
   * Google Compute Engine network where the virtual machines are created.
   * Specify the short name, not the resource path.Defaults to default.
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
   * Enable session affinity. Only applicable in the App Engine flexible
   * environment.
   *
   * @param bool $sessionAffinity
   */
  public function setSessionAffinity($sessionAffinity)
  {
    $this->sessionAffinity = $sessionAffinity;
  }
  /**
   * @return bool
   */
  public function getSessionAffinity()
  {
    return $this->sessionAffinity;
  }
  /**
   * Google Cloud Platform sub-network where the virtual machines are created.
   * Specify the short name, not the resource path.If a subnetwork name is
   * specified, a network name will also be required unless it is for the
   * default network. If the network that the instance is being created in is a
   * Legacy network, then the IP address is allocated from the IPv4Range. If the
   * network that the instance is being created in is an auto Subnet Mode
   * Network, then only network name should be specified (not the
   * subnetwork_name) and the IP address is created from the IPCidrRange of the
   * subnetwork that exists in that zone for that network. If the network that
   * the instance is being created in is a custom Subnet Mode Network, then the
   * subnetwork_name must be specified and the IP address is created from the
   * IPCidrRange of the subnetwork.If specified, the subnetwork must exist in
   * the same region as the App Engine flexible environment application.
   *
   * @param string $subnetworkName
   */
  public function setSubnetworkName($subnetworkName)
  {
    $this->subnetworkName = $subnetworkName;
  }
  /**
   * @return string
   */
  public function getSubnetworkName()
  {
    return $this->subnetworkName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Network::class, 'Google_Service_Appengine_Network');
