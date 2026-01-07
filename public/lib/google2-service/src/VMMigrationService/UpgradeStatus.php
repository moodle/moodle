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

namespace Google\Service\VMMigrationService;

class UpgradeStatus extends \Google\Model
{
  /**
   * The state was not sampled by the health checks yet.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The upgrade has started.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The upgrade failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The upgrade finished successfully.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  protected $errorType = Status::class;
  protected $errorDataType = '';
  /**
   * The version from which we upgraded.
   *
   * @var string
   */
  public $previousVersion;
  /**
   * The time the operation was started.
   *
   * @var string
   */
  public $startTime;
  /**
   * The state of the upgradeAppliance operation.
   *
   * @var string
   */
  public $state;
  /**
   * The version to upgrade to.
   *
   * @var string
   */
  public $version;

  /**
   * Output only. Provides details on the state of the upgrade operation in case
   * of an error.
   *
   * @param Status $error
   */
  public function setError(Status $error)
  {
    $this->error = $error;
  }
  /**
   * @return Status
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * The version from which we upgraded.
   *
   * @param string $previousVersion
   */
  public function setPreviousVersion($previousVersion)
  {
    $this->previousVersion = $previousVersion;
  }
  /**
   * @return string
   */
  public function getPreviousVersion()
  {
    return $this->previousVersion;
  }
  /**
   * The time the operation was started.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * The state of the upgradeAppliance operation.
   *
   * Accepted values: STATE_UNSPECIFIED, RUNNING, FAILED, SUCCEEDED
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
  /**
   * The version to upgrade to.
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpgradeStatus::class, 'Google_Service_VMMigrationService_UpgradeStatus');
