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

class GoogleCloudDataplexV1Lake extends \Google\Model
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
  protected $assetStatusType = GoogleCloudDataplexV1AssetStatus::class;
  protected $assetStatusDataType = '';
  /**
   * Output only. The time when the lake was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Description of the lake.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. User friendly display name.
   *
   * @var string
   */
  public $displayName;
  /**
   * Optional. User-defined labels for the lake.
   *
   * @var string[]
   */
  public $labels;
  protected $metastoreType = GoogleCloudDataplexV1LakeMetastore::class;
  protected $metastoreDataType = '';
  protected $metastoreStatusType = GoogleCloudDataplexV1LakeMetastoreStatus::class;
  protected $metastoreStatusDataType = '';
  /**
   * Output only. The relative resource name of the lake, of the form:
   * projects/{project_number}/locations/{location_id}/lakes/{lake_id}.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Service account associated with this lake. This service
   * account must be authorized to access or operate on resources managed by the
   * lake.
   *
   * @var string
   */
  public $serviceAccount;
  /**
   * Output only. Current state of the lake.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. System generated globally unique ID for the lake. This ID will
   * be different if the lake is deleted and re-created with the same name.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. The time when the lake was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Aggregated status of the underlying assets of the lake.
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
   * Output only. The time when the lake was created.
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
   * Optional. Description of the lake.
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
   * Optional. User-defined labels for the lake.
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
   * Optional. Settings to manage lake and Dataproc Metastore service instance
   * association.
   *
   * @param GoogleCloudDataplexV1LakeMetastore $metastore
   */
  public function setMetastore(GoogleCloudDataplexV1LakeMetastore $metastore)
  {
    $this->metastore = $metastore;
  }
  /**
   * @return GoogleCloudDataplexV1LakeMetastore
   */
  public function getMetastore()
  {
    return $this->metastore;
  }
  /**
   * Output only. Metastore status of the lake.
   *
   * @param GoogleCloudDataplexV1LakeMetastoreStatus $metastoreStatus
   */
  public function setMetastoreStatus(GoogleCloudDataplexV1LakeMetastoreStatus $metastoreStatus)
  {
    $this->metastoreStatus = $metastoreStatus;
  }
  /**
   * @return GoogleCloudDataplexV1LakeMetastoreStatus
   */
  public function getMetastoreStatus()
  {
    return $this->metastoreStatus;
  }
  /**
   * Output only. The relative resource name of the lake, of the form:
   * projects/{project_number}/locations/{location_id}/lakes/{lake_id}.
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
   * Output only. Service account associated with this lake. This service
   * account must be authorized to access or operate on resources managed by the
   * lake.
   *
   * @param string $serviceAccount
   */
  public function setServiceAccount($serviceAccount)
  {
    $this->serviceAccount = $serviceAccount;
  }
  /**
   * @return string
   */
  public function getServiceAccount()
  {
    return $this->serviceAccount;
  }
  /**
   * Output only. Current state of the lake.
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
   * Output only. System generated globally unique ID for the lake. This ID will
   * be different if the lake is deleted and re-created with the same name.
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
   * Output only. The time when the lake was last updated.
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
class_alias(GoogleCloudDataplexV1Lake::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1Lake');
