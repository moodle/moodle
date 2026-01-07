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

namespace Google\Service\Integrations;

class EnterpriseCrmEventbusProtoConnectorsConnection extends \Google\Model
{
  /**
   * Connection name Format:
   * projects/{project}/locations/{location}/connections/{connection}
   *
   * @var string
   */
  public $connectionName;
  /**
   * Connector version Format: projects/{project}/locations/{location}/providers
   * /{provider}/connectors/{connector}/versions/{version}
   *
   * @var string
   */
  public $connectorVersion;
  /**
   * The name of the Hostname of the Service Directory service with TLS if used.
   *
   * @var string
   */
  public $host;
  /**
   * Service name Format: projects/{project}/locations/{location}/namespaces/{na
   * mespace}/services/{service}
   *
   * @var string
   */
  public $serviceName;

  /**
   * Connection name Format:
   * projects/{project}/locations/{location}/connections/{connection}
   *
   * @param string $connectionName
   */
  public function setConnectionName($connectionName)
  {
    $this->connectionName = $connectionName;
  }
  /**
   * @return string
   */
  public function getConnectionName()
  {
    return $this->connectionName;
  }
  /**
   * Connector version Format: projects/{project}/locations/{location}/providers
   * /{provider}/connectors/{connector}/versions/{version}
   *
   * @param string $connectorVersion
   */
  public function setConnectorVersion($connectorVersion)
  {
    $this->connectorVersion = $connectorVersion;
  }
  /**
   * @return string
   */
  public function getConnectorVersion()
  {
    return $this->connectorVersion;
  }
  /**
   * The name of the Hostname of the Service Directory service with TLS if used.
   *
   * @param string $host
   */
  public function setHost($host)
  {
    $this->host = $host;
  }
  /**
   * @return string
   */
  public function getHost()
  {
    return $this->host;
  }
  /**
   * Service name Format: projects/{project}/locations/{location}/namespaces/{na
   * mespace}/services/{service}
   *
   * @param string $serviceName
   */
  public function setServiceName($serviceName)
  {
    $this->serviceName = $serviceName;
  }
  /**
   * @return string
   */
  public function getServiceName()
  {
    return $this->serviceName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseCrmEventbusProtoConnectorsConnection::class, 'Google_Service_Integrations_EnterpriseCrmEventbusProtoConnectorsConnection');
