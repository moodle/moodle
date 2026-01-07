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

class OrganizationSettings extends \Google\Model
{
  protected $assetDiscoveryConfigType = AssetDiscoveryConfig::class;
  protected $assetDiscoveryConfigDataType = '';
  /**
   * A flag that indicates if Asset Discovery should be enabled. If the flag is
   * set to `true`, then discovery of assets will occur. If it is set to
   * `false`, all historical assets will remain, but discovery of future assets
   * will not occur.
   *
   * @var bool
   */
  public $enableAssetDiscovery;
  /**
   * The relative resource name of the settings. See:
   * https://cloud.google.com/apis/design/resource_names#relative_resource_name
   * Example: "organizations/{organization_id}/organizationSettings".
   *
   * @var string
   */
  public $name;

  /**
   * The configuration used for Asset Discovery runs.
   *
   * @param AssetDiscoveryConfig $assetDiscoveryConfig
   */
  public function setAssetDiscoveryConfig(AssetDiscoveryConfig $assetDiscoveryConfig)
  {
    $this->assetDiscoveryConfig = $assetDiscoveryConfig;
  }
  /**
   * @return AssetDiscoveryConfig
   */
  public function getAssetDiscoveryConfig()
  {
    return $this->assetDiscoveryConfig;
  }
  /**
   * A flag that indicates if Asset Discovery should be enabled. If the flag is
   * set to `true`, then discovery of assets will occur. If it is set to
   * `false`, all historical assets will remain, but discovery of future assets
   * will not occur.
   *
   * @param bool $enableAssetDiscovery
   */
  public function setEnableAssetDiscovery($enableAssetDiscovery)
  {
    $this->enableAssetDiscovery = $enableAssetDiscovery;
  }
  /**
   * @return bool
   */
  public function getEnableAssetDiscovery()
  {
    return $this->enableAssetDiscovery;
  }
  /**
   * The relative resource name of the settings. See:
   * https://cloud.google.com/apis/design/resource_names#relative_resource_name
   * Example: "organizations/{organization_id}/organizationSettings".
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
class_alias(OrganizationSettings::class, 'Google_Service_SecurityCommandCenter_OrganizationSettings');
