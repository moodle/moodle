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

class ReservationBlockHealthInfo extends \Google\Model
{
  /**
   * The reservation block is degraded.
   */
  public const HEALTH_STATUS_DEGRADED = 'DEGRADED';
  /**
   * The reservation block is healthy.
   */
  public const HEALTH_STATUS_HEALTHY = 'HEALTHY';
  /**
   * The health status of the reservation block is unspecified.
   */
  public const HEALTH_STATUS_HEALTH_STATUS_UNSPECIFIED = 'HEALTH_STATUS_UNSPECIFIED';
  /**
   * The number of subBlocks that are degraded.
   *
   * @var int
   */
  public $degradedSubBlockCount;
  /**
   * The health status of the reservation block.
   *
   * @var string
   */
  public $healthStatus;
  /**
   * The number of subBlocks that are healthy.
   *
   * @var int
   */
  public $healthySubBlockCount;

  /**
   * The number of subBlocks that are degraded.
   *
   * @param int $degradedSubBlockCount
   */
  public function setDegradedSubBlockCount($degradedSubBlockCount)
  {
    $this->degradedSubBlockCount = $degradedSubBlockCount;
  }
  /**
   * @return int
   */
  public function getDegradedSubBlockCount()
  {
    return $this->degradedSubBlockCount;
  }
  /**
   * The health status of the reservation block.
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
   * The number of subBlocks that are healthy.
   *
   * @param int $healthySubBlockCount
   */
  public function setHealthySubBlockCount($healthySubBlockCount)
  {
    $this->healthySubBlockCount = $healthySubBlockCount;
  }
  /**
   * @return int
   */
  public function getHealthySubBlockCount()
  {
    return $this->healthySubBlockCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReservationBlockHealthInfo::class, 'Google_Service_Compute_ReservationBlockHealthInfo');
