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

namespace Google\Service\MigrationCenterAPI;

class Asset extends \Google\Collection
{
  protected $collection_key = 'sources';
  /**
   * Output only. The list of groups that the asset is assigned to.
   *
   * @var string[]
   */
  public $assignedGroups;
  /**
   * Generic asset attributes.
   *
   * @var string[]
   */
  public $attributes;
  /**
   * Output only. The timestamp when the asset was created.
   *
   * @var string
   */
  public $createTime;
  protected $databaseDeploymentDetailsType = DatabaseDeploymentDetails::class;
  protected $databaseDeploymentDetailsDataType = '';
  protected $databaseDetailsType = DatabaseDetails::class;
  protected $databaseDetailsDataType = '';
  /**
   * Optional. Indicates if the asset is hidden.
   *
   * @var bool
   */
  public $hidden;
  /**
   * Optional. An optional reason for marking this asset as hidden.
   *
   * @var string
   */
  public $hideReason;
  /**
   * Output only. The timestamp when the asset was marked as hidden.
   *
   * @var string
   */
  public $hideTime;
  protected $insightListType = InsightList::class;
  protected $insightListDataType = '';
  /**
   * Labels as key value pairs.
   *
   * @var string[]
   */
  public $labels;
  protected $machineDetailsType = MachineDetails::class;
  protected $machineDetailsDataType = '';
  /**
   * Output only. The full name of the asset.
   *
   * @var string
   */
  public $name;
  protected $performanceDataType = AssetPerformanceData::class;
  protected $performanceDataDataType = '';
  /**
   * Output only. The list of sources contributing to the asset.
   *
   * @var string[]
   */
  public $sources;
  /**
   * Output only. Server generated human readable name of the asset.
   *
   * @var string
   */
  public $title;
  /**
   * Output only. The timestamp when the asset was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The list of groups that the asset is assigned to.
   *
   * @param string[] $assignedGroups
   */
  public function setAssignedGroups($assignedGroups)
  {
    $this->assignedGroups = $assignedGroups;
  }
  /**
   * @return string[]
   */
  public function getAssignedGroups()
  {
    return $this->assignedGroups;
  }
  /**
   * Generic asset attributes.
   *
   * @param string[] $attributes
   */
  public function setAttributes($attributes)
  {
    $this->attributes = $attributes;
  }
  /**
   * @return string[]
   */
  public function getAttributes()
  {
    return $this->attributes;
  }
  /**
   * Output only. The timestamp when the asset was created.
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
   * Output only. Asset information specific for database deployments.
   *
   * @param DatabaseDeploymentDetails $databaseDeploymentDetails
   */
  public function setDatabaseDeploymentDetails(DatabaseDeploymentDetails $databaseDeploymentDetails)
  {
    $this->databaseDeploymentDetails = $databaseDeploymentDetails;
  }
  /**
   * @return DatabaseDeploymentDetails
   */
  public function getDatabaseDeploymentDetails()
  {
    return $this->databaseDeploymentDetails;
  }
  /**
   * Output only. Asset information specific for logical databases.
   *
   * @param DatabaseDetails $databaseDetails
   */
  public function setDatabaseDetails(DatabaseDetails $databaseDetails)
  {
    $this->databaseDetails = $databaseDetails;
  }
  /**
   * @return DatabaseDetails
   */
  public function getDatabaseDetails()
  {
    return $this->databaseDetails;
  }
  /**
   * Optional. Indicates if the asset is hidden.
   *
   * @param bool $hidden
   */
  public function setHidden($hidden)
  {
    $this->hidden = $hidden;
  }
  /**
   * @return bool
   */
  public function getHidden()
  {
    return $this->hidden;
  }
  /**
   * Optional. An optional reason for marking this asset as hidden.
   *
   * @param string $hideReason
   */
  public function setHideReason($hideReason)
  {
    $this->hideReason = $hideReason;
  }
  /**
   * @return string
   */
  public function getHideReason()
  {
    return $this->hideReason;
  }
  /**
   * Output only. The timestamp when the asset was marked as hidden.
   *
   * @param string $hideTime
   */
  public function setHideTime($hideTime)
  {
    $this->hideTime = $hideTime;
  }
  /**
   * @return string
   */
  public function getHideTime()
  {
    return $this->hideTime;
  }
  /**
   * Output only. The list of insights associated with the asset.
   *
   * @param InsightList $insightList
   */
  public function setInsightList(InsightList $insightList)
  {
    $this->insightList = $insightList;
  }
  /**
   * @return InsightList
   */
  public function getInsightList()
  {
    return $this->insightList;
  }
  /**
   * Labels as key value pairs.
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
   * Output only. Asset information specific for virtual and physical machines.
   *
   * @param MachineDetails $machineDetails
   */
  public function setMachineDetails(MachineDetails $machineDetails)
  {
    $this->machineDetails = $machineDetails;
  }
  /**
   * @return MachineDetails
   */
  public function getMachineDetails()
  {
    return $this->machineDetails;
  }
  /**
   * Output only. The full name of the asset.
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
   * Output only. Performance data for the asset.
   *
   * @param AssetPerformanceData $performanceData
   */
  public function setPerformanceData(AssetPerformanceData $performanceData)
  {
    $this->performanceData = $performanceData;
  }
  /**
   * @return AssetPerformanceData
   */
  public function getPerformanceData()
  {
    return $this->performanceData;
  }
  /**
   * Output only. The list of sources contributing to the asset.
   *
   * @param string[] $sources
   */
  public function setSources($sources)
  {
    $this->sources = $sources;
  }
  /**
   * @return string[]
   */
  public function getSources()
  {
    return $this->sources;
  }
  /**
   * Output only. Server generated human readable name of the asset.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
  /**
   * Output only. The timestamp when the asset was last updated.
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
class_alias(Asset::class, 'Google_Service_MigrationCenterAPI_Asset');
