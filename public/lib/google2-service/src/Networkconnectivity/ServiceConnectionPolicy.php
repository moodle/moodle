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

class ServiceConnectionPolicy extends \Google\Collection
{
  /**
   * An invalid infrastructure as the default case.
   */
  public const INFRASTRUCTURE_INFRASTRUCTURE_UNSPECIFIED = 'INFRASTRUCTURE_UNSPECIFIED';
  /**
   * Private Service Connect is used for connections.
   */
  public const INFRASTRUCTURE_PSC = 'PSC';
  protected $collection_key = 'pscConnections';
  protected $autoCreatedSubnetInfoType = AutoCreatedSubnetworkInfo::class;
  protected $autoCreatedSubnetInfoDataType = '';
  /**
   * Output only. Time when the ServiceConnectionPolicy was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * A description of this resource.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. The etag is computed by the server, and may be sent on update and
   * delete requests to ensure the client has an up-to-date value before
   * proceeding.
   *
   * @var string
   */
  public $etag;
  /**
   * Output only. The type of underlying resources used to create the
   * connection.
   *
   * @var string
   */
  public $infrastructure;
  /**
   * User-defined labels.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Immutable. The name of a ServiceConnectionPolicy. Format: projects/{project
   * }/locations/{location}/serviceConnectionPolicies/{service_connection_policy
   * } See: https://google.aip.dev/122#fields-representing-resource-names
   *
   * @var string
   */
  public $name;
  /**
   * The resource path of the consumer network. Example: -
   * projects/{projectNumOrId}/global/networks/{resourceId}.
   *
   * @var string
   */
  public $network;
  protected $pscConfigType = PscConfig::class;
  protected $pscConfigDataType = '';
  protected $pscConnectionsType = PscConnection::class;
  protected $pscConnectionsDataType = 'array';
  /**
   * The service class identifier for which this ServiceConnectionPolicy is for.
   * The service class identifier is a unique, symbolic representation of a
   * ServiceClass. It is provided by the Service Producer. Google services have
   * a prefix of gcp or google-cloud. For example, gcp-memorystore-redis or
   * google-cloud-sql. 3rd party services do not. For example, test-
   * service-a3dfcx.
   *
   * @var string
   */
  public $serviceClass;
  /**
   * Output only. Time when the ServiceConnectionPolicy was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Information for the automatically created subnetwork and its
   * associated IR.
   *
   * @param AutoCreatedSubnetworkInfo $autoCreatedSubnetInfo
   */
  public function setAutoCreatedSubnetInfo(AutoCreatedSubnetworkInfo $autoCreatedSubnetInfo)
  {
    $this->autoCreatedSubnetInfo = $autoCreatedSubnetInfo;
  }
  /**
   * @return AutoCreatedSubnetworkInfo
   */
  public function getAutoCreatedSubnetInfo()
  {
    return $this->autoCreatedSubnetInfo;
  }
  /**
   * Output only. Time when the ServiceConnectionPolicy was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * A description of this resource.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Optional. The etag is computed by the server, and may be sent on update and
   * delete requests to ensure the client has an up-to-date value before
   * proceeding.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Output only. The type of underlying resources used to create the
   * connection.
   *
   * Accepted values: INFRASTRUCTURE_UNSPECIFIED, PSC
   *
   * @param self::INFRASTRUCTURE_* $infrastructure
   */
  public function setInfrastructure($infrastructure)
  {
    $this->infrastructure = $infrastructure;
  }
  /**
   * @return self::INFRASTRUCTURE_*
   */
  public function getInfrastructure()
  {
    return $this->infrastructure;
  }
  /**
   * User-defined labels.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Immutable. The name of a ServiceConnectionPolicy. Format: projects/{project
   * }/locations/{location}/serviceConnectionPolicies/{service_connection_policy
   * } See: https://google.aip.dev/122#fields-representing-resource-names
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
   * The resource path of the consumer network. Example: -
   * projects/{projectNumOrId}/global/networks/{resourceId}.
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
   * Configuration used for Private Service Connect connections. Used when
   * Infrastructure is PSC.
   *
   * @param PscConfig $pscConfig
   */
  public function setPscConfig(PscConfig $pscConfig)
  {
    $this->pscConfig = $pscConfig;
  }
  /**
   * @return PscConfig
   */
  public function getPscConfig()
  {
    return $this->pscConfig;
  }
  /**
   * Output only. [Output only] Information about each Private Service Connect
   * connection.
   *
   * @param PscConnection[] $pscConnections
   */
  public function setPscConnections($pscConnections)
  {
    $this->pscConnections = $pscConnections;
  }
  /**
   * @return PscConnection[]
   */
  public function getPscConnections()
  {
    return $this->pscConnections;
  }
  /**
   * The service class identifier for which this ServiceConnectionPolicy is for.
   * The service class identifier is a unique, symbolic representation of a
   * ServiceClass. It is provided by the Service Producer. Google services have
   * a prefix of gcp or google-cloud. For example, gcp-memorystore-redis or
   * google-cloud-sql. 3rd party services do not. For example, test-
   * service-a3dfcx.
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
  /**
   * Output only. Time when the ServiceConnectionPolicy was updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ServiceConnectionPolicy::class, 'Google_Service_Networkconnectivity_ServiceConnectionPolicy');
