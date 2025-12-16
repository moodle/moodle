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

class GoogleCloudApihubV1AddonConfig extends \Google\Model
{
  protected $allDataAddonConfigType = GoogleCloudApihubV1AllDataAddonConfig::class;
  protected $allDataAddonConfigDataType = '';
  protected $gatewayPluginAddonConfigType = GoogleCloudApihubV1GatewayPluginAddonConfig::class;
  protected $gatewayPluginAddonConfigDataType = '';

  /**
   * Configuration for addons which act on all data in the API hub.
   *
   * @param GoogleCloudApihubV1AllDataAddonConfig $allDataAddonConfig
   */
  public function setAllDataAddonConfig(GoogleCloudApihubV1AllDataAddonConfig $allDataAddonConfig)
  {
    $this->allDataAddonConfig = $allDataAddonConfig;
  }
  /**
   * @return GoogleCloudApihubV1AllDataAddonConfig
   */
  public function getAllDataAddonConfig()
  {
    return $this->allDataAddonConfig;
  }
  /**
   * Configuration for gateway plugin addons.
   *
   * @param GoogleCloudApihubV1GatewayPluginAddonConfig $gatewayPluginAddonConfig
   */
  public function setGatewayPluginAddonConfig(GoogleCloudApihubV1GatewayPluginAddonConfig $gatewayPluginAddonConfig)
  {
    $this->gatewayPluginAddonConfig = $gatewayPluginAddonConfig;
  }
  /**
   * @return GoogleCloudApihubV1GatewayPluginAddonConfig
   */
  public function getGatewayPluginAddonConfig()
  {
    return $this->gatewayPluginAddonConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1AddonConfig::class, 'Google_Service_APIhub_GoogleCloudApihubV1AddonConfig');
