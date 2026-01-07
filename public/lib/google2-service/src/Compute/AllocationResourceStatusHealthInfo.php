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

class AllocationResourceStatusHealthInfo extends \Google\Model
{
  /**
   * The reservation is degraded.
   */
  public const HEALTH_STATUS_DEGRADED = 'DEGRADED';
  /**
   * The reservation is healthy.
   */
  public const HEALTH_STATUS_HEALTHY = 'HEALTHY';
  /**
   * The health status of the reservation is unspecified.
   */
  public const HEALTH_STATUS_HEALTH_STATUS_UNSPECIFIED = 'HEALTH_STATUS_UNSPECIFIED';
  /**
   * The number of reservation blocks that are degraded.
   *
   * @var int
   */
  public $degradedBlockCount;
  /**
   * The health status of the reservation.
   *
   * @var string
   */
  public $healthStatus;
  /**
   * The number of reservation blocks that are healthy.
   *
   * @var int
   */
  public $healthyBlockCount;

  /**
   * The number of reservation blocks that are degraded.
   *
   * @param int $degradedBlockCount
   */
  public function setDegradedBlockCount($degradedBlockCount)
  {
    $this->degradedBlockCount = $degradedBlockCount;
  }
  /**
   * @return int
   */
  public function getDegradedBlockCount()
  {
    return $this->degradedBlockCount;
  }
  /**
   * The health status of the reservation.
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
   * The number of reservation blocks that are healthy.
   *
   * @param int $healthyBlockCount
   */
  public function setHealthyBlockCount($healthyBlockCount)
  {
    $this->healthyBlockCount = $healthyBlockCount;
  }
  /**
   * @return int
   */
  public function getHealthyBlockCount()
  {
    return $this->healthyBlockCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AllocationResourceStatusHealthInfo::class, 'Google_Service_Compute_AllocationResourceStatusHealthInfo');
