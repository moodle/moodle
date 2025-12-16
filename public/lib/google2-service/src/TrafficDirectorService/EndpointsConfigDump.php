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

namespace Google\Service\TrafficDirectorService;

class EndpointsConfigDump extends \Google\Collection
{
  protected $collection_key = 'staticEndpointConfigs';
  protected $dynamicEndpointConfigsType = DynamicEndpointConfig::class;
  protected $dynamicEndpointConfigsDataType = 'array';
  protected $staticEndpointConfigsType = StaticEndpointConfig::class;
  protected $staticEndpointConfigsDataType = 'array';

  /**
   * The dynamically loaded endpoint configs.
   *
   * @param DynamicEndpointConfig[] $dynamicEndpointConfigs
   */
  public function setDynamicEndpointConfigs($dynamicEndpointConfigs)
  {
    $this->dynamicEndpointConfigs = $dynamicEndpointConfigs;
  }
  /**
   * @return DynamicEndpointConfig[]
   */
  public function getDynamicEndpointConfigs()
  {
    return $this->dynamicEndpointConfigs;
  }
  /**
   * The statically loaded endpoint configs.
   *
   * @param StaticEndpointConfig[] $staticEndpointConfigs
   */
  public function setStaticEndpointConfigs($staticEndpointConfigs)
  {
    $this->staticEndpointConfigs = $staticEndpointConfigs;
  }
  /**
   * @return StaticEndpointConfig[]
   */
  public function getStaticEndpointConfigs()
  {
    return $this->staticEndpointConfigs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EndpointsConfigDump::class, 'Google_Service_TrafficDirectorService_EndpointsConfigDump');
