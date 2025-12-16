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

namespace Google\Service\Compute;

class ManagedInstanceInstanceHealth extends \Google\Model
{
  /**
   * The instance is being drained. The existing connections to the instance
   * have time to complete, but the new ones are being refused.
   */
  public const DETAILED_HEALTH_STATE_DRAINING = 'DRAINING';
  /**
   * The instance is reachable i.e. a connection to the application health
   * checking endpoint can be established, and conforms to the requirements
   * defined by the health check.
   */
  public const DETAILED_HEALTH_STATE_HEALTHY = 'HEALTHY';
  /**
   * The instance is unreachable i.e. a connection to the application health
   * checking endpoint cannot be established, or the server does not respond
   * within the specified timeout.
   */
  public const DETAILED_HEALTH_STATE_TIMEOUT = 'TIMEOUT';
  /**
   * The instance is reachable, but does not conform to the requirements defined
   * by the health check.
   */
  public const DETAILED_HEALTH_STATE_UNHEALTHY = 'UNHEALTHY';
  /**
   * The health checking system is aware of the instance but its health is not
   * known at the moment.
   */
  public const DETAILED_HEALTH_STATE_UNKNOWN = 'UNKNOWN';
  /**
   * Output only. [Output Only] The current detailed instance health state.
   *
   * @var string
   */
  public $detailedHealthState;
  /**
   * Output only. [Output Only] The URL for the health check that verifies
   * whether the instance is healthy.
   *
   * @var string
   */
  public $healthCheck;

  /**
   * Output only. [Output Only] The current detailed instance health state.
   *
   * Accepted values: DRAINING, HEALTHY, TIMEOUT, UNHEALTHY, UNKNOWN
   *
   * @param self::DETAILED_HEALTH_STATE_* $detailedHealthState
   */
  public function setDetailedHealthState($detailedHealthState)
  {
    $this->detailedHealthState = $detailedHealthState;
  }
  /**
   * @return self::DETAILED_HEALTH_STATE_*
   */
  public function getDetailedHealthState()
  {
    return $this->detailedHealthState;
  }
  /**
   * Output only. [Output Only] The URL for the health check that verifies
   * whether the instance is healthy.
   *
   * @param string $healthCheck
   */
  public function setHealthCheck($healthCheck)
  {
    $this->healthCheck = $healthCheck;
  }
  /**
   * @return string
   */
  public function getHealthCheck()
  {
    return $this->healthCheck;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ManagedInstanceInstanceHealth::class, 'Google_Service_Compute_ManagedInstanceInstanceHealth');
