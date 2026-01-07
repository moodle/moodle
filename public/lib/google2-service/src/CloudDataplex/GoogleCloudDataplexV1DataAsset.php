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

class GoogleCloudDataplexV1DataAsset extends \Google\Model
{
  protected $accessGroupConfigsType = GoogleCloudDataplexV1DataAssetAccessGroupConfig::class;
  protected $accessGroupConfigsDataType = 'map';
  /**
   * Output only. The time at which the Data Asset was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * This checksum is computed by the server based on the value of other fields,
   * and may be sent on update and delete requests to ensure the client has an
   * up-to-date value before proceeding.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. User-defined labels for the Data Asset.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. Resource name of the Data Asset. Format: projects/{project_id_o
   * r_number}/locations/{location_id}/dataProducts/{data_product_id}/dataAssets
   * /{data_asset_id}
   *
   * @var string
   */
  public $name;
  /**
   * Required. Immutable. Full resource name of the cloud resource represented
   * by the Data Asset. This must follow https://cloud.google.com/iam/docs/full-
   * resource-names. Example: //bigquery.googleapis.com/projects/my_project_123/
   * datasets/dataset_456/tables/table_789 Only BigQuery tables and datasets are
   * currently supported. Data Asset creator must have getIamPolicy and
   * setIamPolicy permissions on the resource. Data Asset creator must also have
   * resource specific get permission, for instance, bigquery.tables.get for
   * BigQuery tables.
   *
   * @var string
   */
  public $resource;
  /**
   * Output only. System generated globally unique ID for the Data Asset. This
   * ID will be different if the Data Asset is deleted and re-created with the
   * same name.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. The time at which the Data Asset was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. Access groups configurations for this Data Asset. The key is
   * DataProduct.AccessGroup.id and the value is AccessGroupConfig. Example:
   * key: "analyst" value: { AccessGroupConfig : { iam_roles :
   * "roles/bigquery.dataViewer" } } Currently, at most one IAM role is allowed
   * per access group. For providing multiple predefined IAM roles, wrap them in
   * a custom IAM role as per https://cloud.google.com/iam/docs/creating-custom-
   * roles.
   *
   * @param GoogleCloudDataplexV1DataAssetAccessGroupConfig[] $accessGroupConfigs
   */
  public function setAccessGroupConfigs($accessGroupConfigs)
  {
    $this->accessGroupConfigs = $accessGroupConfigs;
  }
  /**
   * @return GoogleCloudDataplexV1DataAssetAccessGroupConfig[]
   */
  public function getAccessGroupConfigs()
  {
    return $this->accessGroupConfigs;
  }
  /**
   * Output only. The time at which the Data Asset was created.
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
   * This checksum is computed by the server based on the value of other fields,
   * and may be sent on update and delete requests to ensure the client has an
   * up-to-date value before proceeding.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Optional. User-defined labels for the Data Asset.
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
   * Identifier. Resource name of the Data Asset. Format: projects/{project_id_o
   * r_number}/locations/{location_id}/dataProducts/{data_product_id}/dataAssets
   * /{data_asset_id}
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
   * Required. Immutable. Full resource name of the cloud resource represented
   * by the Data Asset. This must follow https://cloud.google.com/iam/docs/full-
   * resource-names. Example: //bigquery.googleapis.com/projects/my_project_123/
   * datasets/dataset_456/tables/table_789 Only BigQuery tables and datasets are
   * currently supported. Data Asset creator must have getIamPolicy and
   * setIamPolicy permissions on the resource. Data Asset creator must also have
   * resource specific get permission, for instance, bigquery.tables.get for
   * BigQuery tables.
   *
   * @param string $resource
   */
  public function setResource($resource)
  {
    $this->resource = $resource;
  }
  /**
   * @return string
   */
  public function getResource()
  {
    return $this->resource;
  }
  /**
   * Output only. System generated globally unique ID for the Data Asset. This
   * ID will be different if the Data Asset is deleted and re-created with the
   * same name.
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
   * Output only. The time at which the Data Asset was last updated.
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
class_alias(GoogleCloudDataplexV1DataAsset::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1DataAsset');
