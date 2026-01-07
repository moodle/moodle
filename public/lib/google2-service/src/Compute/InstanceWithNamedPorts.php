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

class InstanceWithNamedPorts extends \Google\Collection
{
  /**
   * The instance is halted and we are performing tear down tasks like network
   * deprogramming, releasing quota, IP, tearing down disks etc.
   */
  public const STATUS_DEPROVISIONING = 'DEPROVISIONING';
  /**
   * For Flex Start provisioning instance is waiting for available capacity from
   * Dynamic Workload Scheduler (DWS).
   */
  public const STATUS_PENDING = 'PENDING';
  /**
   * Resources are being allocated for the instance.
   */
  public const STATUS_PROVISIONING = 'PROVISIONING';
  /**
   * The instance is in repair.
   */
  public const STATUS_REPAIRING = 'REPAIRING';
  /**
   * The instance is running.
   */
  public const STATUS_RUNNING = 'RUNNING';
  /**
   * All required resources have been allocated and the instance is being
   * started.
   */
  public const STATUS_STAGING = 'STAGING';
  /**
   * The instance has stopped successfully.
   */
  public const STATUS_STOPPED = 'STOPPED';
  /**
   * The instance is currently stopping (either being deleted or killed).
   */
  public const STATUS_STOPPING = 'STOPPING';
  /**
   * The instance has suspended.
   */
  public const STATUS_SUSPENDED = 'SUSPENDED';
  /**
   * The instance is suspending.
   */
  public const STATUS_SUSPENDING = 'SUSPENDING';
  /**
   * The instance has stopped (either by explicit action or underlying failure).
   */
  public const STATUS_TERMINATED = 'TERMINATED';
  protected $collection_key = 'namedPorts';
  /**
   * Output only. [Output Only] The URL of the instance.
   *
   * @var string
   */
  public $instance;
  protected $namedPortsType = NamedPort::class;
  protected $namedPortsDataType = 'array';
  /**
   * Output only. [Output Only] The status of the instance.
   *
   * @var string
   */
  public $status;

  /**
   * Output only. [Output Only] The URL of the instance.
   *
   * @param string $instance
   */
  public function setInstance($instance)
  {
    $this->instance = $instance;
  }
  /**
   * @return string
   */
  public function getInstance()
  {
    return $this->instance;
  }
  /**
   * Output only. [Output Only] The named ports that belong to this instance
   * group.
   *
   * @param NamedPort[] $namedPorts
   */
  public function setNamedPorts($namedPorts)
  {
    $this->namedPorts = $namedPorts;
  }
  /**
   * @return NamedPort[]
   */
  public function getNamedPorts()
  {
    return $this->namedPorts;
  }
  /**
   * Output only. [Output Only] The status of the instance.
   *
   * Accepted values: DEPROVISIONING, PENDING, PROVISIONING, REPAIRING, RUNNING,
   * STAGING, STOPPED, STOPPING, SUSPENDED, SUSPENDING, TERMINATED
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstanceWithNamedPorts::class, 'Google_Service_Compute_InstanceWithNamedPorts');
