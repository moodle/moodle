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

class ToolVersionSupportedCondition extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const TOOL_VERSION_SUPPORT_STATE_TOOL_VERSION_SUPPORT_STATE_UNSPECIFIED = 'TOOL_VERSION_SUPPORT_STATE_UNSPECIFIED';
  /**
   * This Tool version is currently supported.
   */
  public const TOOL_VERSION_SUPPORT_STATE_TOOL_VERSION_SUPPORT_STATE_SUPPORTED = 'TOOL_VERSION_SUPPORT_STATE_SUPPORTED';
  /**
   * This Tool version is in maintenance mode.
   */
  public const TOOL_VERSION_SUPPORT_STATE_TOOL_VERSION_SUPPORT_STATE_MAINTENANCE_MODE = 'TOOL_VERSION_SUPPORT_STATE_MAINTENANCE_MODE';
  /**
   * This Tool version is no longer supported.
   */
  public const TOOL_VERSION_SUPPORT_STATE_TOOL_VERSION_SUPPORT_STATE_UNSUPPORTED = 'TOOL_VERSION_SUPPORT_STATE_UNSUPPORTED';
  /**
   * Output only. The time at which this release's version of the Tool will
   * enter maintenance mode.
   *
   * @var string
   */
  public $maintenanceModeTime;
  /**
   * Output only. True if the version of Tool used by this release is supported.
   *
   * @var bool
   */
  public $status;
  /**
   * Output only. The time at which this release's version of the Tool will no
   * longer be supported.
   *
   * @var string
   */
  public $supportExpirationTime;
  /**
   * Output only. The Tool support state for this release's version of the Tool.
   *
   * @var string
   */
  public $toolVersionSupportState;

  /**
   * Output only. The time at which this release's version of the Tool will
   * enter maintenance mode.
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
   * Output only. True if the version of Tool used by this release is supported.
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
   * Output only. The time at which this release's version of the Tool will no
   * longer be supported.
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
  /**
   * Output only. The Tool support state for this release's version of the Tool.
   *
   * Accepted values: TOOL_VERSION_SUPPORT_STATE_UNSPECIFIED,
   * TOOL_VERSION_SUPPORT_STATE_SUPPORTED,
   * TOOL_VERSION_SUPPORT_STATE_MAINTENANCE_MODE,
   * TOOL_VERSION_SUPPORT_STATE_UNSUPPORTED
   *
   * @param self::TOOL_VERSION_SUPPORT_STATE_* $toolVersionSupportState
   */
  public function setToolVersionSupportState($toolVersionSupportState)
  {
    $this->toolVersionSupportState = $toolVersionSupportState;
  }
  /**
   * @return self::TOOL_VERSION_SUPPORT_STATE_*
   */
  public function getToolVersionSupportState()
  {
    return $this->toolVersionSupportState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ToolVersionSupportedCondition::class, 'Google_Service_CloudDeploy_ToolVersionSupportedCondition');
