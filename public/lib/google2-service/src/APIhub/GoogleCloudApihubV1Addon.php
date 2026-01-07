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

class GoogleCloudApihubV1Addon extends \Google\Model
{
  /**
   * The data source of the addon is not specified.
   */
  public const DATA_SOURCE_DATA_SOURCE_UNSPECIFIED = 'DATA_SOURCE_UNSPECIFIED';
  /**
   * Addon operates on data collected from specific plugin instances.
   */
  public const DATA_SOURCE_PLUGIN_INSTANCE = 'PLUGIN_INSTANCE';
  /**
   * Addon operates on all data in the API hub.
   */
  public const DATA_SOURCE_ALL_DATA = 'ALL_DATA';
  /**
   * The addon state is not specified.
   */
  public const STATE_ADDON_STATE_UNSPECIFIED = 'ADDON_STATE_UNSPECIFIED';
  /**
   * The addon is active.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The addon is being updated.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * The addon is in error state.
   */
  public const STATE_ERROR = 'ERROR';
  /**
   * The addon is inactive.
   */
  public const STATE_INACTIVE = 'INACTIVE';
  protected $configType = GoogleCloudApihubV1AddonConfig::class;
  protected $configDataType = '';
  /**
   * Output only. The time at which the addon was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. The data source on which the addon operates. This determines
   * which field in the `config` oneof is used.
   *
   * @var string
   */
  public $dataSource;
  /**
   * Optional. The description of the addon.
   *
   * @var string
   */
  public $description;
  /**
   * Required. The display name of the addon.
   *
   * @var string
   */
  public $displayName;
  /**
   * Identifier. The name of the addon to enable. Format:
   * `projects/{project}/locations/{location}/addons/{addon}`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The state of the addon.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The time at which the addon was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Required. The configuration of the addon.
   *
   * @param GoogleCloudApihubV1AddonConfig $config
   */
  public function setConfig(GoogleCloudApihubV1AddonConfig $config)
  {
    $this->config = $config;
  }
  /**
   * @return GoogleCloudApihubV1AddonConfig
   */
  public function getConfig()
  {
    return $this->config;
  }
  /**
   * Output only. The time at which the addon was created.
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
   * Required. The data source on which the addon operates. This determines
   * which field in the `config` oneof is used.
   *
   * Accepted values: DATA_SOURCE_UNSPECIFIED, PLUGIN_INSTANCE, ALL_DATA
   *
   * @param self::DATA_SOURCE_* $dataSource
   */
  public function setDataSource($dataSource)
  {
    $this->dataSource = $dataSource;
  }
  /**
   * @return self::DATA_SOURCE_*
   */
  public function getDataSource()
  {
    return $this->dataSource;
  }
  /**
   * Optional. The description of the addon.
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
   * Required. The display name of the addon.
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
   * Identifier. The name of the addon to enable. Format:
   * `projects/{project}/locations/{location}/addons/{addon}`.
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
   * Output only. The state of the addon.
   *
   * Accepted values: ADDON_STATE_UNSPECIFIED, ACTIVE, UPDATING, ERROR, INACTIVE
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
   * Output only. The time at which the addon was last updated.
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
class_alias(GoogleCloudApihubV1Addon::class, 'Google_Service_APIhub_GoogleCloudApihubV1Addon');
