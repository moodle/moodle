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

namespace Google\Service\Dataflow;

class WorkerHealthReport extends \Google\Collection
{
  protected $collection_key = 'pods';
  /**
   * Message describing any unusual health reports.
   *
   * @var string
   */
  public $msg;
  /**
   * The pods running on the worker. See: http://kubernetes.io/v1.1/docs/api-
   * reference/v1/definitions.html#_v1_pod This field is used by the worker to
   * send the status of the indvidual containers running on each worker.
   *
   * @var array[]
   */
  public $pods;
  /**
   * The interval at which the worker is sending health reports. The default
   * value of 0 should be interpreted as the field is not being explicitly set
   * by the worker.
   *
   * @var string
   */
  public $reportInterval;
  /**
   * Code to describe a specific reason, if known, that a VM has reported broken
   * state.
   *
   * @var string
   */
  public $vmBrokenCode;
  /**
   * Whether the VM is in a permanently broken state. Broken VMs should be
   * abandoned or deleted ASAP to avoid assigning or completing any work.
   *
   * @var bool
   */
  public $vmIsBroken;
  /**
   * Whether the VM is currently healthy.
   *
   * @var bool
   */
  public $vmIsHealthy;
  /**
   * The time the VM was booted.
   *
   * @var string
   */
  public $vmStartupTime;

  /**
   * Message describing any unusual health reports.
   *
   * @param string $msg
   */
  public function setMsg($msg)
  {
    $this->msg = $msg;
  }
  /**
   * @return string
   */
  public function getMsg()
  {
    return $this->msg;
  }
  /**
   * The pods running on the worker. See: http://kubernetes.io/v1.1/docs/api-
   * reference/v1/definitions.html#_v1_pod This field is used by the worker to
   * send the status of the indvidual containers running on each worker.
   *
   * @param array[] $pods
   */
  public function setPods($pods)
  {
    $this->pods = $pods;
  }
  /**
   * @return array[]
   */
  public function getPods()
  {
    return $this->pods;
  }
  /**
   * The interval at which the worker is sending health reports. The default
   * value of 0 should be interpreted as the field is not being explicitly set
   * by the worker.
   *
   * @param string $reportInterval
   */
  public function setReportInterval($reportInterval)
  {
    $this->reportInterval = $reportInterval;
  }
  /**
   * @return string
   */
  public function getReportInterval()
  {
    return $this->reportInterval;
  }
  /**
   * Code to describe a specific reason, if known, that a VM has reported broken
   * state.
   *
   * @param string $vmBrokenCode
   */
  public function setVmBrokenCode($vmBrokenCode)
  {
    $this->vmBrokenCode = $vmBrokenCode;
  }
  /**
   * @return string
   */
  public function getVmBrokenCode()
  {
    return $this->vmBrokenCode;
  }
  /**
   * Whether the VM is in a permanently broken state. Broken VMs should be
   * abandoned or deleted ASAP to avoid assigning or completing any work.
   *
   * @param bool $vmIsBroken
   */
  public function setVmIsBroken($vmIsBroken)
  {
    $this->vmIsBroken = $vmIsBroken;
  }
  /**
   * @return bool
   */
  public function getVmIsBroken()
  {
    return $this->vmIsBroken;
  }
  /**
   * Whether the VM is currently healthy.
   *
   * @param bool $vmIsHealthy
   */
  public function setVmIsHealthy($vmIsHealthy)
  {
    $this->vmIsHealthy = $vmIsHealthy;
  }
  /**
   * @return bool
   */
  public function getVmIsHealthy()
  {
    return $this->vmIsHealthy;
  }
  /**
   * The time the VM was booted.
   *
   * @param string $vmStartupTime
   */
  public function setVmStartupTime($vmStartupTime)
  {
    $this->vmStartupTime = $vmStartupTime;
  }
  /**
   * @return string
   */
  public function getVmStartupTime()
  {
    return $this->vmStartupTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WorkerHealthReport::class, 'Google_Service_Dataflow_WorkerHealthReport');
