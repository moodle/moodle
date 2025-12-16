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

namespace Google\Service\SecurityCommandCenter;

class EffectiveEventThreatDetectionCustomModule extends \Google\Model
{
  /**
   * Unspecified cloud provider.
   */
  public const CLOUD_PROVIDER_CLOUD_PROVIDER_UNSPECIFIED = 'CLOUD_PROVIDER_UNSPECIFIED';
  /**
   * Google Cloud.
   */
  public const CLOUD_PROVIDER_GOOGLE_CLOUD_PLATFORM = 'GOOGLE_CLOUD_PLATFORM';
  /**
   * Amazon Web Services.
   */
  public const CLOUD_PROVIDER_AMAZON_WEB_SERVICES = 'AMAZON_WEB_SERVICES';
  /**
   * Microsoft Azure.
   */
  public const CLOUD_PROVIDER_MICROSOFT_AZURE = 'MICROSOFT_AZURE';
  /**
   * Unspecified enablement state.
   */
  public const ENABLEMENT_STATE_ENABLEMENT_STATE_UNSPECIFIED = 'ENABLEMENT_STATE_UNSPECIFIED';
  /**
   * The module is enabled at the given level.
   */
  public const ENABLEMENT_STATE_ENABLED = 'ENABLED';
  /**
   * The module is disabled at the given level.
   */
  public const ENABLEMENT_STATE_DISABLED = 'DISABLED';
  /**
   * The cloud provider of the custom module.
   *
   * @var string
   */
  public $cloudProvider;
  /**
   * Output only. Config for the effective module.
   *
   * @var array[]
   */
  public $config;
  /**
   * Output only. The description for the module.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. The human readable name to be displayed for the module.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. The effective state of enablement for the module at the given
   * level of the hierarchy.
   *
   * @var string
   */
  public $enablementState;
  /**
   * Output only. The resource name of the effective ETD custom module. Its
   * format is: * `organizations/{organization}/eventThreatDetectionSettings/eff
   * ectiveCustomModules/{module}`. * `folders/{folder}/eventThreatDetectionSett
   * ings/effectiveCustomModules/{module}`. * `projects/{project}/eventThreatDet
   * ectionSettings/effectiveCustomModules/{module}`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Type for the module. e.g. CONFIGURABLE_BAD_IP.
   *
   * @var string
   */
  public $type;

  /**
   * The cloud provider of the custom module.
   *
   * Accepted values: CLOUD_PROVIDER_UNSPECIFIED, GOOGLE_CLOUD_PLATFORM,
   * AMAZON_WEB_SERVICES, MICROSOFT_AZURE
   *
   * @param self::CLOUD_PROVIDER_* $cloudProvider
   */
  public function setCloudProvider($cloudProvider)
  {
    $this->cloudProvider = $cloudProvider;
  }
  /**
   * @return self::CLOUD_PROVIDER_*
   */
  public function getCloudProvider()
  {
    return $this->cloudProvider;
  }
  /**
   * Output only. Config for the effective module.
   *
   * @param array[] $config
   */
  public function setConfig($config)
  {
    $this->config = $config;
  }
  /**
   * @return array[]
   */
  public function getConfig()
  {
    return $this->config;
  }
  /**
   * Output only. The description for the module.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Output only. The human readable name to be displayed for the module.
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
   * Output only. The effective state of enablement for the module at the given
   * level of the hierarchy.
   *
   * Accepted values: ENABLEMENT_STATE_UNSPECIFIED, ENABLED, DISABLED
   *
   * @param self::ENABLEMENT_STATE_* $enablementState
   */
  public function setEnablementState($enablementState)
  {
    $this->enablementState = $enablementState;
  }
  /**
   * @return self::ENABLEMENT_STATE_*
   */
  public function getEnablementState()
  {
    return $this->enablementState;
  }
  /**
   * Output only. The resource name of the effective ETD custom module. Its
   * format is: * `organizations/{organization}/eventThreatDetectionSettings/eff
   * ectiveCustomModules/{module}`. * `folders/{folder}/eventThreatDetectionSett
   * ings/effectiveCustomModules/{module}`. * `projects/{project}/eventThreatDet
   * ectionSettings/effectiveCustomModules/{module}`.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. Type for the module. e.g. CONFIGURABLE_BAD_IP.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EffectiveEventThreatDetectionCustomModule::class, 'Google_Service_SecurityCommandCenter_EffectiveEventThreatDetectionCustomModule');
