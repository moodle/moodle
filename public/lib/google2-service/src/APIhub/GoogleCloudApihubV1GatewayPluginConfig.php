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

namespace Google\Service\APIhub;

class GoogleCloudApihubV1GatewayPluginConfig extends \Google\Model
{
  protected $apigeeEdgeConfigType = GoogleCloudApihubV1ApigeeEdgeConfig::class;
  protected $apigeeEdgeConfigDataType = '';
  protected $apigeeOpdkConfigType = GoogleCloudApihubV1ApigeeOPDKConfig::class;
  protected $apigeeOpdkConfigDataType = '';
  protected $apigeeXHybridConfigType = GoogleCloudApihubV1ApigeeXHybridConfig::class;
  protected $apigeeXHybridConfigDataType = '';
  /**
   * Required. The name of the gateway plugin instance for which the config is
   * to be specified. Format: projects/{project}/locations/{location}/plugins/{p
   * lugin}/pluginInstances/{plugin_instance}
   *
   * @var string
   */
  public $pluginInstance;

  /**
   * Configuration for Apigee Edge gateways.
   *
   * @param GoogleCloudApihubV1ApigeeEdgeConfig $apigeeEdgeConfig
   */
  public function setApigeeEdgeConfig(GoogleCloudApihubV1ApigeeEdgeConfig $apigeeEdgeConfig)
  {
    $this->apigeeEdgeConfig = $apigeeEdgeConfig;
  }
  /**
   * @return GoogleCloudApihubV1ApigeeEdgeConfig
   */
  public function getApigeeEdgeConfig()
  {
    return $this->apigeeEdgeConfig;
  }
  /**
   * Configuration for Apigee OPDK gateways.
   *
   * @param GoogleCloudApihubV1ApigeeOPDKConfig $apigeeOpdkConfig
   */
  public function setApigeeOpdkConfig(GoogleCloudApihubV1ApigeeOPDKConfig $apigeeOpdkConfig)
  {
    $this->apigeeOpdkConfig = $apigeeOpdkConfig;
  }
  /**
   * @return GoogleCloudApihubV1ApigeeOPDKConfig
   */
  public function getApigeeOpdkConfig()
  {
    return $this->apigeeOpdkConfig;
  }
  /**
   * Configuration for Apigee X and Apigee Hybrid gateways.
   *
   * @param GoogleCloudApihubV1ApigeeXHybridConfig $apigeeXHybridConfig
   */
  public function setApigeeXHybridConfig(GoogleCloudApihubV1ApigeeXHybridConfig $apigeeXHybridConfig)
  {
    $this->apigeeXHybridConfig = $apigeeXHybridConfig;
  }
  /**
   * @return GoogleCloudApihubV1ApigeeXHybridConfig
   */
  public function getApigeeXHybridConfig()
  {
    return $this->apigeeXHybridConfig;
  }
  /**
   * Required. The name of the gateway plugin instance for which the config is
   * to be specified. Format: projects/{project}/locations/{location}/plugins/{p
   * lugin}/pluginInstances/{plugin_instance}
   *
   * @param string $pluginInstance
   */
  public function setPluginInstance($pluginInstance)
  {
    $this->pluginInstance = $pluginInstance;
  }
  /**
   * @return string
   */
  public function getPluginInstance()
  {
    return $this->pluginInstance;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1GatewayPluginConfig::class, 'Google_Service_APIhub_GoogleCloudApihubV1GatewayPluginConfig');
