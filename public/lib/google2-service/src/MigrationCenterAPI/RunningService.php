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

namespace Google\Service\MigrationCenterAPI;

class RunningService extends \Google\Model
{
  /**
   * Start mode unspecified.
   */
  public const START_MODE_START_MODE_UNSPECIFIED = 'START_MODE_UNSPECIFIED';
  /**
   * The service is a device driver started by the system loader.
   */
  public const START_MODE_BOOT = 'BOOT';
  /**
   * The service is a device driver started by the IOInitSystem function.
   */
  public const START_MODE_SYSTEM = 'SYSTEM';
  /**
   * The service is started by the operating system, at system start-up
   */
  public const START_MODE_AUTO = 'AUTO';
  /**
   * The service is started only manually, by a user.
   */
  public const START_MODE_MANUAL = 'MANUAL';
  /**
   * The service is disabled.
   */
  public const START_MODE_DISABLED = 'DISABLED';
  /**
   * Service state unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Service is active.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * Service is paused.
   */
  public const STATE_PAUSED = 'PAUSED';
  /**
   * Service is stopped.
   */
  public const STATE_STOPPED = 'STOPPED';
  /**
   * Service command line.
   *
   * @var string
   */
  public $cmdline;
  /**
   * Service binary path.
   *
   * @var string
   */
  public $exePath;
  /**
   * Service pid.
   *
   * @var string
   */
  public $pid;
  /**
   * Service name.
   *
   * @var string
   */
  public $serviceName;
  /**
   * Service start mode (OS-agnostic).
   *
   * @var string
   */
  public $startMode;
  /**
   * Service state (OS-agnostic).
   *
   * @var string
   */
  public $state;

  /**
   * Service command line.
   *
   * @param string $cmdline
   */
  public function setCmdline($cmdline)
  {
    $this->cmdline = $cmdline;
  }
  /**
   * @return string
   */
  public function getCmdline()
  {
    return $this->cmdline;
  }
  /**
   * Service binary path.
   *
   * @param string $exePath
   */
  public function setExePath($exePath)
  {
    $this->exePath = $exePath;
  }
  /**
   * @return string
   */
  public function getExePath()
  {
    return $this->exePath;
  }
  /**
   * Service pid.
   *
   * @param string $pid
   */
  public function setPid($pid)
  {
    $this->pid = $pid;
  }
  /**
   * @return string
   */
  public function getPid()
  {
    return $this->pid;
  }
  /**
   * Service name.
   *
   * @param string $serviceName
   */
  public function setServiceName($serviceName)
  {
    $this->serviceName = $serviceName;
  }
  /**
   * @return string
   */
  public function getServiceName()
  {
    return $this->serviceName;
  }
  /**
   * Service start mode (OS-agnostic).
   *
   * Accepted values: START_MODE_UNSPECIFIED, BOOT, SYSTEM, AUTO, MANUAL,
   * DISABLED
   *
   * @param self::START_MODE_* $startMode
   */
  public function setStartMode($startMode)
  {
    $this->startMode = $startMode;
  }
  /**
   * @return self::START_MODE_*
   */
  public function getStartMode()
  {
    return $this->startMode;
  }
  /**
   * Service state (OS-agnostic).
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, PAUSED, STOPPED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RunningService::class, 'Google_Service_MigrationCenterAPI_RunningService');
