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

class GoogleCloudApihubV1GatewayPluginAddonConfig extends \Google\Collection
{
  protected $collection_key = 'gatewayPluginConfigs';
  protected $gatewayPluginConfigsType = GoogleCloudApihubV1GatewayPluginConfig::class;
  protected $gatewayPluginConfigsDataType = 'array';

  /**
   * Required. The list of gateway plugin configs for which the addon is
   * enabled. Each gateway plugin config should have a unique plugin instance.
   *
   * @param GoogleCloudApihubV1GatewayPluginConfig[] $gatewayPluginConfigs
   */
  public function setGatewayPluginConfigs($gatewayPluginConfigs)
  {
    $this->gatewayPluginConfigs = $gatewayPluginConfigs;
  }
  /**
   * @return GoogleCloudApihubV1GatewayPluginConfig[]
   */
  public function getGatewayPluginConfigs()
  {
    return $this->gatewayPluginConfigs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1GatewayPluginAddonConfig::class, 'Google_Service_APIhub_GoogleCloudApihubV1GatewayPluginAddonConfig');
