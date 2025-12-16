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

namespace Google\Service\Networkconnectivity;

class CheckConsumerConfigRequest extends \Google\Model
{
  /**
   * Default value. We will use IPv4 or IPv6 depending on the IP version of
   * first available subnetwork.
   */
  public const REQUESTED_IP_VERSION_IP_VERSION_UNSPECIFIED = 'IP_VERSION_UNSPECIFIED';
  /**
   * Will use IPv4 only.
   */
  public const REQUESTED_IP_VERSION_IPV4 = 'IPV4';
  /**
   * Will use IPv6 only.
   */
  public const REQUESTED_IP_VERSION_IPV6 = 'IPV6';
  /**
   * Required. Full resource name of the consumer network. Example: -
   * projects/{project}/global/networks/{network}.
   *
   * @var string
   */
  public $consumerNetwork;
  /**
   * The project number or ID where the PSC endpoint is to be created.
   *
   * @var string
   */
  public $endpointProject;
  /**
   * The requested IP Version
   *
   * @var string
   */
  public $requestedIpVersion;
  /**
   * Required. The service class identifier of the producer.
   *
   * @var string
   */
  public $serviceClass;

  /**
   * Required. Full resource name of the consumer network. Example: -
   * projects/{project}/global/networks/{network}.
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
   * The project number or ID where the PSC endpoint is to be created.
   *
   * @param string $endpointProject
   */
  public function setEndpointProject($endpointProject)
  {
    $this->endpointProject = $endpointProject;
  }
  /**
   * @return string
   */
  public function getEndpointProject()
  {
    return $this->endpointProject;
  }
  /**
   * The requested IP Version
   *
   * Accepted values: IP_VERSION_UNSPECIFIED, IPV4, IPV6
   *
   * @param self::REQUESTED_IP_VERSION_* $requestedIpVersion
   */
  public function setRequestedIpVersion($requestedIpVersion)
  {
    $this->requestedIpVersion = $requestedIpVersion;
  }
  /**
   * @return self::REQUESTED_IP_VERSION_*
   */
  public function getRequestedIpVersion()
  {
    return $this->requestedIpVersion;
  }
  /**
   * Required. The service class identifier of the producer.
   *
   * @param string $serviceClass
   */
  public function setServiceClass($serviceClass)
  {
    $this->serviceClass = $serviceClass;
  }
  /**
   * @return string
   */
  public function getServiceClass()
  {
    return $this->serviceClass;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CheckConsumerConfigRequest::class, 'Google_Service_Networkconnectivity_CheckConsumerConfigRequest');
