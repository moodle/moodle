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

class GoogleCloudSecuritycenterV2BigQueryExport extends \Google\Model
{
  /**
   * Output only. The time at which the BigQuery export was created. This field
   * is set by the server and will be ignored if provided on export on creation.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The resource name of the Cloud KMS `CryptoKey` used to protect
   * this configuration's data, if configured during Security Command Center
   * activation.
   *
   * @var string
   */
  public $cryptoKeyName;
  /**
   * The dataset to write findings' updates to. Its format is
   * "projects/[project_id]/datasets/[bigquery_dataset_id]". BigQuery dataset
   * unique ID must contain only letters (a-z, A-Z), numbers (0-9), or
   * underscores (_).
   *
   * @var string
   */
  public $dataset;
  /**
   * The description of the export (max of 1024 characters).
   *
   * @var string
   */
  public $description;
  /**
   * Expression that defines the filter to apply across create/update events of
   * findings. The expression is a list of zero or more restrictions combined
   * via logical operators `AND` and `OR`. Parentheses are supported, and `OR`
   * has higher precedence than `AND`. Restrictions have the form ` ` and may
   * have a `-` character in front of them to indicate negation. The fields map
   * to those defined in the corresponding resource. The supported operators
   * are: * `=` for all value types. * `>`, `<`, `>=`, `<=` for integer values.
   * * `:`, meaning substring matching, for strings. The supported value types
   * are: * string literals in quotes. * integer literals without quotes. *
   * boolean literals `true` and `false` without quotes.
   *
   * @var string
   */
  public $filter;
  /**
   * Output only. Email address of the user who last edited the BigQuery export.
   * This field is set by the server and will be ignored if provided on export
   * creation or update.
   *
   * @var string
   */
  public $mostRecentEditor;
  /**
   * Identifier. The relative resource name of this export. See:
   * https://cloud.google.com/apis/design/resource_names#relative_resource_name.
   * The following list shows some examples: + `organizations/{organization_id}/
   * locations/{location_id}/bigQueryExports/{export_id}` +
   * `folders/{folder_id}/locations/{location_id}/bigQueryExports/{export_id}` +
   * `projects/{project_id}/locations/{location_id}/bigQueryExports/{export_id}`
   * This field is provided in responses, and is ignored when provided in create
   * requests.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The service account that needs permission to create table and
   * upload data to the BigQuery dataset.
   *
   * @var string
   */
  public $principal;
  /**
   * Output only. The most recent time at which the BigQuery export was updated.
   * This field is set by the server and will be ignored if provided on export
   * creation or update.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The time at which the BigQuery export was created. This field
   * is set by the server and will be ignored if provided on export on creation.
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
   * Output only. The resource name of the Cloud KMS `CryptoKey` used to protect
   * this configuration's data, if configured during Security Command Center
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
   * The dataset to write findings' updates to. Its format is
   * "projects/[project_id]/datasets/[bigquery_dataset_id]". BigQuery dataset
   * unique ID must contain only letters (a-z, A-Z), numbers (0-9), or
   * underscores (_).
   *
   * @param string $dataset
   */
  public function setDataset($dataset)
  {
    $this->dataset = $dataset;
  }
  /**
   * @return string
   */
  public function getDataset()
  {
    return $this->dataset;
  }
  /**
   * The description of the export (max of 1024 characters).
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
   * Expression that defines the filter to apply across create/update events of
   * findings. The expression is a list of zero or more restrictions combined
   * via logical operators `AND` and `OR`. Parentheses are supported, and `OR`
   * has higher precedence than `AND`. Restrictions have the form ` ` and may
   * have a `-` character in front of them to indicate negation. The fields map
   * to those defined in the corresponding resource. The supported operators
   * are: * `=` for all value types. * `>`, `<`, `>=`, `<=` for integer values.
   * * `:`, meaning substring matching, for strings. The supported value types
   * are: * string literals in quotes. * integer literals without quotes. *
   * boolean literals `true` and `false` without quotes.
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
   * Output only. Email address of the user who last edited the BigQuery export.
   * This field is set by the server and will be ignored if provided on export
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
   * Identifier. The relative resource name of this export. See:
   * https://cloud.google.com/apis/design/resource_names#relative_resource_name.
   * The following list shows some examples: + `organizations/{organization_id}/
   * locations/{location_id}/bigQueryExports/{export_id}` +
   * `folders/{folder_id}/locations/{location_id}/bigQueryExports/{export_id}` +
   * `projects/{project_id}/locations/{location_id}/bigQueryExports/{export_id}`
   * This field is provided in responses, and is ignored when provided in create
   * requests.
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
   * Output only. The service account that needs permission to create table and
   * upload data to the BigQuery dataset.
   *
   * @param string $principal
   */
  public function setPrincipal($principal)
  {
    $this->principal = $principal;
  }
  /**
   * @return string
   */
  public function getPrincipal()
  {
    return $this->principal;
  }
  /**
   * Output only. The most recent time at which the BigQuery export was updated.
   * This field is set by the server and will be ignored if provided on export
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
class_alias(GoogleCloudSecuritycenterV2BigQueryExport::class, 'Google_Service_SecurityCommandCenter_GoogleCloudSecuritycenterV2BigQueryExport');
