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

namespace Google\Service\SecurityPosture;

class SecurityHealthAnalyticsModule extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const MODULE_ENABLEMENT_STATE_ENABLEMENT_STATE_UNSPECIFIED = 'ENABLEMENT_STATE_UNSPECIFIED';
  /**
   * The detector or custom module is enabled.
   */
  public const MODULE_ENABLEMENT_STATE_ENABLED = 'ENABLED';
  /**
   * The detector or custom module is disabled.
   */
  public const MODULE_ENABLEMENT_STATE_DISABLED = 'DISABLED';
  /**
   * Whether the detector is enabled at a specified level of the resource
   * hierarchy.
   *
   * @var string
   */
  public $moduleEnablementState;
  /**
   * Required. The name of the detector. For example,
   * `BIGQUERY_TABLE_CMEK_DISABLED`. This field is also used as the finding
   * category for all the asset violation findings that the detector returns.
   *
   * @var string
   */
  public $moduleName;

  /**
   * Whether the detector is enabled at a specified level of the resource
   * hierarchy.
   *
   * Accepted values: ENABLEMENT_STATE_UNSPECIFIED, ENABLED, DISABLED
   *
   * @param self::MODULE_ENABLEMENT_STATE_* $moduleEnablementState
   */
  public function setModuleEnablementState($moduleEnablementState)
  {
    $this->moduleEnablementState = $moduleEnablementState;
  }
  /**
   * @return self::MODULE_ENABLEMENT_STATE_*
   */
  public function getModuleEnablementState()
  {
    return $this->moduleEnablementState;
  }
  /**
   * Required. The name of the detector. For example,
   * `BIGQUERY_TABLE_CMEK_DISABLED`. This field is also used as the finding
   * category for all the asset violation findings that the detector returns.
   *
   * @param string $moduleName
   */
  public function setModuleName($moduleName)
  {
    $this->moduleName = $moduleName;
  }
  /**
   * @return string
   */
  public function getModuleName()
  {
    return $this->moduleName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SecurityHealthAnalyticsModule::class, 'Google_Service_SecurityPosture_SecurityHealthAnalyticsModule');
