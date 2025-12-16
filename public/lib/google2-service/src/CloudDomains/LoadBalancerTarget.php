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

namespace Google\Service\CloudDomains;

class LoadBalancerTarget extends \Google\Model
{
  public const IP_PROTOCOL_UNDEFINED = 'UNDEFINED';
  /**
   * Indicates the load balancer is accessible via TCP.
   */
  public const IP_PROTOCOL_TCP = 'TCP';
  /**
   * Indicates the load balancer is accessible via UDP.
   */
  public const IP_PROTOCOL_UDP = 'UDP';
  public const LOAD_BALANCER_TYPE_NONE = 'NONE';
  /**
   * Indicates the load balancer is a Cross-Region Application Load Balancer.
   */
  public const LOAD_BALANCER_TYPE_GLOBAL_L7ILB = 'GLOBAL_L7ILB';
  /**
   * Indicates the load balancer is a Regional Network Passthrough Load
   * Balancer.
   */
  public const LOAD_BALANCER_TYPE_REGIONAL_L4ILB = 'REGIONAL_L4ILB';
  /**
   * Indicates the load balancer is a Regional Application Load Balancer.
   */
  public const LOAD_BALANCER_TYPE_REGIONAL_L7ILB = 'REGIONAL_L7ILB';
  /**
   * The frontend IP address of the load balancer to health check.
   *
   * @var string
   */
  public $ipAddress;
  /**
   * The protocol of the load balancer to health check.
   *
   * @var string
   */
  public $ipProtocol;
  /**
   * The type of load balancer specified by this target. This value must match
   * the configuration of the load balancer located at the LoadBalancerTarget's
   * IP address, port, and region. Use the following: - *regionalL4ilb*: for a
   * regional internal passthrough Network Load Balancer. - *regionalL7ilb*: for
   * a regional internal Application Load Balancer. - *globalL7ilb*: for a
   * global internal Application Load Balancer.
   *
   * @var string
   */
  public $loadBalancerType;
  /**
   * The fully qualified URL of the network that the load balancer is attached
   * to. This should be formatted like `https://www.googleapis.com/compute/v1/pr
   * ojects/{project}/global/networks/{network}`.
   *
   * @var string
   */
  public $networkUrl;
  /**
   * The configured port of the load balancer.
   *
   * @var string
   */
  public $port;
  /**
   * The project ID in which the load balancer is located.
   *
   * @var string
   */
  public $project;
  /**
   * The region in which the load balancer is located.
   *
   * @var string
   */
  public $region;

  /**
   * The frontend IP address of the load balancer to health check.
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
   * The protocol of the load balancer to health check.
   *
   * Accepted values: UNDEFINED, TCP, UDP
   *
   * @param self::IP_PROTOCOL_* $ipProtocol
   */
  public function setIpProtocol($ipProtocol)
  {
    $this->ipProtocol = $ipProtocol;
  }
  /**
   * @return self::IP_PROTOCOL_*
   */
  public function getIpProtocol()
  {
    return $this->ipProtocol;
  }
  /**
   * The type of load balancer specified by this target. This value must match
   * the configuration of the load balancer located at the LoadBalancerTarget's
   * IP address, port, and region. Use the following: - *regionalL4ilb*: for a
   * regional internal passthrough Network Load Balancer. - *regionalL7ilb*: for
   * a regional internal Application Load Balancer. - *globalL7ilb*: for a
   * global internal Application Load Balancer.
   *
   * Accepted values: NONE, GLOBAL_L7ILB, REGIONAL_L4ILB, REGIONAL_L7ILB
   *
   * @param self::LOAD_BALANCER_TYPE_* $loadBalancerType
   */
  public function setLoadBalancerType($loadBalancerType)
  {
    $this->loadBalancerType = $loadBalancerType;
  }
  /**
   * @return self::LOAD_BALANCER_TYPE_*
   */
  public function getLoadBalancerType()
  {
    return $this->loadBalancerType;
  }
  /**
   * The fully qualified URL of the network that the load balancer is attached
   * to. This should be formatted like `https://www.googleapis.com/compute/v1/pr
   * ojects/{project}/global/networks/{network}`.
   *
   * @param string $networkUrl
   */
  public function setNetworkUrl($networkUrl)
  {
    $this->networkUrl = $networkUrl;
  }
  /**
   * @return string
   */
  public function getNetworkUrl()
  {
    return $this->networkUrl;
  }
  /**
   * The configured port of the load balancer.
   *
   * @param string $port
   */
  public function setPort($port)
  {
    $this->port = $port;
  }
  /**
   * @return string
   */
  public function getPort()
  {
    return $this->port;
  }
  /**
   * The project ID in which the load balancer is located.
   *
   * @param string $project
   */
  public function setProject($project)
  {
    $this->project = $project;
  }
  /**
   * @return string
   */
  public function getProject()
  {
    return $this->project;
  }
  /**
   * The region in which the load balancer is located.
   *
   * @param string $region
   */
  public function setRegion($region)
  {
    $this->region = $region;
  }
  /**
   * @return string
   */
  public function getRegion()
  {
    return $this->region;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LoadBalancerTarget::class, 'Google_Service_CloudDomains_LoadBalancerTarget');
