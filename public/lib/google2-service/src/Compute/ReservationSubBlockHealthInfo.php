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

class ReservationSubBlockHealthInfo extends \Google\Model
{
  /**
   * The reservation subBlock is degraded.
   */
  public const HEALTH_STATUS_DEGRADED = 'DEGRADED';
  /**
   * The reservation subBlock is healthy.
   */
  public const HEALTH_STATUS_HEALTHY = 'HEALTHY';
  /**
   * The health status of the reservation subBlock is unspecified.
   */
  public const HEALTH_STATUS_HEALTH_STATUS_UNSPECIFIED = 'HEALTH_STATUS_UNSPECIFIED';
  /**
   * The number of degraded hosts in the reservation subBlock.
   *
   * @var int
   */
  public $degradedHostCount;
  /**
   * The number of degraded infrastructure (e.g NV link domain) in the
   * reservation subblock.
   *
   * @var int
   */
  public $degradedInfraCount;
  /**
   * The health status of the reservation subBlock.
   *
   * @var string
   */
  public $healthStatus;
  /**
   * The number of healthy hosts in the reservation subBlock.
   *
   * @var int
   */
  public $healthyHostCount;
  /**
   * The number of healthy infrastructure (e.g NV link domain) in the
   * reservation subblock.
   *
   * @var int
   */
  public $healthyInfraCount;

  /**
   * The number of degraded hosts in the reservation subBlock.
   *
   * @param int $degradedHostCount
   */
  public function setDegradedHostCount($degradedHostCount)
  {
    $this->degradedHostCount = $degradedHostCount;
  }
  /**
   * @return int
   */
  public function getDegradedHostCount()
  {
    return $this->degradedHostCount;
  }
  /**
   * The number of degraded infrastructure (e.g NV link domain) in the
   * reservation subblock.
   *
   * @param int $degradedInfraCount
   */
  public function setDegradedInfraCount($degradedInfraCount)
  {
    $this->degradedInfraCount = $degradedInfraCount;
  }
  /**
   * @return int
   */
  public function getDegradedInfraCount()
  {
    return $this->degradedInfraCount;
  }
  /**
   * The health status of the reservation subBlock.
   *
   * Accepted values: DEGRADED, HEALTHY, HEALTH_STATUS_UNSPECIFIED
   *
   * @param self::HEALTH_STATUS_* $healthStatus
   */
  public function setHealthStatus($healthStatus)
  {
    $this->healthStatus = $healthStatus;
  }
  /**
   * @return self::HEALTH_STATUS_*
   */
  public function getHealthStatus()
  {
    return $this->healthStatus;
  }
  /**
   * The number of healthy hosts in the reservation subBlock.
   *
   * @param int $healthyHostCount
   */
  public function setHealthyHostCount($healthyHostCount)
  {
    $this->healthyHostCount = $healthyHostCount;
  }
  /**
   * @return int
   */
  public function getHealthyHostCount()
  {
    return $this->healthyHostCount;
  }
  /**
   * The number of healthy infrastructure (e.g NV link domain) in the
   * reservation subblock.
   *
   * @param int $healthyInfraCount
   */
  public function setHealthyInfraCount($healthyInfraCount)
  {
    $this->healthyInfraCount = $healthyInfraCount;
  }
  /**
   * @return int
   */
  public function getHealthyInfraCount()
  {
    return $this->healthyInfraCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReservationSubBlockHealthInfo::class, 'Google_Service_Compute_ReservationSubBlockHealthInfo');
