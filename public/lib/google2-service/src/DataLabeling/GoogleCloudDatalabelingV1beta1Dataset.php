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

namespace Google\Service\DataLabeling;

class GoogleCloudDatalabelingV1beta1Dataset extends \Google\Collection
{
  protected $collection_key = 'inputConfigs';
  /**
   * Output only. The names of any related resources that are blocking changes
   * to the dataset.
   *
   * @var string[]
   */
  public $blockingResources;
  /**
   * Output only. Time the dataset is created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The number of data items in the dataset.
   *
   * @var string
   */
  public $dataItemCount;
  /**
   * Optional. User-provided description of the annotation specification set.
   * The description can be up to 10000 characters long.
   *
   * @var string
   */
  public $description;
  /**
   * Required. The display name of the dataset. Maximum of 64 characters.
   *
   * @var string
   */
  public $displayName;
  protected $inputConfigsType = GoogleCloudDatalabelingV1beta1InputConfig::class;
  protected $inputConfigsDataType = 'array';
  /**
   * Last time that the Dataset is migrated to AI Platform V2. If any of the
   * AnnotatedDataset is migrated, the last_migration_time in Dataset is also
   * updated.
   *
   * @var string
   */
  public $lastMigrateTime;
  /**
   * Output only. Dataset resource name, format is:
   * projects/{project_id}/datasets/{dataset_id}
   *
   * @var string
   */
  public $name;

  /**
   * Output only. The names of any related resources that are blocking changes
   * to the dataset.
   *
   * @param string[] $blockingResources
   */
  public function setBlockingResources($blockingResources)
  {
    $this->blockingResources = $blockingResources;
  }
  /**
   * @return string[]
   */
  public function getBlockingResources()
  {
    return $this->blockingResources;
  }
  /**
   * Output only. Time the dataset is created.
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
   * Output only. The number of data items in the dataset.
   *
   * @param string $dataItemCount
   */
  public function setDataItemCount($dataItemCount)
  {
    $this->dataItemCount = $dataItemCount;
  }
  /**
   * @return string
   */
  public function getDataItemCount()
  {
    return $this->dataItemCount;
  }
  /**
   * Optional. User-provided description of the annotation specification set.
   * The description can be up to 10000 characters long.
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
   * Required. The display name of the dataset. Maximum of 64 characters.
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
   * Output only. This is populated with the original input configs where
   * ImportData is called. It is available only after the clients import data to
   * this dataset.
   *
   * @param GoogleCloudDatalabelingV1beta1InputConfig[] $inputConfigs
   */
  public function setInputConfigs($inputConfigs)
  {
    $this->inputConfigs = $inputConfigs;
  }
  /**
   * @return GoogleCloudDatalabelingV1beta1InputConfig[]
   */
  public function getInputConfigs()
  {
    return $this->inputConfigs;
  }
  /**
   * Last time that the Dataset is migrated to AI Platform V2. If any of the
   * AnnotatedDataset is migrated, the last_migration_time in Dataset is also
   * updated.
   *
   * @param string $lastMigrateTime
   */
  public function setLastMigrateTime($lastMigrateTime)
  {
    $this->lastMigrateTime = $lastMigrateTime;
  }
  /**
   * @return string
   */
  public function getLastMigrateTime()
  {
    return $this->lastMigrateTime;
  }
  /**
   * Output only. Dataset resource name, format is:
   * projects/{project_id}/datasets/{dataset_id}
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
class_alias(GoogleCloudDatalabelingV1beta1Dataset::class, 'Google_Service_DataLabeling_GoogleCloudDatalabelingV1beta1Dataset');
