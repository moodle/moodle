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

class ConsumerConfig extends \Google\Collection
{
  protected $collection_key = 'usedIpRanges';
  protected $cloudsqlConfigsType = CloudSQLConfig::class;
  protected $cloudsqlConfigsDataType = 'array';
  /**
   * Export custom routes flag value for peering from consumer to producer.
   *
   * @var bool
   */
  public $consumerExportCustomRoutes;
  /**
   * Export subnet routes with public ip flag value for peering from consumer to
   * producer.
   *
   * @var bool
   */
  public $consumerExportSubnetRoutesWithPublicIp;
  /**
   * Import custom routes flag value for peering from consumer to producer.
   *
   * @var bool
   */
  public $consumerImportCustomRoutes;
  /**
   * Import subnet routes with public ip flag value for peering from consumer to
   * producer.
   *
   * @var bool
   */
  public $consumerImportSubnetRoutesWithPublicIp;
  /**
   * Export custom routes flag value for peering from producer to consumer.
   *
   * @var bool
   */
  public $producerExportCustomRoutes;
  /**
   * Export subnet routes with public ip flag value for peering from producer to
   * consumer.
   *
   * @var bool
   */
  public $producerExportSubnetRoutesWithPublicIp;
  /**
   * Import custom routes flag value for peering from producer to consumer.
   *
   * @var bool
   */
  public $producerImportCustomRoutes;
  /**
   * Import subnet routes with public ip flag value for peering from producer to
   * consumer.
   *
   * @var bool
   */
  public $producerImportSubnetRoutesWithPublicIp;
  /**
   * Output only. The VPC host network that is used to host managed service
   * instances. In the format, projects/{project}/global/networks/{network}
   * where {project} is the project number e.g. '12345' and {network} is the
   * network name.
   *
   * @var string
   */
  public $producerNetwork;
  protected $reservedRangesType = GoogleCloudServicenetworkingV1ConsumerConfigReservedRange::class;
  protected $reservedRangesDataType = 'array';
  /**
   * Output only. The IP ranges already in use by consumer or producer
   *
   * @var string[]
   */
  public $usedIpRanges;
  /**
   * Output only. Indicates whether the VPC Service Controls reference
   * architecture is configured for the producer VPC host network.
   *
   * @var bool
   */
  public $vpcScReferenceArchitectureEnabled;

