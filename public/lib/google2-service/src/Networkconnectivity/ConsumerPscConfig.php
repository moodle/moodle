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

class ConsumerPscConfig extends \Google\Model
{
  /**
   * Default value. We will use IPv4 or IPv6 depending on the IP version of
   * first available subnetwork.
   */
  public const IP_VERSION_IP_VERSION_UNSPECIFIED = 'IP_VERSION_UNSPECIFIED';
  /**
   * Will use IPv4 only.
   */
  public const IP_VERSION_IPV4 = 'IPV4';
  /**
   * Will use IPv6 only.
   */
  public const IP_VERSION_IPV6 = 'IPV6';
  /**
   * Default state, when Connection Map is created initially.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Set when policy and map configuration is valid, and their matching can lead
   * to allowing creation of PSC Connections subject to other constraints like
   * connections limit.
   */
  public const STATE_VALID = 'VALID';
  /**
   * No Service Connection Policy found for this network and Service Class
   */
  public const STATE_CONNECTION_POLICY_MISSING = 'CONNECTION_POLICY_MISSING';
  /**
   * Service Connection Policy limit reached for this network and Service Class
   */
  public const STATE_POLICY_LIMIT_REACHED = 'POLICY_LIMIT_REACHED';
  /**
   * The consumer instance project is not in
   * AllowedGoogleProducersResourceHierarchyLevels of the matching
   * ServiceConnectionPolicy.
   */
  public const STATE_CONSUMER_INSTANCE_PROJECT_NOT_ALLOWLISTED = 'CONSUMER_INSTANCE_PROJECT_NOT_ALLOWLISTED';
  /**
   * Required. The project ID or project number of the consumer project. This
   * project is the one that the consumer uses to interact with the producer
   * instance. From the perspective of a consumer who's created a producer
   * instance, this is the project of the producer instance. Format: 'projects/'
   * Eg. 'projects/consumer-project' or 'projects/1234'
   *
   * @var string
   */
  public $consumerInstanceProject;
  /**
   * This is used in PSC consumer ForwardingRule to control whether the PSC
   * endpoint can be accessed from another region.
   *
   * @var bool
   */
  public $disableGlobalAccess;
  /**
   * The requested IP version for the PSC connection.
   *
   * @var string
   */
  public $ipVersion;
  /**
   * The resource path of the consumer network where PSC connections are allowed
   * to be created in. Note, this network does not need be in the
   * ConsumerPscConfig.project in the case of SharedVPC. Example:
   * projects/{projectNumOrId}/global/networks/{networkId}.
   *
   * @var string
   */
  public $network;
  /**
   * Immutable. Deprecated. Use producer_instance_metadata instead. An immutable
   * identifier for the producer instance.
   *
   * @deprecated
   * @var string
   */
  public $producerInstanceId;
  /**
   * Immutable. An immutable map for the producer instance metadata.
   *
   * @var string[]
   */
  public $producerInstanceMetadata;
  /**
   * The consumer project where PSC connections are allowed to be created in.
   *
   * @var string
   */
  public $project;
  /**
   * Output only. A map to store mapping between customer vip and target service
   * attachment. Only service attachment with producer specified ip addresses
   * are stored here.
   *
   * @var string[]
   */
  public $serviceAttachmentIpAddressMap;
  /**
   * Output only. Overall state of PSC Connections management for this consumer
   * psc config.
   *
   * @var string
   */
  public $state;

  /**
   * Required. The project ID or project number of the consumer project. This
   * project is the one that the consumer uses to interact with the producer
   * instance. From the perspective of a consumer who's created a producer
   * instance, this is the project of the producer instance. Format: 'projects/'
   * Eg. 'projects/consumer-project' or 'projects/1234'
   *
   * @param string $consumerInstanceProject
   */
  public function setConsumerInstanceProject($consumerInstanceProject)
  {
    $this->consumerInstanceProject = $consumerInstanceProject;
  }
  /**
   * @return string
   */
  public function getConsumerInstanceProject()
  {
    return $this->consumerInstanceProject;
  }
  /**
   * This is used in PSC consumer ForwardingRule to control whether the PSC
   * endpoint can be accessed from another region.
   *
   * @param bool $disableGlobalAccess
   */
  public function setDisableGlobalAccess($disableGlobalAccess)
  {
    $this->disableGlobalAccess = $disableGlobalAccess;
  }
  /**
   * @return bool
   */
  public function getDisableGlobalAccess()
  {
    return $this->disableGlobalAccess;
  }
  /**
   * The requested IP version for the PSC connection.
   *
   * Accepted values: IP_VERSION_UNSPECIFIED, IPV4, IPV6
   *
   * @param self::IP_VERSION_* $ipVersion
   */
  public function setIpVersion($ipVersion)
  {
    $this->ipVersion = $ipVersion;
  }
  /**
   * @return self::IP_VERSION_*
   */
  public function getIpVersion()
  {
    return $this->ipVersion;
  }
  /**
   * The resource path of the consumer network where PSC connections are allowed
   * to be created in. Note, this network does not need be in the
   * ConsumerPscConfig.project in the case of SharedVPC. Example:
   * projects/{projectNumOrId}/global/networks/{networkId}.
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
   * Immutable. Deprecated. Use producer_instance_metadata instead. An immutable
   * identifier for the producer instance.
   *
   * @deprecated
   * @param string $producerInstanceId
   */
  public function setProducerInstanceId($producerInstanceId)
  {
    $this->producerInstanceId = $producerInstanceId;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getProducerInstanceId()
  {
    return $this->producerInstanceId;
  }
  /**
   * Immutable. An immutable map for the producer instance metadata.
   *
   * @param string[] $producerInstanceMetadata
   */
  public function setProducerInstanceMetadata($producerInstanceMetadata)
  {
    $this->producerInstanceMetadata = $producerInstanceMetadata;
  }
  /**
   * @return string[]
   */
  public function getProducerInstanceMetadata()
  {
    return $this->producerInstanceMetadata;
  }
  /**
   * The consumer project where PSC connections are allowed to be created in.
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
   * Output only. A map to store mapping between customer vip and target service
   * attachment. Only service attachment with producer specified ip addresses
   * are stored here.
   *
   * @param string[] $serviceAttachmentIpAddressMap
   */
  public function setServiceAttachmentIpAddressMap($serviceAttachmentIpAddressMap)
  {
    $this->serviceAttachmentIpAddressMap = $serviceAttachmentIpAddressMap;
  }
  /**
   * @return string[]
   */
  public function getServiceAttachmentIpAddressMap()
  {
    return $this->serviceAttachmentIpAddressMap;
  }
  /**
   * Output only. Overall state of PSC Connections management for this consumer
   * psc config.
   *
   * Accepted values: STATE_UNSPECIFIED, VALID, CONNECTION_POLICY_MISSING,
   * POLICY_LIMIT_REACHED, CONSUMER_INSTANCE_PROJECT_NOT_ALLOWLISTED
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConsumerPscConfig::class, 'Google_Service_Networkconnectivity_ConsumerPscConfig');
