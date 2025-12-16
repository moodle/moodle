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

namespace Google\Service\ManagedKafka;

class ConnectNetworkConfig extends \Google\Collection
{
  protected $collection_key = 'dnsDomainNames';
  /**
   * Optional. Deprecated: Managed Kafka Connect clusters can now reach any
   * endpoint accessible from the primary subnet without the need to define
   * additional subnets. Please see https://cloud.google.com/managed-service-
   * for-apache-kafka/docs/connect-cluster/create-connect-cluster#worker-subnet
   * for more information.
   *
   * @deprecated
   * @var string[]
   */
  public $additionalSubnets;
  /**
   * Optional. Additional DNS domain names from the subnet's network to be made
   * visible to the Connect Cluster. When using MirrorMaker2, it's necessary to
   * add the bootstrap address's dns domain name of the target cluster to make
   * it visible to the connector. For example: my-kafka-cluster.us-
   * central1.managedkafka.my-project.cloud.goog
   *
   * @var string[]
   */
  public $dnsDomainNames;
  /**
   * Required. VPC subnet to make available to the Kafka Connect cluster.
   * Structured like:
   * projects/{project}/regions/{region}/subnetworks/{subnet_id} It is used to
   * create a Private Service Connect (PSC) interface for the Kafka Connect
   * workers. It must be located in the same region as the Kafka Connect
   * cluster. The CIDR range of the subnet must be within the IPv4 address
   * ranges for private networks, as specified in RFC 1918. The primary subnet
   * CIDR range must have a minimum size of /22 (1024 addresses).
   *
   * @var string
   */
  public $primarySubnet;

  /**
   * Optional. Deprecated: Managed Kafka Connect clusters can now reach any
   * endpoint accessible from the primary subnet without the need to define
   * additional subnets. Please see https://cloud.google.com/managed-service-
   * for-apache-kafka/docs/connect-cluster/create-connect-cluster#worker-subnet
   * for more information.
   *
   * @deprecated
   * @param string[] $additionalSubnets
   */
  public function setAdditionalSubnets($additionalSubnets)
  {
    $this->additionalSubnets = $additionalSubnets;
  }
  /**
   * @deprecated
   * @return string[]
   */
  public function getAdditionalSubnets()
  {
    return $this->additionalSubnets;
  }
  /**
   * Optional. Additional DNS domain names from the subnet's network to be made
   * visible to the Connect Cluster. When using MirrorMaker2, it's necessary to
   * add the bootstrap address's dns domain name of the target cluster to make
   * it visible to the connector. For example: my-kafka-cluster.us-
   * central1.managedkafka.my-project.cloud.goog
   *
   * @param string[] $dnsDomainNames
   */
  public function setDnsDomainNames($dnsDomainNames)
  {
    $this->dnsDomainNames = $dnsDomainNames;
  }
  /**
   * @return string[]
   */
  public function getDnsDomainNames()
  {
    return $this->dnsDomainNames;
  }
  /**
   * Required. VPC subnet to make available to the Kafka Connect cluster.
   * Structured like:
   * projects/{project}/regions/{region}/subnetworks/{subnet_id} It is used to
   * create a Private Service Connect (PSC) interface for the Kafka Connect
   * workers. It must be located in the same region as the Kafka Connect
   * cluster. The CIDR range of the subnet must be within the IPv4 address
   * ranges for private networks, as specified in RFC 1918. The primary subnet
   * CIDR range must have a minimum size of /22 (1024 addresses).
   *
   * @param string $primarySubnet
   */
  public function setPrimarySubnet($primarySubnet)
  {
    $this->primarySubnet = $primarySubnet;
  }
  /**
   * @return string
   */
  public function getPrimarySubnet()
  {
    return $this->primarySubnet;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConnectNetworkConfig::class, 'Google_Service_ManagedKafka_ConnectNetworkConfig');
