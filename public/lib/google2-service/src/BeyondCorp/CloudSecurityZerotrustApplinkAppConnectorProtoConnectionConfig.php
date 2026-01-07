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

namespace Google\Service\BeyondCorp;

class CloudSecurityZerotrustApplinkAppConnectorProtoConnectionConfig extends \Google\Collection
{
  protected $collection_key = 'gateway';
  /**
   * application_endpoint is the endpoint of the application the form of
   * host:port. For example, "localhost:80".
   *
   * @var string
   */
  public $applicationEndpoint;
  /**
   * application_name represents the given name of the application the
   * connection is connecting with.
   *
   * @var string
   */
  public $applicationName;
  protected $gatewayType = CloudSecurityZerotrustApplinkAppConnectorProtoGateway::class;
  protected $gatewayDataType = 'array';
  /**
   * name is the unique ID for each connection. TODO(b/190732451) returns
   * connection name from user-specified name in config. Now, name =
   * ${application_name}:${application_endpoint}
   *
   * @var string
   */
  public $name;
  /**
   * project represents the consumer project the connection belongs to.
   *
   * @var string
   */
  public $project;
  /**
   * tunnels_per_gateway reflects the number of tunnels between a connector and
   * a gateway.
   *
   * @var string
   */
  public $tunnelsPerGateway;
  /**
   * user_port specifies the reserved port on gateways for user connections.
   *
   * @var int
   */
  public $userPort;

  /**
   * application_endpoint is the endpoint of the application the form of
   * host:port. For example, "localhost:80".
   *
   * @param string $applicationEndpoint
   */
  public function setApplicationEndpoint($applicationEndpoint)
  {
    $this->applicationEndpoint = $applicationEndpoint;
  }
  /**
   * @return string
   */
  public function getApplicationEndpoint()
  {
    return $this->applicationEndpoint;
  }
  /**
   * application_name represents the given name of the application the
   * connection is connecting with.
   *
   * @param string $applicationName
   */
  public function setApplicationName($applicationName)
  {
    $this->applicationName = $applicationName;
  }
  /**
   * @return string
   */
  public function getApplicationName()
  {
    return $this->applicationName;
  }
  /**
   * gateway lists all instances running a gateway in GCP. They all connect to a
   * connector on the host.
   *
   * @param CloudSecurityZerotrustApplinkAppConnectorProtoGateway[] $gateway
   */
  public function setGateway($gateway)
  {
    $this->gateway = $gateway;
  }
  /**
   * @return CloudSecurityZerotrustApplinkAppConnectorProtoGateway[]
   */
  public function getGateway()
  {
    return $this->gateway;
  }
  /**
   * name is the unique ID for each connection. TODO(b/190732451) returns
   * connection name from user-specified name in config. Now, name =
   * ${application_name}:${application_endpoint}
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
   * project represents the consumer project the connection belongs to.
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
   * tunnels_per_gateway reflects the number of tunnels between a connector and
   * a gateway.
   *
   * @param string $tunnelsPerGateway
   */
  public function setTunnelsPerGateway($tunnelsPerGateway)
  {
    $this->tunnelsPerGateway = $tunnelsPerGateway;
  }
  /**
   * @return string
   */
  public function getTunnelsPerGateway()
  {
    return $this->tunnelsPerGateway;
  }
  /**
   * user_port specifies the reserved port on gateways for user connections.
   *
   * @param int $userPort
   */
  public function setUserPort($userPort)
  {
    $this->userPort = $userPort;
  }
  /**
   * @return int
   */
  public function getUserPort()
  {
    return $this->userPort;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudSecurityZerotrustApplinkAppConnectorProtoConnectionConfig::class, 'Google_Service_BeyondCorp_CloudSecurityZerotrustApplinkAppConnectorProtoConnectionConfig');
