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

class GoogleCloudSecuritycenterV2MuteConfig extends \Google\Model
{
  /**
   * Unused.
   */
  public const TYPE_MUTE_CONFIG_TYPE_UNSPECIFIED = 'MUTE_CONFIG_TYPE_UNSPECIFIED';
  /**
   * A static mute config, which sets the static mute state of future matching
   * findings to muted. Once the static mute state has been set, finding or
   * config modifications will not affect the state.
   */
  public const TYPE_STATIC = 'STATIC';
  /**
   * A dynamic mute config, which is applied to existing and future matching
   * findings, setting their dynamic mute state to "muted". If the config is
   * updated or deleted, or a matching finding is updated, such that the finding
   * doesn't match the config, the config will be removed from the finding, and
   * the finding's dynamic mute state may become "unmuted" (unless other configs
   * still match).
   */
  public const TYPE_DYNAMIC = 'DYNAMIC';
  /**
   * Output only. The time at which the mute config was created. This field is
   * set by the server and will be ignored if provided on config creation.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The resource name of the Cloud KMS `CryptoKey` used to encrypt
   * this configuration data, if CMEK was enabled during Security Command Center
   * activation.
   *
   * @var string
   */
  public $cryptoKeyName;
  /**
   * A description of the mute config.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. The expiry of the mute config. Only applicable for dynamic
   * configs. If the expiry is set, when the config expires, it is removed from
   * all findings.
   *
   * @var string
   */
  public $expiryTime;
  /**
   * Required. An expression that defines the filter to apply across
   * create/update events of findings. While creating a filter string, be
   * mindful of the scope in which the mute configuration is being created.
   * E.g., If a filter contains project = X but is created under the project = Y
   * scope, it might not match any findings. The following field and operator
   * combinations are supported: * severity: `=`, `:` * category: `=`, `:` *
   * resource.name: `=`, `:` * resource.project_name: `=`, `:` *
   * resource.project_display_name: `=`, `:` * resource.folders.resource_folder:
   * `=`, `:` * resource.parent_name: `=`, `:` * resource.parent_display_name:
   * `=`, `:` * resource.type: `=`, `:` * finding_class: `=`, `:` *
   * indicator.ip_addresses: `=`, `:` * indicator.domains: `=`, `:`
   *
   * @var string
   */
  public $filter;
  /**
   * Output only. Email address of the user who last edited the mute config.
   * This field is set by the server and will be ignored if provided on config
   * creation or update.
   *
   * @var string
   */
  public $mostRecentEditor;
  /**
   * Identifier. This field will be ignored if provided on config creation. The
   * following list shows some examples of the format: +
   * `organizations/{organization}/muteConfigs/{mute_config}` + `organizations/{
   * organization}locations/{location}//muteConfigs/{mute_config}` +
   * `folders/{folder}/muteConfigs/{mute_config}` +
   * `folders/{folder}/locations/{location}/muteConfigs/{mute_config}` +
   * `projects/{project}/muteConfigs/{mute_config}` +
   * `projects/{project}/locations/{location}/muteConfigs/{mute_config}`
   *
   * @var string
   */
  public $name;
  /**
   * Required. The type of the mute config, which determines what type of mute
   * state the config affects. Immutable after creation.
   *
   * @var string
   */
  public $type;
  /**
   * Output only. The most recent time at which the mute config was updated.
   * This field is set by the server and will be ignored if provided on config
   * creation or update.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The time at which the mute config was created. This field is
   * set by the server and will be ignored if provided on config creation.
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
   * Output only. The resource name of the Cloud KMS `CryptoKey` used to encrypt
   * this configuration data, if CMEK was enabled during Security Command Center
   * activation.
   *
   * @param string $cryptoKeyName
   */
  public function setCryptoKeyName($cryptoKeyName)
  {
    $this->cryptoKeyName = $cryptoKeyName;
  }
  /**
   * @return string
   */
  public function getCryptoKeyName()
  {
    return $this->cryptoKeyName;
  }
  /**
   * A description of the mute config.
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
   * Optional. The expiry of the mute config. Only applicable for dynamic
   * configs. If the expiry is set, when the config expires, it is removed from
   * all findings.
   *
   * @param string $expiryTime
   */
  public function setExpiryTime($expiryTime)
  {
    $this->expiryTime = $expiryTime;
  }
  /**
   * @return string
   */
  public function getExpiryTime()
  {
    return $this->expiryTime;
  }
  /**
   * Required. An expression that defines the filter to apply across
   * create/update events of findings. While creating a filter string, be
   * mindful of the scope in which the mute configuration is being created.
   * E.g., If a filter contains project = X but is created under the project = Y
   * scope, it might not match any findings. The following field and operator
   * combinations are supported: * severity: `=`, `:` * category: `=`, `:` *
   * resource.name: `=`, `:` * resource.project_name: `=`, `:` *
   * resource.project_display_name: `=`, `:` * resource.folders.resource_folder:
   * `=`, `:` * resource.parent_name: `=`, `:` * resource.parent_display_name:
   * `=`, `:` * resource.type: `=`, `:` * finding_class: `=`, `:` *
   * indicator.ip_addresses: `=`, `:` * indicator.domains: `=`, `:`
   *
   * @param string $filter
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * Output only. Email address of the user who last edited the mute config.
   * This field is set by the server and will be ignored if provided on config
   * creation or update.
   *
   * @param string $mostRecentEditor
   */
  public function setMostRecentEditor($mostRecentEditor)
  {
    $this->mostRecentEditor = $mostRecentEditor;
  }
  /**
   * @return string
   */
  public function getMostRecentEditor()
  {
    return $this->mostRecentEditor;
  }
  /**
   * Identifier. This field will be ignored if provided on config creation. The
   * following list shows some examples of the format: +
   * `organizations/{organization}/muteConfigs/{mute_config}` + `organizations/{
   * organization}locations/{location}//muteConfigs/{mute_config}` +
   * `folders/{folder}/muteConfigs/{mute_config}` +
   * `folders/{folder}/locations/{location}/muteConfigs/{mute_config}` +
   * `projects/{project}/muteConfigs/{mute_config}` +
   * `projects/{project}/locations/{location}/muteConfigs/{mute_config}`
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
   * Required. The type of the mute config, which determines what type of mute
   * state the config affects. Immutable after creation.
   *
   * Accepted values: MUTE_CONFIG_TYPE_UNSPECIFIED, STATIC, DYNAMIC
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Output only. The most recent time at which the mute config was updated.
   * This field is set by the server and will be ignored if provided on config
   * creation or update.
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
class_alias(GoogleCloudSecuritycenterV2MuteConfig::class, 'Google_Service_SecurityCommandCenter_GoogleCloudSecuritycenterV2MuteConfig');
