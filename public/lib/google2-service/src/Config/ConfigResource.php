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

namespace Google\Service\Config;

class ConfigResource extends \Google\Model
{
  /**
   * The default value. This value is used if the intent is omitted.
   */
  public const INTENT_INTENT_UNSPECIFIED = 'INTENT_UNSPECIFIED';
  /**
   * Infra Manager will create this Resource.
   */
  public const INTENT_CREATE = 'CREATE';
  /**
   * Infra Manager will update this Resource.
   */
  public const INTENT_UPDATE = 'UPDATE';
  /**
   * Infra Manager will delete this Resource.
   */
  public const INTENT_DELETE = 'DELETE';
  /**
   * Infra Manager will destroy and recreate this Resource.
   */
  public const INTENT_RECREATE = 'RECREATE';
  /**
   * Infra Manager will leave this Resource untouched.
   */
  public const INTENT_UNCHANGED = 'UNCHANGED';
  /**
   * The default value. This value is used if the state is omitted.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Resource has been planned for reconcile.
   */
  public const STATE_PLANNED = 'PLANNED';
  /**
   * Resource is actively reconciling into the intended state.
   */
  public const STATE_IN_PROGRESS = 'IN_PROGRESS';
  /**
   * Resource has reconciled to intended state.
   */
  public const STATE_RECONCILED = 'RECONCILED';
  /**
   * Resource failed to reconcile.
   */
  public const STATE_FAILED = 'FAILED';
  protected $caiAssetsType = ResourceCAIInfo::class;
  protected $caiAssetsDataType = 'map';
  /**
   * Output only. Intent of the resource.
   *
   * @var string
   */
  public $intent;
  /**
   * Output only. Resource name. Format: `projects/{project}/locations/{location
   * }/deployments/{deployment}/revisions/{revision}/resources/{resource}`
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Current state of the resource.
   *
   * @var string
   */
  public $state;
  protected $terraformInfoType = ResourceTerraformInfo::class;
  protected $terraformInfoDataType = '';

  /**
   * Output only. Map of Cloud Asset Inventory (CAI) type to CAI info (e.g. CAI
   * ID). CAI type format follows https://cloud.google.com/asset-
   * inventory/docs/supported-asset-types
   *
   * @param ResourceCAIInfo[] $caiAssets
   */
  public function setCaiAssets($caiAssets)
  {
    $this->caiAssets = $caiAssets;
  }
  /**
   * @return ResourceCAIInfo[]
   */
  public function getCaiAssets()
  {
    return $this->caiAssets;
  }
  /**
   * Output only. Intent of the resource.
   *
   * Accepted values: INTENT_UNSPECIFIED, CREATE, UPDATE, DELETE, RECREATE,
   * UNCHANGED
   *
   * @param self::INTENT_* $intent
   */
  public function setIntent($intent)
  {
    $this->intent = $intent;
  }
  /**
   * @return self::INTENT_*
   */
  public function getIntent()
  {
    return $this->intent;
  }
  /**
   * Output only. Resource name. Format: `projects/{project}/locations/{location
   * }/deployments/{deployment}/revisions/{revision}/resources/{resource}`
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
   * Output only. Current state of the resource.
   *
   * Accepted values: STATE_UNSPECIFIED, PLANNED, IN_PROGRESS, RECONCILED,
   * FAILED
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
   * Output only. Terraform-specific info if this resource was created using
   * Terraform.
   *
   * @param ResourceTerraformInfo $terraformInfo
   */
  public function setTerraformInfo(ResourceTerraformInfo $terraformInfo)
  {
    $this->terraformInfo = $terraformInfo;
  }
  /**
   * @return ResourceTerraformInfo
   */
  public function getTerraformInfo()
  {
    return $this->terraformInfo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConfigResource::class, 'Google_Service_Config_ConfigResource');
