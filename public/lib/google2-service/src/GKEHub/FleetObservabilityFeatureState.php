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

class FleetObservabilityFeatureState extends \Google\Model
{
  protected $loggingType = FleetObservabilityFleetObservabilityLoggingState::class;
  protected $loggingDataType = '';
  protected $monitoringType = FleetObservabilityFleetObservabilityMonitoringState::class;
  protected $monitoringDataType = '';

  /**
   * @param FleetObservabilityFleetObservabilityLoggingState
   */
  public function setLogging(FleetObservabilityFleetObservabilityLoggingState $logging)
  {
    $this->logging = $logging;
  }
  /**
   * @return FleetObservabilityFleetObservabilityLoggingState
   */
  public function getLogging()
  {
    return $this->logging;
  }
  /**
   * @param FleetObservabilityFleetObservabilityMonitoringState
   */
  public function setMonitoring(FleetObservabilityFleetObservabilityMonitoringState $monitoring)
  {
    $this->monitoring = $monitoring;
  }
  /**
   * @return FleetObservabilityFleetObservabilityMonitoringState
   */
  public function getMonitoring()
  {
    return $this->monitoring;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FleetObservabilityFeatureState::class, 'Google_Service_GKEHub_FleetObservabilityFeatureState');
