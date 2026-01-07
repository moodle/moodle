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

class ServiceConnectionMap extends \Google\Collection
{
  /**
   * An invalid infrastructure as the default case.
   */
  public const INFRASTRUCTURE_INFRASTRUCTURE_UNSPECIFIED = 'INFRASTRUCTURE_UNSPECIFIED';
  /**
   * Private Service Connect is used for connections.
   */
  public const INFRASTRUCTURE_PSC = 'PSC';
  protected $collection_key = 'producerPscConfigs';
  protected $consumerPscConfigsType = ConsumerPscConfig::class;
  protected $consumerPscConfigsDataType = 'array';
  protected $consumerPscConnectionsType = ConsumerPscConnection::class;
  protected $consumerPscConnectionsDataType = 'array';
  /**
   * Output only. Time when the ServiceConnectionMap was created.
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
   * Output only. The infrastructure used for connections between
   * consumers/producers.
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
   * Immutable. The name of a ServiceConnectionMap. Format: projects/{project}/l
   * ocations/{location}/serviceConnectionMaps/{service_connection_map} See:
   * https://google.aip.dev/122#fields-representing-resource-names
   *
   * @var string
   */
  public $name;
  protected $producerPscConfigsType = ProducerPscConfig::class;
  protected $producerPscConfigsDataType = 'array';
  /**
   * The service class identifier this ServiceConnectionMap is for. The user of
   * ServiceConnectionMap create API needs to have
   * networkconnectivity.serviceClasses.use IAM permission for the service
   * class.
   *
   * @var string
   */
  public $serviceClass;
  /**
   * Output only. The service class uri this ServiceConnectionMap is for.
   *
   * @var string
   */
  public $serviceClassUri;
  /**
   * The token provided by the consumer. This token authenticates that the
   * consumer can create a connection within the specified project and network.
   *
   * @var string
   */
  public $token;
  /**
   * Output only. Time when the ServiceConnectionMap was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * The PSC configurations on consumer side.
   *
   * @param ConsumerPscConfig[] $consumerPscConfigs
   */
  public function setConsumerPscConfigs($consumerPscConfigs)
  {
    $this->consumerPscConfigs = $consumerPscConfigs;
  }
  /**
   * @return ConsumerPscConfig[]
   */
  public function getConsumerPscConfigs()
  {
    return $this->consumerPscConfigs;
  }
  /**
   * Output only. PSC connection details on consumer side.
   *
   * @param ConsumerPscConnection[] $consumerPscConnections
   */
  public function setConsumerPscConnections($consumerPscConnections)
  {
    $this->consumerPscConnections = $consumerPscConnections;
  }
  /**
   * @return ConsumerPscConnection[]
   */
  public function getConsumerPscConnections()
  {
    return $this->consumerPscConnections;
  }
  /**
   * Output only. Time when the ServiceConnectionMap was created.
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
   * Output only. The infrastructure used for connections between
   * consumers/producers.
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
   * Immutable. The name of a ServiceConnectionMap. Format: projects/{project}/l
   * ocations/{location}/serviceConnectionMaps/{service_connection_map} See:
   * https://google.aip.dev/122#fields-representing-resource-names
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
   * The PSC configurations on producer side.
   *
   * @param ProducerPscConfig[] $producerPscConfigs
   */
  public function setProducerPscConfigs($producerPscConfigs)
  {
    $this->producerPscConfigs = $producerPscConfigs;
  }
  /**
   * @return ProducerPscConfig[]
   */
  public function getProducerPscConfigs()
  {
    return $this->producerPscConfigs;
  }
  /**
   * The service class identifier this ServiceConnectionMap is for. The user of
   * ServiceConnectionMap create API needs to have
   * networkconnectivity.serviceClasses.use IAM permission for the service
   * class.
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
   * Output only. The service class uri this ServiceConnectionMap is for.
   *
   * @param string $serviceClassUri
   */
  public function setServiceClassUri($serviceClassUri)
  {
    $this->serviceClassUri = $serviceClassUri;
  }
  /**
   * @return string
   */
  public function getServiceClassUri()
  {
    return $this->serviceClassUri;
  }
  /**
   * The token provided by the consumer. This token authenticates that the
   * consumer can create a connection within the specified project and network.
   *
   * @param string $token
   */
  public function setToken($token)
  {
    $this->token = $token;
  }
  /**
   * @return string
   */
  public function getToken()
  {
    return $this->token;
  }
  /**
   * Output only. Time when the ServiceConnectionMap was updated.
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
class_alias(ServiceConnectionMap::class, 'Google_Service_Networkconnectivity_ServiceConnectionMap');
