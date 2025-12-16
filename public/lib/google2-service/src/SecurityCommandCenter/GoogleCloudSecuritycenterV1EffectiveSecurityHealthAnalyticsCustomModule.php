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

class GoogleCloudSecuritycenterV1EffectiveSecurityHealthAnalyticsCustomModule extends \Google\Model
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
  protected $customConfigType = GoogleCloudSecuritycenterV1CustomConfig::class;
  protected $customConfigDataType = '';
  /**
   * Output only. The display name for the custom module. The name must be
   * between 1 and 128 characters, start with a lowercase letter, and contain
   * alphanumeric characters or underscores only.
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
   * Output only. The resource name of the custom module. Its format is "organiz
   * ations/{organization}/securityHealthAnalyticsSettings/effectiveCustomModule
   * s/{customModule}", or "folders/{folder}/securityHealthAnalyticsSettings/eff
   * ectiveCustomModules/{customModule}", or "projects/{project}/securityHealthA
   * nalyticsSettings/effectiveCustomModules/{customModule}"
   *
   * @var string
   */
  public $name;

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
   * Output only. The user-specified configuration for the module.
   *
   * @param GoogleCloudSecuritycenterV1CustomConfig $customConfig
   */
  public function setCustomConfig(GoogleCloudSecuritycenterV1CustomConfig $customConfig)
  {
    $this->customConfig = $customConfig;
  }
  /**
   * @return GoogleCloudSecuritycenterV1CustomConfig
   */
  public function getCustomConfig()
  {
    return $this->customConfig;
  }
  /**
   * Output only. The display name for the custom module. The name must be
   * between 1 and 128 characters, start with a lowercase letter, and contain
   * alphanumeric characters or underscores only.
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
   * Output only. The resource name of the custom module. Its format is "organiz
   * ations/{organization}/securityHealthAnalyticsSettings/effectiveCustomModule
   * s/{customModule}", or "folders/{folder}/securityHealthAnalyticsSettings/eff
   * ectiveCustomModules/{customModule}", or "projects/{project}/securityHealthA
   * nalyticsSettings/effectiveCustomModules/{customModule}"
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudSecuritycenterV1EffectiveSecurityHealthAnalyticsCustomModule::class, 'Google_Service_SecurityCommandCenter_GoogleCloudSecuritycenterV1EffectiveSecurityHealthAnalyticsCustomModule');
