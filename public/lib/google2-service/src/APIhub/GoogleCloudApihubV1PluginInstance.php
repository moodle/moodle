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

namespace Google\Service\APIhub;

class GoogleCloudApihubV1PluginInstance extends \Google\Collection
{
  /**
   * Default unspecified state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The plugin instance is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The plugin instance is active and ready for executions. This is the only
   * state where executions can run on the plugin instance.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The updated config that contains additional_config and auth_config is being
   * applied.
   */
  public const STATE_APPLYING_CONFIG = 'APPLYING_CONFIG';
  /**
   * The ERROR state can come while applying config. Users can retrigger
   * ApplyPluginInstanceConfig to restore the plugin instance back to active
   * state. Note, In case the ERROR state happens while applying config
   * (auth_config, additional_config), the plugin instance will reflect the
   * config which was trying to be applied while error happened. In order to
   * overwrite, trigger ApplyConfig with a new config.
   */
  public const STATE_ERROR = 'ERROR';
  /**
   * The plugin instance is in a failed state. This indicates that an
   * unrecoverable error occurred during a previous operation (Create, Delete).
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The plugin instance is being deleted. Delete is only possible if there is
   * no other operation running on the plugin instance and plugin instance
   * action.
   */
  public const STATE_DELETING = 'DELETING';
  protected $collection_key = 'actions';
  protected $actionsType = GoogleCloudApihubV1PluginInstanceAction::class;
  protected $actionsDataType = 'array';
  protected $additionalConfigType = GoogleCloudApihubV1ConfigVariable::class;
  protected $additionalConfigDataType = 'map';
  protected $authConfigType = GoogleCloudApihubV1AuthConfig::class;
  protected $authConfigDataType = '';
  /**
   * Output only. Timestamp indicating when the plugin instance was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. The display name for this plugin instance. Max length is 255
   * characters.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. Error message describing the failure, if any, during Create,
   * Delete or ApplyConfig operation corresponding to the plugin instance.This
   * field will only be populated if the plugin instance is in the ERROR or
   * FAILED state.
   *
   * @var string
   */
  public $errorMessage;
  /**
   * Identifier. The unique name of the plugin instance resource. Format: `proje
   * cts/{project}/locations/{location}/plugins/{plugin}/instances/{instance}`
   *
   * @var string
   */
  public $name;
  protected $sourceEnvironmentsConfigType = GoogleCloudApihubV1SourceEnvironment::class;
  protected $sourceEnvironmentsConfigDataType = 'map';
  /**
   * Optional. The source project id of the plugin instance. This will be the id
   * of runtime project in case of gcp based plugins and org id in case of non
   * gcp based plugins. This field will be a required field for Google provided
   * on-ramp plugins.
   *
   * @var string
   */
  public $sourceProjectId;
  /**
   * Output only. The current state of the plugin instance (e.g., enabled,
   * disabled, provisioning).
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Timestamp indicating when the plugin instance was last
   * updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Required. The action status for the plugin instance.
   *
   * @param GoogleCloudApihubV1PluginInstanceAction[] $actions
   */
  public function setActions($actions)
  {
    $this->actions = $actions;
  }
  /**
   * @return GoogleCloudApihubV1PluginInstanceAction[]
   */
  public function getActions()
  {
    return $this->actions;
  }
  /**
   * Optional. The additional information for this plugin instance corresponding
   * to the additional config template of the plugin. This information will be
   * sent to plugin hosting service on each call to plugin hosted service. The
   * key will be the config_variable_template.display_name to uniquely identify
   * the config variable.
   *
   * @param GoogleCloudApihubV1ConfigVariable[] $additionalConfig
   */
  public function setAdditionalConfig($additionalConfig)
  {
    $this->additionalConfig = $additionalConfig;
  }
  /**
   * @return GoogleCloudApihubV1ConfigVariable[]
   */
  public function getAdditionalConfig()
  {
    return $this->additionalConfig;
  }
  /**
   * Optional. The authentication information for this plugin instance.
   *
   * @param GoogleCloudApihubV1AuthConfig $authConfig
   */
  public function setAuthConfig(GoogleCloudApihubV1AuthConfig $authConfig)
  {
    $this->authConfig = $authConfig;
  }
  /**
   * @return GoogleCloudApihubV1AuthConfig
   */
  public function getAuthConfig()
  {
    return $this->authConfig;
  }
  /**
   * Output only. Timestamp indicating when the plugin instance was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Required. The display name for this plugin instance. Max length is 255
   * characters.
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
   * Output only. Error message describing the failure, if any, during Create,
   * Delete or ApplyConfig operation corresponding to the plugin instance.This
   * field will only be populated if the plugin instance is in the ERROR or
   * FAILED state.
   *
   * @param string $errorMessage
   */
  public function setErrorMessage($errorMessage)
  {
    $this->errorMessage = $errorMessage;
  }
  /**
   * @return string
   */
  public function getErrorMessage()
  {
    return $this->errorMessage;
  }
  /**
   * Identifier. The unique name of the plugin instance resource. Format: `proje
   * cts/{project}/locations/{location}/plugins/{plugin}/instances/{instance}`
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
   * Optional. The source environment's config present in the gateway instance
   * linked to the plugin instance. The key is the `source_environment` name
   * from the SourceEnvironment message.
   *
   * @param GoogleCloudApihubV1SourceEnvironment[] $sourceEnvironmentsConfig
   */
  public function setSourceEnvironmentsConfig($sourceEnvironmentsConfig)
  {
    $this->sourceEnvironmentsConfig = $sourceEnvironmentsConfig;
  }
  /**
   * @return GoogleCloudApihubV1SourceEnvironment[]
   */
  public function getSourceEnvironmentsConfig()
  {
    return $this->sourceEnvironmentsConfig;
  }
  /**
   * Optional. The source project id of the plugin instance. This will be the id
   * of runtime project in case of gcp based plugins and org id in case of non
   * gcp based plugins. This field will be a required field for Google provided
   * on-ramp plugins.
   *
   * @param string $sourceProjectId
   */
  public function setSourceProjectId($sourceProjectId)
  {
    $this->sourceProjectId = $sourceProjectId;
  }
  /**
   * @return string
   */
  public function getSourceProjectId()
  {
    return $this->sourceProjectId;
  }
  /**
   * Output only. The current state of the plugin instance (e.g., enabled,
   * disabled, provisioning).
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, ACTIVE, APPLYING_CONFIG,
   * ERROR, FAILED, DELETING
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
   * Output only. Timestamp indicating when the plugin instance was last
   * updated.
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
class_alias(GoogleCloudApihubV1PluginInstance::class, 'Google_Service_APIhub_GoogleCloudApihubV1PluginInstance');
