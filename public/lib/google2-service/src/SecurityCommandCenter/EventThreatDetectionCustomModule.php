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

class EventThreatDetectionCustomModule extends \Google\Model
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
   * The module is enabled at the given level.
   */
  public const ENABLEMENT_STATE_ENABLED = 'ENABLED';
  /**
   * The module is disabled at the given level.
   */
  public const ENABLEMENT_STATE_DISABLED = 'DISABLED';
  /**
   * When the enablement state is inherited.
   */
  public const ENABLEMENT_STATE_INHERITED = 'INHERITED';
  /**
   * Output only. The closest ancestor module that this module inherits the
   * enablement state from. The format is the same as the
   * EventThreatDetectionCustomModule resource name.
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
  /**
   * Config for the module. For the resident module, its config value is defined
   * at this level. For the inherited module, its config value is inherited from
   * the ancestor module.
   *
   * @var array[]
   */
  public $config;
  /**
   * The description for the module.
   *
   * @var string
   */
  public $description;
  /**
   * The human readable name to be displayed for the module.
   *
   * @var string
   */
  public $displayName;
  /**
   * The state of enablement for the module at the given level of the hierarchy.
   *
   * @var string
   */
  public $enablementState;
  /**
   * Output only. The editor the module was last updated by.
   *
   * @var string
   */
  public $lastEditor;
  /**
   * Immutable. The resource name of the Event Threat Detection custom module.
   * Its format is: * `organizations/{organization}/eventThreatDetectionSettings
   * /customModules/{module}`. *
   * `folders/{folder}/eventThreatDetectionSettings/customModules/{module}`. *
   * `projects/{project}/eventThreatDetectionSettings/customModules/{module}`.
   *
   * @var string
   */
  public $name;
  /**
   * Type for the module. e.g. CONFIGURABLE_BAD_IP.
   *
   * @var string
   */
  public $type;
  /**
   * Output only. The time the module was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The closest ancestor module that this module inherits the
   * enablement state from. The format is the same as the
   * EventThreatDetectionCustomModule resource name.
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
   * Config for the module. For the resident module, its config value is defined
   * at this level. For the inherited module, its config value is inherited from
   * the ancestor module.
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
   * The description for the module.
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
   * The human readable name to be displayed for the module.
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
   * The state of enablement for the module at the given level of the hierarchy.
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
   * Output only. The editor the module was last updated by.
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
   * Immutable. The resource name of the Event Threat Detection custom module.
   * Its format is: * `organizations/{organization}/eventThreatDetectionSettings
   * /customModules/{module}`. *
   * `folders/{folder}/eventThreatDetectionSettings/customModules/{module}`. *
   * `projects/{project}/eventThreatDetectionSettings/customModules/{module}`.
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
   * Type for the module. e.g. CONFIGURABLE_BAD_IP.
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
  /**
   * Output only. The time the module was last updated.
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
class_alias(EventThreatDetectionCustomModule::class, 'Google_Service_SecurityCommandCenter_EventThreatDetectionCustomModule');
