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

class GoogleCloudConnectorsV1EventingConfig extends \Google\Collection
{
  protected $collection_key = 'privateConnectivityAllowlistedProjects';
  protected $additionalVariablesType = GoogleCloudConnectorsV1ConfigVariable::class;
  protected $additionalVariablesDataType = 'array';
  protected $authConfigType = GoogleCloudConnectorsV1AuthConfig::class;
  protected $authConfigDataType = '';
  protected $deadLetterConfigType = GoogleCloudConnectorsV1EventingConfigDeadLetterConfig::class;
  protected $deadLetterConfigDataType = '';
  protected $enrichmentConfigType = GoogleCloudConnectorsV1EnrichmentConfig::class;
  protected $enrichmentConfigDataType = '';
  /**
   * Optional. Enrichment Enabled.
   *
   * @var bool
   */
  public $enrichmentEnabled;
  /**
   * Optional. Ingress endpoint of the event listener. This is used only when
   * private connectivity is enabled.
   *
   * @var string
   */
  public $eventsListenerIngressEndpoint;
  protected $listenerAuthConfigType = GoogleCloudConnectorsV1AuthConfig::class;
  protected $listenerAuthConfigDataType = '';
  /**
   * Optional. List of projects to be allowlisted for the service attachment
   * created in the tenant project for eventing ingress.
   *
   * @var string[]
   */
  public $privateConnectivityAllowlistedProjects;
  /**
   * Optional. Private Connectivity Enabled.
   *
   * @var bool
   */
  public $privateConnectivityEnabled;
  protected $proxyDestinationConfigType = GoogleCloudConnectorsV1DestinationConfig::class;
  protected $proxyDestinationConfigDataType = '';
  protected $registrationDestinationConfigType = GoogleCloudConnectorsV1DestinationConfig::class;
  protected $registrationDestinationConfigDataType = '';
  protected $sslConfigType = GoogleCloudConnectorsV1SslConfig::class;
  protected $sslConfigDataType = '';

