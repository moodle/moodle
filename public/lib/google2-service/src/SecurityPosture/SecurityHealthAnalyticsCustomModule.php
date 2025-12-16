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

class SecurityHealthAnalyticsCustomModule extends \Google\Model
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
  protected $configType = CustomConfig::class;
  protected $configDataType = '';
  /**
   * Optional. The display name of the custom module. This value is used as the
   * finding category for all the asset violation findings that the custom
   * module returns. The display name must contain between 1 and 128
   * alphanumeric characters or underscores, and it must start with a lowercase
   * letter.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. Immutable. The unique identifier for the custom module.
   * Contains 1 to 20 digits.
   *
   * @var string
   */
  public $id;
  /**
   * Whether the custom module is enabled at a specified level of the resource
   * hierarchy.
   *
   * @var string
   */
  public $moduleEnablementState;

  /**
   * Required. Configuration settings for the custom module.
   *
   * @param CustomConfig $config
   */
  public function setConfig(CustomConfig $config)
  {
    $this->config = $config;
  }
  /**
   * @return CustomConfig
   */
  public function getConfig()
  {
    return $this->config;
  }
  /**
   * Optional. The display name of the custom module. This value is used as the
   * finding category for all the asset violation findings that the custom
   * module returns. The display name must contain between 1 and 128
   * alphanumeric characters or underscores, and it must start with a lowercase
   * letter.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. Immutable. The unique identifier for the custom module.
   * Contains 1 to 20 digits.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Whether the custom module is enabled at a specified level of the resource
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SecurityHealthAnalyticsCustomModule::class, 'Google_Service_SecurityPosture_SecurityHealthAnalyticsCustomModule');
