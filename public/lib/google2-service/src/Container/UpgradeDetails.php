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

namespace Google\Service\Container;

class UpgradeDetails extends \Google\Model
{
  /**
   * Upgrade start type is unspecified.
   */
  public const START_TYPE_START_TYPE_UNSPECIFIED = 'START_TYPE_UNSPECIFIED';
  /**
   * Upgrade started automatically.
   */
  public const START_TYPE_AUTOMATIC = 'AUTOMATIC';
  /**
   * Upgrade started manually.
   */
  public const START_TYPE_MANUAL = 'MANUAL';
  /**
   * Upgrade state is unknown.
   */
  public const STATE_UNKNOWN = 'UNKNOWN';
  /**
   * Upgrade has failed with an error.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Upgrade has succeeded.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * Upgrade has been canceled.
   */
  public const STATE_CANCELED = 'CANCELED';
  /**
   * Upgrade is running.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The end timestamp of the upgrade.
   *
   * @var string
   */
  public $endTime;
  /**
   * The version before the upgrade.
   *
   * @var string
   */
  public $initialVersion;
  /**
   * The start timestamp of the upgrade.
   *
   * @var string
   */
  public $startTime;
  /**
   * The start type of the upgrade.
   *
   * @var string
   */
  public $startType;
  /**
   * Output only. The state of the upgrade.
   *
   * @var string
   */
  public $state;
  /**
   * The version after the upgrade.
   *
   * @var string
   */
  public $targetVersion;

  /**
   * The end timestamp of the upgrade.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * The version before the upgrade.
   *
   * @param string $initialVersion
   */
  public function setInitialVersion($initialVersion)
  {
    $this->initialVersion = $initialVersion;
  }
  /**
   * @return string
   */
  public function getInitialVersion()
  {
    return $this->initialVersion;
  }
  /**
   * The start timestamp of the upgrade.
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
   * The start type of the upgrade.
   *
   * Accepted values: START_TYPE_UNSPECIFIED, AUTOMATIC, MANUAL
   *
   * @param self::START_TYPE_* $startType
   */
  public function setStartType($startType)
  {
    $this->startType = $startType;
  }
  /**
   * @return self::START_TYPE_*
   */
  public function getStartType()
  {
    return $this->startType;
  }
  /**
   * Output only. The state of the upgrade.
   *
   * Accepted values: UNKNOWN, FAILED, SUCCEEDED, CANCELED, RUNNING
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
   * The version after the upgrade.
   *
   * @param string $targetVersion
   */
  public function setTargetVersion($targetVersion)
  {
    $this->targetVersion = $targetVersion;
  }
  /**
   * @return string
   */
  public function getTargetVersion()
  {
    return $this->targetVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpgradeDetails::class, 'Google_Service_Container_UpgradeDetails');