  /**
   * Optional. Additional eventing related field values
   *
   * @param GoogleCloudConnectorsV1ConfigVariable[] $additionalVariables
   */
  public function setAdditionalVariables($additionalVariables)
  {
    $this->additionalVariables = $additionalVariables;
  }
  /**
   * @return GoogleCloudConnectorsV1ConfigVariable[]
   */
  public function getAdditionalVariables()
  {
    return $this->additionalVariables;
  }
  /**
   * Optional. Auth details for the webhook adapter.
   *
   * @param GoogleCloudConnectorsV1AuthConfig $authConfig
   */
  public function setAuthConfig(GoogleCloudConnectorsV1AuthConfig $authConfig)
  {
    $this->authConfig = $authConfig;
  }
  /**
   * @return GoogleCloudConnectorsV1AuthConfig
   */
  public function getAuthConfig()
  {
    return $this->authConfig;
  }
  /**
   * Optional. Dead letter configuration for eventing of a connection.
   *
   * @param GoogleCloudConnectorsV1EventingConfigDeadLetterConfig $deadLetterConfig
   */
  public function setDeadLetterConfig(GoogleCloudConnectorsV1EventingConfigDeadLetterConfig $deadLetterConfig)
  {
    $this->deadLetterConfig = $deadLetterConfig;
  }
  /**
   * @return GoogleCloudConnectorsV1EventingConfigDeadLetterConfig
   */
  public function getDeadLetterConfig()
  {
    return $this->deadLetterConfig;
  }
  /**
   * Optional. Data enrichment configuration.
   *
   * @param GoogleCloudConnectorsV1EnrichmentConfig $enrichmentConfig
   */
  public function setEnrichmentConfig(GoogleCloudConnectorsV1EnrichmentConfig $enrichmentConfig)
  {
    $this->enrichmentConfig = $enrichmentConfig;
  }
  /**
   * @return GoogleCloudConnectorsV1EnrichmentConfig
   */
  public function getEnrichmentConfig()
  {
    return $this->enrichmentConfig;
  }
  /**
   * Optional. Enrichment Enabled.
   *
   * @param bool $enrichmentEnabled
   */
  public function setEnrichmentEnabled($enrichmentEnabled)
  {
    $this->enrichmentEnabled = $enrichmentEnabled;
  }
  /**
   * @return bool
   */
  public function getEnrichmentEnabled()
  {
    return $this->enrichmentEnabled;
  }
  /**
   * Optional. Ingress endpoint of the event listener. This is used only when
   * private connectivity is enabled.
   *
   * @param string $eventsListenerIngressEndpoint
   */
  public function setEventsListenerIngressEndpoint($eventsListenerIngressEndpoint)
  {
    $this->eventsListenerIngressEndpoint = $eventsListenerIngressEndpoint;
  }
  /**
   * @return string
   */
  public function getEventsListenerIngressEndpoint()
  {
    return $this->eventsListenerIngressEndpoint;
  }
  /**
   * Optional. Auth details for the event listener.
   *
   * @param GoogleCloudConnectorsV1AuthConfig $listenerAuthConfig
   */
  public function setListenerAuthConfig(GoogleCloudConnectorsV1AuthConfig $listenerAuthConfig)
  {
    $this->listenerAuthConfig = $listenerAuthConfig;
  }
  /**
   * @return GoogleCloudConnectorsV1AuthConfig
   */
  public function getListenerAuthConfig()
  {
    return $this->listenerAuthConfig;
  }
  /**
   * Optional. List of projects to be allowlisted for the service attachment
   * created in the tenant project for eventing ingress.
   *
   * @param string[] $privateConnectivityAllowlistedProjects
   */
  public function setPrivateConnectivityAllowlistedProjects($privateConnectivityAllowlistedProjects)
  {
    $this->privateConnectivityAllowlistedProjects = $privateConnectivityAllowlistedProjects;
  }
  /**
   * @return string[]
   */
  public function getPrivateConnectivityAllowlistedProjects()
  {
    return $this->privateConnectivityAllowlistedProjects;
  }
  /**
   * Optional. Private Connectivity Enabled.
   *
   * @param bool $privateConnectivityEnabled
   */
  public function setPrivateConnectivityEnabled($privateConnectivityEnabled)
  {
    $this->privateConnectivityEnabled = $privateConnectivityEnabled;
  }
  /**
   * @return bool
   */
  public function getPrivateConnectivityEnabled()
  {
    return $this->privateConnectivityEnabled;
  }
  /**
   * Optional. Proxy for Eventing auto-registration.
   *
   * @param GoogleCloudConnectorsV1DestinationConfig $proxyDestinationConfig
   */
  public function setProxyDestinationConfig(GoogleCloudConnectorsV1DestinationConfig $proxyDestinationConfig)
  {
    $this->proxyDestinationConfig = $proxyDestinationConfig;
  }
  /**
   * @return GoogleCloudConnectorsV1DestinationConfig
   */
  public function getProxyDestinationConfig()
  {
    return $this->proxyDestinationConfig;
  }
  /**
   * Optional. Registration endpoint for auto registration.
   *
   * @param GoogleCloudConnectorsV1DestinationConfig $registrationDestinationConfig
   */
  public function setRegistrationDestinationConfig(GoogleCloudConnectorsV1DestinationConfig $registrationDestinationConfig)
  {
    $this->registrationDestinationConfig = $registrationDestinationConfig;
  }
  /**
   * @return GoogleCloudConnectorsV1DestinationConfig
   */
  public function getRegistrationDestinationConfig()
  {
    return $this->registrationDestinationConfig;
  }
  /**
   * Optional. Ssl config of a connection
   *
   * @param GoogleCloudConnectorsV1SslConfig $sslConfig
   */
  public function setSslConfig(GoogleCloudConnectorsV1SslConfig $sslConfig)
  {
    $this->sslConfig = $sslConfig;
  }
  /**
   * @return GoogleCloudConnectorsV1SslConfig
   */
  public function getSslConfig()
  {
    return $this->sslConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudConnectorsV1EventingConfig::class, 'Google_Service_Integrations_GoogleCloudConnectorsV1EventingConfig');