  /**
   * Represents one or multiple Cloud SQL configurations.
   *
   * @param CloudSQLConfig[] $cloudsqlConfigs
   */
  public function setCloudsqlConfigs($cloudsqlConfigs)
  {
    $this->cloudsqlConfigs = $cloudsqlConfigs;
  }
  /**
   * @return CloudSQLConfig[]
   */
  public function getCloudsqlConfigs()
  {
    return $this->cloudsqlConfigs;
  }
  /**
   * Export custom routes flag value for peering from consumer to producer.
   *
   * @param bool $consumerExportCustomRoutes
   */
  public function setConsumerExportCustomRoutes($consumerExportCustomRoutes)
  {
    $this->consumerExportCustomRoutes = $consumerExportCustomRoutes;
  }
  /**
   * @return bool
   */
  public function getConsumerExportCustomRoutes()
  {
    return $this->consumerExportCustomRoutes;
  }
  /**
   * Export subnet routes with public ip flag value for peering from consumer to
   * producer.
   *
   * @param bool $consumerExportSubnetRoutesWithPublicIp
   */
  public function setConsumerExportSubnetRoutesWithPublicIp($consumerExportSubnetRoutesWithPublicIp)
  {
    $this->consumerExportSubnetRoutesWithPublicIp = $consumerExportSubnetRoutesWithPublicIp;
  }
  /**
   * @return bool
   */
  public function getConsumerExportSubnetRoutesWithPublicIp()
  {
    return $this->consumerExportSubnetRoutesWithPublicIp;
  }
  /**
   * Import custom routes flag value for peering from consumer to producer.
   *
   * @param bool $consumerImportCustomRoutes
   */
  public function setConsumerImportCustomRoutes($consumerImportCustomRoutes)
  {
    $this->consumerImportCustomRoutes = $consumerImportCustomRoutes;
  }
  /**
   * @return bool
   */
  public function getConsumerImportCustomRoutes()
  {
    return $this->consumerImportCustomRoutes;
  }
  /**
   * Import subnet routes with public ip flag value for peering from consumer to
   * producer.
   *
   * @param bool $consumerImportSubnetRoutesWithPublicIp
   */
  public function setConsumerImportSubnetRoutesWithPublicIp($consumerImportSubnetRoutesWithPublicIp)
  {
    $this->consumerImportSubnetRoutesWithPublicIp = $consumerImportSubnetRoutesWithPublicIp;
  }
  /**
   * @return bool
   */
  public function getConsumerImportSubnetRoutesWithPublicIp()
  {
    return $this->consumerImportSubnetRoutesWithPublicIp;
  }
  /**
   * Export custom routes flag value for peering from producer to consumer.
   *
   * @param bool $producerExportCustomRoutes
   */
  public function setProducerExportCustomRoutes($producerExportCustomRoutes)
  {
    $this->producerExportCustomRoutes = $producerExportCustomRoutes;
  }
  /**
   * @return bool
   */
  public function getProducerExportCustomRoutes()
  {
    return $this->producerExportCustomRoutes;
  }
  /**
   * Export subnet routes with public ip flag value for peering from producer to
   * consumer.
   *
   * @param bool $producerExportSubnetRoutesWithPublicIp
   */
  public function setProducerExportSubnetRoutesWithPublicIp($producerExportSubnetRoutesWithPublicIp)
  {
    $this->producerExportSubnetRoutesWithPublicIp = $producerExportSubnetRoutesWithPublicIp;
  }
  /**
   * @return bool
   */
  public function getProducerExportSubnetRoutesWithPublicIp()
  {
    return $this->producerExportSubnetRoutesWithPublicIp;
  }
  /**
   * Import custom routes flag value for peering from producer to consumer.
   *
   * @param bool $producerImportCustomRoutes
   */
  public function setProducerImportCustomRoutes($producerImportCustomRoutes)
  {
    $this->producerImportCustomRoutes = $producerImportCustomRoutes;
  }
  /**
   * @return bool
   */
  public function getProducerImportCustomRoutes()
  {
    return $this->producerImportCustomRoutes;
  }
  /**
   * Import subnet routes with public ip flag value for peering from producer to
   * consumer.
   *
   * @param bool $producerImportSubnetRoutesWithPublicIp
   */
  public function setProducerImportSubnetRoutesWithPublicIp($producerImportSubnetRoutesWithPublicIp)
  {
    $this->producerImportSubnetRoutesWithPublicIp = $producerImportSubnetRoutesWithPublicIp;
  }
  /**
   * @return bool
   */
  public function getProducerImportSubnetRoutesWithPublicIp()
  {
    return $this->producerImportSubnetRoutesWithPublicIp;
  }
  /**
   * Output only. The VPC host network that is used to host managed service
   * instances. In the format, projects/{project}/global/networks/{network}
   * where {project} is the project number e.g. '12345' and {network} is the
   * network name.
   *
   * @param string $producerNetwork
   */
  public function setProducerNetwork($producerNetwork)
  {
    $this->producerNetwork = $producerNetwork;
  }
  /**
   * @return string
   */
  public function getProducerNetwork()
  {
    return $this->producerNetwork;
  }
  /**
   * Output only. The reserved ranges associated with this private service
   * access connection.
   *
   * @param GoogleCloudServicenetworkingV1ConsumerConfigReservedRange[] $reservedRanges
   */
  public function setReservedRanges($reservedRanges)
  {
    $this->reservedRanges = $reservedRanges;
  }
  /**
   * @return GoogleCloudServicenetworkingV1ConsumerConfigReservedRange[]
   */
  public function getReservedRanges()
  {
    return $this->reservedRanges;
  }
  /**
   * Output only. The IP ranges already in use by consumer or producer
   *
   * @param string[] $usedIpRanges
   */
  public function setUsedIpRanges($usedIpRanges)
  {
    $this->usedIpRanges = $usedIpRanges;
  }
  /**
   * @return string[]
   */
  public function getUsedIpRanges()
  {
    return $this->usedIpRanges;
  }
  /**
   * Output only. Indicates whether the VPC Service Controls reference
   * architecture is configured for the producer VPC host network.
   *
   * @param bool $vpcScReferenceArchitectureEnabled
   */
  public function setVpcScReferenceArchitectureEnabled($vpcScReferenceArchitectureEnabled)
  {
    $this->vpcScReferenceArchitectureEnabled = $vpcScReferenceArchitectureEnabled;
  }
  /**
   * @return bool
   */
  public function getVpcScReferenceArchitectureEnabled()
  {
    return $this->vpcScReferenceArchitectureEnabled;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConsumerConfig::class, 'Google_Service_ServiceNetworking_ConsumerConfig');
