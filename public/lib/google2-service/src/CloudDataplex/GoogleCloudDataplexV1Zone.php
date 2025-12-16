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

class GoogleCloudDataplexV1Zone extends \Google\Model
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
   * Zone type not specified.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * A zone that contains data that needs further processing before it is
   * considered generally ready for consumption and analytics workloads.
   */
  public const TYPE_RAW = 'RAW';
  /**
   * A zone that contains data that is considered to be ready for broader
   * consumption and analytics workloads. Curated structured data stored in
   * Cloud Storage must conform to certain file formats (parquet, avro and orc)
   * and organized in a hive-compatible directory layout.
   */
  public const TYPE_CURATED = 'CURATED';
  protected $assetStatusType = GoogleCloudDataplexV1AssetStatus::class;
  protected $assetStatusDataType = '';
  /**
   * Output only. The time when the zone was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Description of the zone.
   *
   * @var string
   */
  public $description;
  protected $discoverySpecType = GoogleCloudDataplexV1ZoneDiscoverySpec::class;
  protected $discoverySpecDataType = '';
  /**
   * Optional. User friendly display name.
   *
   * @var string
   */
  public $displayName;
  /**
   * Optional. User defined labels for the zone.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. The relative resource name of the zone, of the form: projects/
   * {project_number}/locations/{location_id}/lakes/{lake_id}/zones/{zone_id}.
   *
   * @var string
   */
  public $name;
  protected $resourceSpecType = GoogleCloudDataplexV1ZoneResourceSpec::class;
  protected $resourceSpecDataType = '';
  /**
   * Output only. Current state of the zone.
   *
   * @var string
   */
  public $state;
  /**
   * Required. Immutable. The type of the zone.
   *
   * @var string
   */
  public $type;
  /**
   * Output only. System generated globally unique ID for the zone. This ID will
   * be different if the zone is deleted and re-created with the same name.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. The time when the zone was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Aggregated status of the underlying assets of the zone.
   *
   * @param GoogleCloudDataplexV1AssetStatus $assetStatus
   */
  public function setAssetStatus(GoogleCloudDataplexV1AssetStatus $assetStatus)
  {
    $this->assetStatus = $assetStatus;
  }
  /**
   * @return GoogleCloudDataplexV1AssetStatus
   */
  public function getAssetStatus()
  {
    return $this->assetStatus;
  }
  /**
   * Output only. The time when the zone was created.
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
   * Optional. Description of the zone.
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
   * Optional. Specification of the discovery feature applied to data in this
   * zone.
   *
   * @param GoogleCloudDataplexV1ZoneDiscoverySpec $discoverySpec
   */
  public function setDiscoverySpec(GoogleCloudDataplexV1ZoneDiscoverySpec $discoverySpec)
  {
    $this->discoverySpec = $discoverySpec;
  }
  /**
   * @return GoogleCloudDataplexV1ZoneDiscoverySpec
   */
  public function getDiscoverySpec()
  {
    return $this->discoverySpec;
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
   * Optional. User defined labels for the zone.
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
   * Output only. The relative resource name of the zone, of the form: projects/
   * {project_number}/locations/{location_id}/lakes/{lake_id}/zones/{zone_id}.
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
   * Required. Specification of the resources that are referenced by the assets
   * within this zone.
   *
   * @param GoogleCloudDataplexV1ZoneResourceSpec $resourceSpec
   */
  public function setResourceSpec(GoogleCloudDataplexV1ZoneResourceSpec $resourceSpec)
  {
    $this->resourceSpec = $resourceSpec;
  }
  /**
   * @return GoogleCloudDataplexV1ZoneResourceSpec
   */
  public function getResourceSpec()
  {
    return $this->resourceSpec;
  }
  /**
   * Output only. Current state of the zone.
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
   * Required. Immutable. The type of the zone.
   *
   * Accepted values: TYPE_UNSPECIFIED, RAW, CURATED
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
   * Output only. System generated globally unique ID for the zone. This ID will
   * be different if the zone is deleted and re-created with the same name.
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
   * Output only. The time when the zone was last updated.
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
class_alias(GoogleCloudDataplexV1Zone::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1Zone');
