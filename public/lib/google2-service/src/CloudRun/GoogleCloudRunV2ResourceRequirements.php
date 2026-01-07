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

namespace Google\Service\CloudRun;

class GoogleCloudRunV2ResourceRequirements extends \Google\Model
{
  /**
   * Determines whether CPU is only allocated during requests (true by default).
   * However, if ResourceRequirements is set, the caller must explicitly set
   * this field to true to preserve the default behavior.
   *
   * @var bool
   */
  public $cpuIdle;
  /**
   * Only `memory`, `cpu` and `nvidia.com/gpu` keys in the map are supported.
   * Notes: * The only supported values for CPU are '1', '2', '4', and '8'.
   * Setting 4 CPU requires at least 2Gi of memory. For more information, go to
   * https://cloud.google.com/run/docs/configuring/cpu. * For supported 'memory'
   * values and syntax, go to
   * https://cloud.google.com/run/docs/configuring/memory-limits * The only
   * supported 'nvidia.com/gpu' value is '1'.
   *
   * @var string[]
   */
  public $limits;
  /**
   * Determines whether CPU should be boosted on startup of a new container
   * instance above the requested CPU threshold, this can help reduce cold-start
   * latency.
   *
   * @var bool
   */
  public $startupCpuBoost;

  /**
   * Determines whether CPU is only allocated during requests (true by default).
   * However, if ResourceRequirements is set, the caller must explicitly set
   * this field to true to preserve the default behavior.
   *
   * @param bool $cpuIdle
   */
  public function setCpuIdle($cpuIdle)
  {
    $this->cpuIdle = $cpuIdle;
  }
  /**
   * @return bool
   */
  public function getCpuIdle()
  {
    return $this->cpuIdle;
  }
  /**
   * Only `memory`, `cpu` and `nvidia.com/gpu` keys in the map are supported.
   * Notes: * The only supported values for CPU are '1', '2', '4', and '8'.
   * Setting 4 CPU requires at least 2Gi of memory. For more information, go to
   * https://cloud.google.com/run/docs/configuring/cpu. * For supported 'memory'
   * values and syntax, go to
   * https://cloud.google.com/run/docs/configuring/memory-limits * The only
   * supported 'nvidia.com/gpu' value is '1'.
   *
   * @param string[] $limits
   */
  public function setLimits($limits)
  {
    $this->limits = $limits;
  }
  /**
   * @return string[]
   */
  public function getLimits()
  {
    return $this->limits;
  }
  /**
   * Determines whether CPU should be boosted on startup of a new container
   * instance above the requested CPU threshold, this can help reduce cold-start
   * latency.
   *
   * @param bool $startupCpuBoost
   */
  public function setStartupCpuBoost($startupCpuBoost)
  {
    $this->startupCpuBoost = $startupCpuBoost;
  }
  /**
   * @return bool
   */
  public function getStartupCpuBoost()
  {
    return $this->startupCpuBoost;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRunV2ResourceRequirements::class, 'Google_Service_CloudRun_GoogleCloudRunV2ResourceRequirements');
