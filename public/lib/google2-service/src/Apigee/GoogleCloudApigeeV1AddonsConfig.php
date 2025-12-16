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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1AddonsConfig extends \Google\Model
{
  protected $advancedApiOpsConfigType = GoogleCloudApigeeV1AdvancedApiOpsConfig::class;
  protected $advancedApiOpsConfigDataType = '';
  protected $analyticsConfigType = GoogleCloudApigeeV1AnalyticsConfig::class;
  protected $analyticsConfigDataType = '';
  protected $apiSecurityConfigType = GoogleCloudApigeeV1ApiSecurityConfig::class;
  protected $apiSecurityConfigDataType = '';
  protected $connectorsPlatformConfigType = GoogleCloudApigeeV1ConnectorsPlatformConfig::class;
  protected $connectorsPlatformConfigDataType = '';
  protected $integrationConfigType = GoogleCloudApigeeV1IntegrationConfig::class;
  protected $integrationConfigDataType = '';
  protected $monetizationConfigType = GoogleCloudApigeeV1MonetizationConfig::class;
  protected $monetizationConfigDataType = '';

  /**
   * Configuration for the Advanced API Ops add-on.
   *
   * @param GoogleCloudApigeeV1AdvancedApiOpsConfig $advancedApiOpsConfig
   */
  public function setAdvancedApiOpsConfig(GoogleCloudApigeeV1AdvancedApiOpsConfig $advancedApiOpsConfig)
  {
    $this->advancedApiOpsConfig = $advancedApiOpsConfig;
  }
  /**
   * @return GoogleCloudApigeeV1AdvancedApiOpsConfig
   */
  public function getAdvancedApiOpsConfig()
  {
    return $this->advancedApiOpsConfig;
  }
  /**
   * Configuration for the Analytics add-on. Only used in
   * organizations.environments.addonsConfig.
   *
   * @param GoogleCloudApigeeV1AnalyticsConfig $analyticsConfig
   */
  public function setAnalyticsConfig(GoogleCloudApigeeV1AnalyticsConfig $analyticsConfig)
  {
    $this->analyticsConfig = $analyticsConfig;
  }
  /**
   * @return GoogleCloudApigeeV1AnalyticsConfig
   */
  public function getAnalyticsConfig()
  {
    return $this->analyticsConfig;
  }
  /**
   * Configuration for the API Security add-on.
   *
   * @param GoogleCloudApigeeV1ApiSecurityConfig $apiSecurityConfig
   */
  public function setApiSecurityConfig(GoogleCloudApigeeV1ApiSecurityConfig $apiSecurityConfig)
  {
    $this->apiSecurityConfig = $apiSecurityConfig;
  }
  /**
   * @return GoogleCloudApigeeV1ApiSecurityConfig
   */
  public function getApiSecurityConfig()
  {
    return $this->apiSecurityConfig;
  }
  /**
   * Configuration for the Connectors Platform add-on.
   *
   * @param GoogleCloudApigeeV1ConnectorsPlatformConfig $connectorsPlatformConfig
   */
  public function setConnectorsPlatformConfig(GoogleCloudApigeeV1ConnectorsPlatformConfig $connectorsPlatformConfig)
  {
    $this->connectorsPlatformConfig = $connectorsPlatformConfig;
  }
  /**
   * @return GoogleCloudApigeeV1ConnectorsPlatformConfig
   */
  public function getConnectorsPlatformConfig()
  {
    return $this->connectorsPlatformConfig;
  }
  /**
   * Configuration for the Integration add-on.
   *
   * @param GoogleCloudApigeeV1IntegrationConfig $integrationConfig
   */
  public function setIntegrationConfig(GoogleCloudApigeeV1IntegrationConfig $integrationConfig)
  {
    $this->integrationConfig = $integrationConfig;
  }
  /**
   * @return GoogleCloudApigeeV1IntegrationConfig
   */
  public function getIntegrationConfig()
  {
    return $this->integrationConfig;
  }
  /**
   * Configuration for the Monetization add-on.
   *
   * @param GoogleCloudApigeeV1MonetizationConfig $monetizationConfig
   */
  public function setMonetizationConfig(GoogleCloudApigeeV1MonetizationConfig $monetizationConfig)
  {
    $this->monetizationConfig = $monetizationConfig;
  }
  /**
   * @return GoogleCloudApigeeV1MonetizationConfig
   */
  public function getMonetizationConfig()
  {
    return $this->monetizationConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1AddonsConfig::class, 'Google_Service_Apigee_GoogleCloudApigeeV1AddonsConfig');
