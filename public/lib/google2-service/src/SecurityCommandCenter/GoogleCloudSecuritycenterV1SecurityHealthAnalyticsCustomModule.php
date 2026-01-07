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

class GoogleCloudSecuritycenterV1SecurityHealthAnalyticsCustomModule extends \Google\Model
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
   * Amazon Web Services (AWS).
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
   * The module is enabled at the given CRM resource.
   */
  public const ENABLEMENT_STATE_ENABLED = 'ENABLED';
  /**
   * The module is disabled at the given CRM resource.
   */
  public const ENABLEMENT_STATE_DISABLED = 'DISABLED';
  /**
   * State is inherited from an ancestor module. The module will either be
   * effectively ENABLED or DISABLED based on its closest non-inherited ancestor
   * module in the CRM hierarchy.
   */
  public const ENABLEMENT_STATE_INHERITED = 'INHERITED';
  /**
   * Output only. If empty, indicates that the custom module was created in the
   * organization, folder, or project in which you are viewing the custom
   * module. Otherwise, `ancestor_module` specifies the organization or folder
   * from which the custom module is inherited.
   *
   * @var string
   */
  public $ancestorModule;
  /**
   * The cloud provider of the custom module.
   *
   * @var string
   */
  public $cloudProvider;
  protected $customConfigType = GoogleCloudSecuritycenterV1CustomConfig::class;
  protected $customConfigDataType = '';
  /**
   * The display name of the Security Health Analytics custom module. This
   * display name becomes the finding category for all findings that are
   * returned by this custom module. The display name must be between 1 and 128
   * characters, start with a lowercase letter, and contain alphanumeric
   * characters or underscores only.
   *
   * @var string
   */
  public $displayName;
  /**
   * The enablement state of the custom module.
   *
   * @var string
   */
  public $enablementState;
  /**
   * Output only. The editor that last updated the custom module.
   *
   * @var string
   */
  public $lastEditor;
  /**
   * Immutable. The resource name of the custom module. Its format is "organizat
   * ions/{organization}/securityHealthAnalyticsSettings/customModules/{customMo
   * dule}", or "folders/{folder}/securityHealthAnalyticsSettings/customModules/
   * {customModule}", or "projects/{project}/securityHealthAnalyticsSettings/cus
   * tomModules/{customModule}" The id {customModule} is server-generated and is
   * not user settable. It will be a numeric id containing 1-20 digits.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The time at which the custom module was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. If empty, indicates that the custom module was created in the
   * organization, folder, or project in which you are viewing the custom
   * module. Otherwise, `ancestor_module` specifies the organization or folder
   * from which the custom module is inherited.
   *
   * @param string $ancestorModule
   */
  public function setAncestorModule($ancestorModule)
  {
    $this->ancestorModule = $ancestorModule;
  }
  /**
   * @return string
   */
  public function getAncestorModule()
  {
    return $this->ancestorModule;
  }
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
   * The user specified custom configuration for the module.
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
   * The display name of the Security Health Analytics custom module. This
   * display name becomes the finding category for all findings that are
   * returned by this custom module. The display name must be between 1 and 128
   * characters, start with a lowercase letter, and contain alphanumeric
   * characters or underscores only.
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
   * The enablement state of the custom module.
   *
   * Accepted values: ENABLEMENT_STATE_UNSPECIFIED, ENABLED, DISABLED, INHERITED
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
   * Output only. The editor that last updated the custom module.
   *
   * @param string $lastEditor
   */
  public function setLastEditor($lastEditor)
  {
    $this->lastEditor = $lastEditor;
  }
  /**
   * @return string
   */
  public function getLastEditor()
  {
    return $this->lastEditor;
  }
  /**
   * Immutable. The resource name of the custom module. Its format is "organizat
   * ions/{organization}/securityHealthAnalyticsSettings/customModules/{customMo
   * dule}", or "folders/{folder}/securityHealthAnalyticsSettings/customModules/
   * {customModule}", or "projects/{project}/securityHealthAnalyticsSettings/cus
   * tomModules/{customModule}" The id {customModule} is server-generated and is
   * not user settable. It will be a numeric id containing 1-20 digits.
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
   * Output only. The time at which the custom module was last updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudSecuritycenterV1SecurityHealthAnalyticsCustomModule::class, 'Google_Service_SecurityCommandCenter_GoogleCloudSecuritycenterV1SecurityHealthAnalyticsCustomModule');
