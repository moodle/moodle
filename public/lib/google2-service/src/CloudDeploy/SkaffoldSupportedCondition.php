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

namespace Google\Service\CloudDeploy;

class SkaffoldSupportedCondition extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const SKAFFOLD_SUPPORT_STATE_SKAFFOLD_SUPPORT_STATE_UNSPECIFIED = 'SKAFFOLD_SUPPORT_STATE_UNSPECIFIED';
  /**
   * This Skaffold version is currently supported.
   */
  public const SKAFFOLD_SUPPORT_STATE_SKAFFOLD_SUPPORT_STATE_SUPPORTED = 'SKAFFOLD_SUPPORT_STATE_SUPPORTED';
  /**
   * This Skaffold version is in maintenance mode.
   */
  public const SKAFFOLD_SUPPORT_STATE_SKAFFOLD_SUPPORT_STATE_MAINTENANCE_MODE = 'SKAFFOLD_SUPPORT_STATE_MAINTENANCE_MODE';
  /**
   * This Skaffold version is no longer supported.
   */
  public const SKAFFOLD_SUPPORT_STATE_SKAFFOLD_SUPPORT_STATE_UNSUPPORTED = 'SKAFFOLD_SUPPORT_STATE_UNSUPPORTED';
  /**
   * The time at which this release's version of Skaffold will enter maintenance
   * mode.
   *
   * @var string
   */
  public $maintenanceModeTime;
  /**
   * The Skaffold support state for this release's version of Skaffold.
   *
   * @var string
   */
  public $skaffoldSupportState;
  /**
   * True if the version of Skaffold used by this release is supported.
   *
   * @var bool
   */
  public $status;
  /**
   * The time at which this release's version of Skaffold will no longer be
   * supported.
   *
   * @var string
   */
  public $supportExpirationTime;

  /**
   * The time at which this release's version of Skaffold will enter maintenance
   * mode.
   *
   * @param string $maintenanceModeTime
   */
  public function setMaintenanceModeTime($maintenanceModeTime)
  {
    $this->maintenanceModeTime = $maintenanceModeTime;
  }
  /**
   * @return string
   */
  public function getMaintenanceModeTime()
  {
    return $this->maintenanceModeTime;
  }
  /**
   * The Skaffold support state for this release's version of Skaffold.
   *
   * Accepted values: SKAFFOLD_SUPPORT_STATE_UNSPECIFIED,
   * SKAFFOLD_SUPPORT_STATE_SUPPORTED, SKAFFOLD_SUPPORT_STATE_MAINTENANCE_MODE,
   * SKAFFOLD_SUPPORT_STATE_UNSUPPORTED
   *
   * @param self::SKAFFOLD_SUPPORT_STATE_* $skaffoldSupportState
   */
  public function setSkaffoldSupportState($skaffoldSupportState)
  {
    $this->skaffoldSupportState = $skaffoldSupportState;
  }
  /**
   * @return self::SKAFFOLD_SUPPORT_STATE_*
   */
  public function getSkaffoldSupportState()
  {
    return $this->skaffoldSupportState;
  }
  /**
   * True if the version of Skaffold used by this release is supported.
   *
   * @param bool $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return bool
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * The time at which this release's version of Skaffold will no longer be
   * supported.
   *
   * @param string $supportExpirationTime
   */
  public function setSupportExpirationTime($supportExpirationTime)
  {
    $this->supportExpirationTime = $supportExpirationTime;
  }
  /**
   * @return string
   */
  public function getSupportExpirationTime()
  {
    return $this->supportExpirationTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SkaffoldSupportedCondition::class, 'Google_Service_CloudDeploy_SkaffoldSupportedCondition');
