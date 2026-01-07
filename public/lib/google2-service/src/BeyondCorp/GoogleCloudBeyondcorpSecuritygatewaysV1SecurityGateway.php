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

class GoogleCloudBeyondcorpSecuritygatewaysV1SecurityGateway extends \Google\Collection
{
  /**
   * Default value. This value is unused.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * SecurityGateway is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * SecurityGateway is being updated.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * SecurityGateway is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * SecurityGateway is running.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * SecurityGateway is down and may be restored in the future.
   */
  public const STATE_DOWN = 'DOWN';
  /**
   * SecurityGateway encountered an error and is in an indeterministic state.
   */
  public const STATE_ERROR = 'ERROR';
  protected $collection_key = 'externalIps';
  /**
   * Output only. Timestamp when the resource was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. Service account used for operations that involve resources in
   * consumer projects.
   *
   * @var string
   */
  public $delegatingServiceAccount;
  /**
   * Optional. An arbitrary user-provided name for the SecurityGateway. Cannot
   * exceed 64 characters.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. IP addresses that will be used for establishing connection to
   * the endpoints.
   *
   * @var string[]
   */
  public $externalIps;
  protected $hubsType = GoogleCloudBeyondcorpSecuritygatewaysV1Hub::class;
  protected $hubsDataType = 'map';
  /**
   * Identifier. Name of the resource.
   *
   * @var string
   */
  public $name;
  protected $proxyProtocolConfigType = GoogleCloudBeyondcorpSecuritygatewaysV1ProxyProtocolConfig::class;
  protected $proxyProtocolConfigDataType = '';
  protected $serviceDiscoveryType = GoogleCloudBeyondcorpSecuritygatewaysV1ServiceDiscovery::class;
  protected $serviceDiscoveryDataType = '';
  /**
   * Output only. The operational state of the SecurityGateway.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Timestamp when the resource was last modified.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Timestamp when the resource was created.
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
   * Output only. Service account used for operations that involve resources in
   * consumer projects.
   *
   * @param string $delegatingServiceAccount
   */
  public function setDelegatingServiceAccount($delegatingServiceAccount)
  {
    $this->delegatingServiceAccount = $delegatingServiceAccount;
  }
  /**
   * @return string
   */
  public function getDelegatingServiceAccount()
  {
    return $this->delegatingServiceAccount;
  }
  /**
   * Optional. An arbitrary user-provided name for the SecurityGateway. Cannot
   * exceed 64 characters.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. IP addresses that will be used for establishing connection to
   * the endpoints.
   *
   * @param string[] $externalIps
   */
  public function setExternalIps($externalIps)
  {
    $this->externalIps = $externalIps;
  }
  /**
   * @return string[]
   */
  public function getExternalIps()
  {
    return $this->externalIps;
  }
  /**
   * Optional. Map of Hubs that represents regional data path deployment with
   * GCP region as a key.
   *
   * @param GoogleCloudBeyondcorpSecuritygatewaysV1Hub[] $hubs
   */
  public function setHubs($hubs)
  {
    $this->hubs = $hubs;
  }
  /**
   * @return GoogleCloudBeyondcorpSecuritygatewaysV1Hub[]
   */
  public function getHubs()
  {
    return $this->hubs;
  }
  /**
   * Identifier. Name of the resource.
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
   * Optional. Shared proxy configuration for all apps.
   *
   * @param GoogleCloudBeyondcorpSecuritygatewaysV1ProxyProtocolConfig $proxyProtocolConfig
   */
  public function setProxyProtocolConfig(GoogleCloudBeyondcorpSecuritygatewaysV1ProxyProtocolConfig $proxyProtocolConfig)
  {
    $this->proxyProtocolConfig = $proxyProtocolConfig;
  }
  /**
   * @return GoogleCloudBeyondcorpSecuritygatewaysV1ProxyProtocolConfig
   */
  public function getProxyProtocolConfig()
  {
    return $this->proxyProtocolConfig;
  }
  /**
   * Optional. Settings related to the Service Discovery.
   *
   * @param GoogleCloudBeyondcorpSecuritygatewaysV1ServiceDiscovery $serviceDiscovery
   */
  public function setServiceDiscovery(GoogleCloudBeyondcorpSecuritygatewaysV1ServiceDiscovery $serviceDiscovery)
  {
    $this->serviceDiscovery = $serviceDiscovery;
  }
  /**
   * @return GoogleCloudBeyondcorpSecuritygatewaysV1ServiceDiscovery
   */
  public function getServiceDiscovery()
  {
    return $this->serviceDiscovery;
  }
  /**
   * Output only. The operational state of the SecurityGateway.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, UPDATING, DELETING, RUNNING,
   * DOWN, ERROR
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
  /**
   * Output only. Timestamp when the resource was last modified.
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
class_alias(GoogleCloudBeyondcorpSecuritygatewaysV1SecurityGateway::class, 'Google_Service_BeyondCorp_GoogleCloudBeyondcorpSecuritygatewaysV1SecurityGateway');
