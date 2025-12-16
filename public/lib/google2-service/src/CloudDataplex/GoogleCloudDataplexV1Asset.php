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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1Asset extends \Google\Model
{
  /**
   * State is not specified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Resource is active, i.e., ready to use.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * Resource is under creation.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * Resource is under deletion.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Resource is active but has unresolved actions.
   */
  public const STATE_ACTION_REQUIRED = 'ACTION_REQUIRED';
  /**
   * Output only. The time when the asset was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Description of the asset.
   *
   * @var string
   */
  public $description;
  protected $discoverySpecType = GoogleCloudDataplexV1AssetDiscoverySpec::class;
  protected $discoverySpecDataType = '';
  protected $discoveryStatusType = GoogleCloudDataplexV1AssetDiscoveryStatus::class;
  protected $discoveryStatusDataType = '';
  /**
   * Optional. User friendly display name.
   *
   * @var string
   */
  public $displayName;
  /**
   * Optional. User defined labels for the asset.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. The relative resource name of the asset, of the form: projects
   * /{project_number}/locations/{location_id}/lakes/{lake_id}/zones/{zone_id}/a
   * ssets/{asset_id}.
   *
   * @var string
   */
  public $name;
  protected $resourceSpecType = GoogleCloudDataplexV1AssetResourceSpec::class;
  protected $resourceSpecDataType = '';
  protected $resourceStatusType = GoogleCloudDataplexV1AssetResourceStatus::class;
  protected $resourceStatusDataType = '';
  protected $securityStatusType = GoogleCloudDataplexV1AssetSecurityStatus::class;
  protected $securityStatusDataType = '';
  /**
   * Output only. Current state of the asset.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. System generated globally unique ID for the asset. This ID
   * will be different if the asset is deleted and re-created with the same
   * name.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. The time when the asset was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The time when the asset was created.
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
   * Optional. Description of the asset.
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
   * Optional. Specification of the discovery feature applied to data referenced
   * by this asset. When this spec is left unset, the asset will use the spec
   * set on the parent zone.
   *
   * @param GoogleCloudDataplexV1AssetDiscoverySpec $discoverySpec
   */
  public function setDiscoverySpec(GoogleCloudDataplexV1AssetDiscoverySpec $discoverySpec)
  {
    $this->discoverySpec = $discoverySpec;
  }
  /**
   * @return GoogleCloudDataplexV1AssetDiscoverySpec
   */
  public function getDiscoverySpec()
  {
    return $this->discoverySpec;
  }
  /**
   * Output only. Status of the discovery feature applied to data referenced by
   * this asset.
   *
   * @param GoogleCloudDataplexV1AssetDiscoveryStatus $discoveryStatus
   */
  public function setDiscoveryStatus(GoogleCloudDataplexV1AssetDiscoveryStatus $discoveryStatus)
  {
    $this->discoveryStatus = $discoveryStatus;
  }
  /**
   * @return GoogleCloudDataplexV1AssetDiscoveryStatus
   */
  public function getDiscoveryStatus()
  {
    return $this->discoveryStatus;
  }
  /**
   * Optional. User friendly display name.
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
   * Optional. User defined labels for the asset.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Output only. The relative resource name of the asset, of the form: projects
   * /{project_number}/locations/{location_id}/lakes/{lake_id}/zones/{zone_id}/a
   * ssets/{asset_id}.
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
   * Required. Specification of the resource that is referenced by this asset.
   *
   * @param GoogleCloudDataplexV1AssetResourceSpec $resourceSpec
   */
  public function setResourceSpec(GoogleCloudDataplexV1AssetResourceSpec $resourceSpec)
  {
    $this->resourceSpec = $resourceSpec;
  }
  /**
   * @return GoogleCloudDataplexV1AssetResourceSpec
   */
  public function getResourceSpec()
  {
    return $this->resourceSpec;
  }
  /**
   * Output only. Status of the resource referenced by this asset.
   *
   * @param GoogleCloudDataplexV1AssetResourceStatus $resourceStatus
   */
  public function setResourceStatus(GoogleCloudDataplexV1AssetResourceStatus $resourceStatus)
  {
    $this->resourceStatus = $resourceStatus;
  }
  /**
   * @return GoogleCloudDataplexV1AssetResourceStatus
   */
  public function getResourceStatus()
  {
    return $this->resourceStatus;
  }
  /**
   * Output only. Status of the security policy applied to resource referenced
   * by this asset.
   *
   * @param GoogleCloudDataplexV1AssetSecurityStatus $securityStatus
   */
  public function setSecurityStatus(GoogleCloudDataplexV1AssetSecurityStatus $securityStatus)
  {
    $this->securityStatus = $securityStatus;
  }
  /**
   * @return GoogleCloudDataplexV1AssetSecurityStatus
   */
  public function getSecurityStatus()
  {
    return $this->securityStatus;
  }
  /**
   * Output only. Current state of the asset.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, CREATING, DELETING,
   * ACTION_REQUIRED
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
   * Output only. System generated globally unique ID for the asset. This ID
   * will be different if the asset is deleted and re-created with the same
   * name.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
  /**
   * Output only. The time when the asset was last updated.
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
class_alias(GoogleCloudDataplexV1Asset::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1Asset');
