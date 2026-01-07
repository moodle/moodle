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

class InstanceGroupManagerAutoHealingPolicy extends \Google\Model
{
  /**
   * The URL for the health check that signals autohealing.
   *
   * @var string
   */
  public $healthCheck;
  /**
   * The initial delay is the number of seconds that a new VM takes to
   * initialize and run its startup script. During a VM's initial delay period,
   * the MIG ignores unsuccessful health checks because the VM might be in the
   * startup process. This prevents the MIG from prematurely recreating a VM. If
   * the health check receives a healthy response during the initial delay, it
   * indicates that the startup process is complete and the VM is ready. The
   * value of initial delay must be between 0 and 3600 seconds. The default
   * value is 0.
   *
   * @var int
   */
  public $initialDelaySec;

  /**
   * The URL for the health check that signals autohealing.
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
  /**
   * The initial delay is the number of seconds that a new VM takes to
   * initialize and run its startup script. During a VM's initial delay period,
   * the MIG ignores unsuccessful health checks because the VM might be in the
   * startup process. This prevents the MIG from prematurely recreating a VM. If
   * the health check receives a healthy response during the initial delay, it
   * indicates that the startup process is complete and the VM is ready. The
   * value of initial delay must be between 0 and 3600 seconds. The default
   * value is 0.
   *
   * @param int $initialDelaySec
   */
  public function setInitialDelaySec($initialDelaySec)
  {
    $this->initialDelaySec = $initialDelaySec;
  }
  /**
   * @return int
   */
  public function getInitialDelaySec()
  {
    return $this->initialDelaySec;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstanceGroupManagerAutoHealingPolicy::class, 'Google_Service_Compute_InstanceGroupManagerAutoHealingPolicy');
