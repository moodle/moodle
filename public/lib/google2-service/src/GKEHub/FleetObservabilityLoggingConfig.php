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

namespace Google\Service\GKEHub;

class FleetObservabilityLoggingConfig extends \Google\Model
{
  protected $defaultConfigType = FleetObservabilityRoutingConfig::class;
  protected $defaultConfigDataType = '';
  protected $fleetScopeLogsConfigType = FleetObservabilityRoutingConfig::class;
  protected $fleetScopeLogsConfigDataType = '';

  /**
   * @param FleetObservabilityRoutingConfig
   */
  public function setDefaultConfig(FleetObservabilityRoutingConfig $defaultConfig)
  {
    $this->defaultConfig = $defaultConfig;
  }
  /**
   * @return FleetObservabilityRoutingConfig
   */
  public function getDefaultConfig()
  {
    return $this->defaultConfig;
  }
  /**
   * @param FleetObservabilityRoutingConfig
   */
  public function setFleetScopeLogsConfig(FleetObservabilityRoutingConfig $fleetScopeLogsConfig)
  {
    $this->fleetScopeLogsConfig = $fleetScopeLogsConfig;
  }
  /**
   * @return FleetObservabilityRoutingConfig
   */
  public function getFleetScopeLogsConfig()
  {
    return $this->fleetScopeLogsConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FleetObservabilityLoggingConfig::class, 'Google_Service_GKEHub_FleetObservabilityLoggingConfig');
